<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MissingProduct;
use App\Models\NewProduct;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::when($search, function ($query, $search) {
                return $query->where('asin', 'like', "%$search%")
                             ->orWhere('ean', 'like', "%$search%");
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('products.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function missing(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $query = MissingProduct::query();

        if ($start) {
            $query->whereDate('created_at', '>=', Carbon::parse($start)->startOfDay());
        }

        if ($end) {
            $query->whereDate('created_at', '<=', Carbon::parse($end)->endOfDay());
        }

        $missing = $query->orderByDesc('created_at')->paginate(10)->appends([
            'start_date' => $start,
            'end_date' => $end,
        ]);

        return view('products.missing', [
            'missing' => $missing,
            'from' => $start,
            'to' => $end,
        ]);
    }

    public function newProducts()
    {
        $newProducts = NewProduct::orderByDesc('created_at')->paginate(10);

        return view('products.new', [
            'newProducts' => $newProducts,
        ]);
    }

    public function transferToInventory()
    {
        $newProducts = NewProduct::all();

        foreach ($newProducts as $item) {
            Product::create([
                'asin'     => $item->asin,
                'ean'      => $item->ean,
                'stock'    => $item->stock,
                'price'    => $item->price,
                'discount' => $item->discount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        NewProduct::truncate();

        return back()->with('success', '✅ New products transferred to inventory.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $product->stock = $request->input('stock');
        $product->price = $request->input('price');
        $product->discount = $request->input('discount');
        $product->updated_at = now();
        $product->save();

        return back()->with('success', 'Product updated successfully.');
    }

    public function exportMissing(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $query = MissingProduct::query();

        if ($start) {
            $query->whereDate('created_at', '>=', Carbon::parse($start)->startOfDay());
        }

        if ($end) {
            $query->whereDate('created_at', '<=', Carbon::parse($end)->endOfDay());
        }

        $missing = $query->orderByDesc('created_at')->get();

        if ($missing->isEmpty()) {
            return back()->with('error', 'No missing products to export.');
        }

        $response = new StreamedResponse(function () use ($missing) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ASIN', 'EAN', 'Stock', 'Detected At']);

            foreach ($missing as $item) {
                fputcsv($handle, [
                    $item->asin ?? '',
                    $item->ean ?? '',
                    $item->stock ?? 0,
                    $item->created_at->toDateTimeString(),
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="missing_products.csv"');

        return $response;
    }

    public function exportOutOfStock()
    {
        $products = Product::where('stock', '<=', 5)->orderBy('stock')->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'No out-of-stock products to export.');
        }

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ASIN', 'EAN', 'Stock', 'Price', 'Discount', 'Last Updated']);

            foreach ($products as $item) {
                fputcsv($handle, [
                    $item->asin ?? '',
                    $item->ean ?? '',
                    $item->stock ?? 0,
                    $item->price ?? 0,
                    $item->discount ?? 0,
                    $item->updated_at?->toDateTimeString() ?? '',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="out_of_stock_products.csv"');

        return $response;
    }

    public function exportInStock()
    {
        $products = Product::where('stock', '>=', 6)->orderBy('stock', 'desc')->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'No in-stock products to export.');
        }

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ASIN', 'EAN', 'Stock', 'Price', 'Discount', 'Last Updated']);

            foreach ($products as $item) {
                fputcsv($handle, [
                    $item->asin ?? '',
                    $item->ean ?? '',
                    $item->stock ?? 0,
                    $item->price ?? 0,
                    $item->discount ?? 0,
                    $item->updated_at?->toDateTimeString() ?? '',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="in_stock_products.csv"');

        return $response;
    }

    // === Export New Products with Full Data ===
    public function exportNewProducts()
    {
        $products = NewProduct::orderByDesc('created_at')->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'No new products to export.');
        }

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ASIN', 'EAN', 'Stock', 'Price', 'Discount', 'Date Added']);

            foreach ($products as $item) {
                fputcsv($handle, [
                    $item->asin ?? '',
                    $item->ean ?? '',
                    $item->stock ?? 0,
                    $item->price ?? 0,
                    $item->discount ?? 0,
                    $item->created_at?->toDateTimeString() ?? '',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="new_products.csv"');

        return $response;
    }
}
