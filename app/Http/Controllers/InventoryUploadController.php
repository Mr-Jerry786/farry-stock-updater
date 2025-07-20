<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AmazonInventory;
use App\Models\MissingInventory;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class InventoryUploadController extends Controller
{
    public function showForm()
    {
        return view('inventory.upload');
    }

    public function handleUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls,txt',
            'type' => 'required|in:inventory,stock',
        ]);

        $file = $request->file('file');
        $type = $request->input('type');

        try {
            $rows = Excel::toArray([], $file)[0];
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to read file: ' . $e->getMessage());
        }

        if (count($rows) < 1) {
            return back()->with('error', 'The file is empty.');
        }

        // Detect headerless stock file (e.g., starts with UPDATE)
        if ($type === 'stock' && isset($rows[0][0]) && str_starts_with(strtoupper(trim($rows[0][0])), 'UPDATE')) {
            array_shift($rows); // Remove UPDATE line
            $header = ['ean', 'stock'];
            $hasHeader = false;
        } else {
            $header = array_map('strtolower', array_map('trim', array_shift($rows)));
            $hasHeader = true;
        }

        $added = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (!$hasHeader) {
                if (count($row) < 2) {
                    $skipped++;
                    continue;
                }

                $ean = trim($row[0]);
                $stock = (int) $row[1];
                $asin = null;
                $price = 0;
            } else {
                if (count($row) !== count($header)) {
                    $skipped++;
                    continue;
                }

                $data = array_combine($header, $row);
                $asin = trim($data['asin'] ?? '');
                $ean = trim($data['ean'] ?? '');
                $stock = isset($data['stock']) ? (int) $data['stock'] : 0;
                $price = isset($data['price']) ? (float) $data['price'] : 0;
            }

            if (!$asin && !$ean) {
                $skipped++;
                continue;
            }

            $existing = AmazonInventory::where(function ($query) use ($asin, $ean) {
                $query->where(function ($q) use ($asin) {
                    if ($asin) $q->where('asin', $asin);
                })->orWhere(function ($q) use ($ean) {
                    if ($ean) $q->where('ean', $ean);
                });
            })->first();

            if ($type === 'inventory') {
                if ($existing) {
                    $skipped++;
                    continue;
                }

                AmazonInventory::create([
                    'asin' => $asin,
                    'ean' => $ean,
                    'stock' => $stock,
                    'price' => $price,
                ]);
                $added++;
            }

            if ($type === 'stock') {
                if ($existing) {
                    $existing->update([
                        'stock' => $stock,
                        'price' => $price,
                    ]);
                    $updated++;
                } else {
                    MissingInventory::firstOrCreate(
                        ['asin' => $asin ?: null, 'ean' => $ean ?: null],
                        ['stock' => $stock, 'price' => $price]
                    );
                    $skipped++;
                }
            }
        }

        $message = "✅ Upload complete: $added added, $updated updated, $skipped skipped.";
        Log::info("Amazon Inventory Upload [$type]: $message");

        return redirect()->route('inventory.index')->with('success', $message);
    }
}
