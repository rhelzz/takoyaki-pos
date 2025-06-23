<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }
        
        // Sort filter
        switch ($request->get('sort', 'newest')) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(5);
        
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('id', 'asc')->get();
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

    /**
     * Delete product with proper JSON response for AJAX
     */
    public function destroy(Product $product)
    {
        try {
            // Cek apakah produk pernah digunakan dalam transaksi
            if ($product->transactionItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak dapat dihapus karena sudah pernah digunakan dalam transaksi',
                    'code' => 'PRODUCT_IN_USE'
                ], 422);
            }

            // Hapus gambar jika ada
            if ($product->image) {
                try {
                    Storage::disk('public')->delete($product->image);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete product image: ' . $e->getMessage());
                    // Continue with product deletion even if image deletion fails
                }
            }
            
            $productName = $product->name;
            $product->delete();
            
            // Log successful deletion
            Log::info("Product '{$productName}' deleted successfully", [
                'product_id' => $product->id,
                'deleted_by' => Auth::user()->name ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus',
                'product_name' => $productName
            ]);
            
        } catch (\Exception $e) {
            Log::error('Product deletion error', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus produk. Silakan coba lagi.',
                'code' => 'DELETION_FAILED'
            ], 500);
        }
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
        $deletedCount = 0;
        $errors = [];
        
        foreach ($products as $product) {
            if (!$product->transactionItems()->exists()) {
                try {
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $product->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus {$product->name}";
                    Log::error('Bulk delete error', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                $errors[] = "{$product->name} tidak dapat dihapus (sudah digunakan dalam transaksi)";
            }
        }

        $message = $deletedCount > 0 ? "{$deletedCount} produk berhasil dihapus" : "Tidak ada produk yang dapat dihapus";
        
        if (count($errors) > 0) {
            $message .= ". Beberapa produk tidak dapat dihapus: " . implode(', ', $errors);
        }

        return redirect()->route('products.index')->with('success', $message);
    }
}
