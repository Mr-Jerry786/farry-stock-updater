<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MissingProduct;
use App\Models\NewProduct;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FileUploadController extends Controller
{
    public function showForm()
    {
        return view('upload');
    }

    public function handleUpload(Request $request)
    {
        $request->validate([
            'file_type'   => 'required|in:inventory,stock',
            'stock_file'  => 'required|file|mimes:xlsx,csv,txt',
        ]);

        $fileType = $request->input('file_type');
        $file = $request->file('stock_file');

        try {
            $rows = Excel::toArray([], $file)[0]; // Load first sheet
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to read file: ' . $e->getMessage());
        }

        // Remove header lines
        $data = array_filter($rows, fn($row) => !Str::contains(strtolower(implode(',', $row)), 'header'));

        $added = 0;
        $updated = 0;
        $missing = 0;
        $newDetected = 0;

        $inventoryEmpty = Product::count() === 0;

        foreach ($data as $row) {
            $asin = trim($row[0] ?? '');
            $ean  = trim($row[1] ?? '');

            if (!$asin && !$ean) continue;

            $price = isset($row[2]) ? floatval($row[2]) : 0.0;

            // Clean discount: remove pound sign, set to 35.00 if 0
            $rawDiscount = $row[3] ?? '0';
            $cleanDiscount = str_replace('£', '', $rawDiscount);
            $discount = is_numeric($cleanDiscount) ? floatval($cleanDiscount) : 0.0;
            if ($discount == 0.0) {
                $discount = 35.0;
            }

            $stock = isset($row[4]) ? (int) $row[4] : 0;

            if ($fileType === 'inventory') {
                $exists = Product::where('asin', $asin)->orWhere('ean', $ean)->exists();

                if ($inventoryEmpty || !$exists) {
                    if ($inventoryEmpty) {
                        // Insert directly into inventory
                        Product::create([
                            'asin'     => $asin,
                            'ean'      => $ean,
                            'stock'    => $stock,
                            'price'    => $price,
                            'discount' => $discount,
                        ]);
                        $added++;
                    } else {
                        // Insert into new_products
                        NewProduct::firstOrCreate(
                            ['asin' => $asin, 'ean' => $ean],
                            [
                                'stock'    => $stock,
                                'price'    => $price,
                                'discount' => $discount,
                                'created_at' => now(),
                            ]
                        );
                        $newDetected++;
                    }
                }
            }

            if ($fileType === 'stock') {
                $product = Product::where('asin', $asin)->orWhere('ean', $ean)->first();

                if ($product) {
                    $product->stock = $stock;
                    $product->price = $price;
                    $product->discount = $discount;
                    $product->updated_at = now();
                    $product->save();
                    $updated++;
                } else {
                    MissingProduct::firstOrCreate(
                        ['asin' => $asin ?: null, 'ean' => $ean ?: null],
                        [
                            'stock'      => $stock,
                            'price'      => $price,
                            'discount'   => $discount,
                            'created_at' => now(),
                        ]
                    );
                    $missing++;
                }
            }
        }

        $msg = "✅ Upload complete: $added added, $updated updated, $missing missing, $newDetected new.";

        Log::info("File upload [$fileType]: $msg");

        return redirect()->route('products.index')->with('success', $msg);
    }
}
