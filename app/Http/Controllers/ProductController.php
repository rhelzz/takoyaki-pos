<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gt:cost_price',
            'quantity_per_serving' => 'required|integer|in:1,5,10,15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'required|boolean'
        ], [
            'selling_price.gt' => 'Harga jual harus lebih besar dari harga modal'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'transactionItems.transaction']);
        
        // Statistik produk
        $stats = [
            'total_sold' => $product->transactionItems->sum('quantity'),
            'total_revenue' => $product->transactionItems->sum('total_price'),
            'total_profit' => $product->transactionItems->sum(function($item) {
                return $item->total_price - $item->total_cost;
            }),
            'transaction_count' => $product->transactionItems->count()
        ];
        
        return view('products.show', compact('product', 'stats'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gt:cost_price',
            'quantity_per_serving' => 'required|integer|in:1,5,10,15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'required|boolean'
        ], [
            'selling_price.gt' => 'Harga jual harus lebih besar dari harga modal'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        // Cek apakah produk pernah digunakan dalam transaksi
        if ($product->transactionItems()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah pernah digunakan dalam transaksi');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('products.index')->with('success', "Produk berhasil {$status}");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $products = Product::whereIn('id', $request->product_ids)->get();
        
        foreach ($products as $product) {
            if (!$product->transactionItems()->exists()) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->delete();
            }
        }

        return redirect()->route('products.index')->with('success', 'Produk yang dipilih berhasil dihapus');
    }
}
