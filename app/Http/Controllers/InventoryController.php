<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AmazonInventory;
use App\Models\MissingInventory;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AmazonInventory::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('asin', 'like', "%{$search}%")
                  ->orWhere('ean', 'like', "%{$search}%");
            });
        }

        $inventory = $query->paginate(20)->appends($request->only('search'));

        return view('inventory.index', compact('inventory'));
    }

    public function missing(Request $request)
    {
        $query = MissingInventory::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('asin', 'like', "%{$search}%")
                  ->orWhere('ean', 'like', "%{$search}%");
            });
        }

        $missingInventory = $query->latest()->paginate(20)->appends($request->only('search'));

        return view('inventory.missing', compact('missingInventory'));
    }

    public function showUploadForm()
    {
        return view('inventory.upload');
    }

    public function handleUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'type' => 'required|in:inventory,stock',
        ]);

        $uploadedFile = $request->file('file');
        $extension = $uploadedFile->getClientOriginalExtension();
        $path = $uploadedFile->store('uploads');
        $fullPath = storage_path("app/{$path}");

        if (!file_exists($fullPath)) {
            return back()->with('error', 'Uploaded file not found.');
        }

        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                $spreadsheet = IOFactory::load($fullPath);
                $sheet = $spreadsheet->getActiveSheet()->toArray();
            } else {
                $sheet = array_map('str_getcsv', file($fullPath));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error reading file: ' . $e->getMessage());
        }

        if (empty($sheet) || !is_array($sheet[0])) {
            return back()->with('error', 'Invalid or empty file.');
        }

        $headers = array_map('strtolower', array_map('trim', array_shift($sheet)));

        $added = 0;
        $updated = 0;
        $skipped = 0;
        $missing = 0;

        foreach ($sheet as $row) {
            if (count($row) !== count($headers)) {
                $skipped++;
                continue;
            }

            $data = array_combine($headers, $row);
            $asin  = trim($data['asin'] ?? '');
            $ean   = trim($data['ean'] ?? '');
            $stock = $data['stock'] ?? 0;
            $price = $data['price'] ?? 0;

            if (!$asin && !$ean) {
                $skipped++;
                continue;
            }

            $query = AmazonInventory::query();
            if ($asin) $query->orWhere('asin', $asin);
            if ($ean)  $query->orWhere('ean', $ean);
            $existing = $query->first();

            if ($request->type === 'inventory') {
                if ($existing) {
                    $skipped++;
                    continue;
                }

                AmazonInventory::create([
                    'asin'  => $asin,
                    'ean'   => $ean,
                    'stock' => $stock,
                    'price' => $price,
                ]);
                $added++;

            } elseif ($request->type === 'stock') {
                if ($existing) {
                    $existing->update([
                        'stock' => $stock,
                        'price' => $price,
                    ]);
                    $updated++;
                } else {
                    $missingExists = MissingInventory::where(function ($q) use ($asin, $ean) {
                        if ($asin) $q->where('asin', $asin);
                        if ($ean)  $q->orWhere('ean', $ean);
                    })->first();

                    if (!$missingExists) {
                        MissingInventory::create([
                            'asin'  => $asin,
                            'ean'   => $ean,
                            'stock' => $stock,
                            'price' => $price,
                        ]);
                        $missing++;
                    }
                    $skipped++;
                }
            }
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', "Upload complete: {$added} added, {$updated} updated, {$missing} missing, {$skipped} skipped.");
    }

    public function exportInStock(): StreamedResponse
    {
        $data = AmazonInventory::where('stock', '>=', 6)->get();

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ASIN', 'EAN', 'Stock', 'Price']);
            foreach ($data as $item) {
                fputcsv($handle, [$item->asin, $item->ean, $item->stock, $item->price]);
            }
            fclose($handle);
        }, 'in_stock.csv');
    }

    public function exportOutOfStock(): StreamedResponse
    {
        $data = AmazonInventory::where('stock', '<=', 5)->get();

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ASIN', 'EAN', 'Stock', 'Price']);
            foreach ($data as $item) {
                fputcsv($handle, [$item->asin, $item->ean, $item->stock, $item->price]);
            }
            fclose($handle);
        }, 'out_of_stock.csv');
    }

    public function exportMissing(): StreamedResponse
    {
        $data = MissingInventory::latest()->get();

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ASIN', 'EAN', 'Stock', 'Price']);
            foreach ($data as $item) {
                fputcsv($handle, [$item->asin, $item->ean, $item->stock, $item->price]);
            }
            fclose($handle);
        }, 'missing_inventory.csv');
    }
}
