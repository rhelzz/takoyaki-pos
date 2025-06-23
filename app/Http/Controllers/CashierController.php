<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\TaxTemplate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Models\DiscountTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function index()
    {
        try {
            // Debug log
            Log::info('Cashier page loading...');

            // Ambil categories - PERBAIKAN: hapus scope active() jika belum ada
            $categories = Category::with(['products' => function($query) {
                $query->where('is_active', true);
            }])->get();
            
            // Ambil products aktif - PERBAIKAN: gunakan where langsung
            $products = Product::where('is_active', true)->with('category')->get();
            
            // Debug log
            Log::info('Products loaded', [
                'total_categories' => $categories->count(),
                'total_products' => $products->count(),
                'products_list' => $products->pluck('name', 'id')->toArray()
            ]);

            // Jika tidak ada products aktif, ambil semua
            if ($products->isEmpty()) {
                Log::warning('No active products found, getting all products');
                $products = Product::with('category')->get();
                
                // Auto activate jika ada products
                if ($products->count() > 0) {
                    Product::query()->update(['is_active' => true]);
                    $products = Product::where('is_active', true)->with('category')->get();
                    Log::info('Auto-activated all products: ' . $products->count());
                }
            }
            
            // Default templates karena mungkin tabel belum ada
            $discountTemplates = collect([
                (object)['id' => 1, 'percentage' => 5, 'display_name' => 'Diskon 5%'],
                (object)['id' => 2, 'percentage' => 10, 'display_name' => 'Diskon 10%'],
                (object)['id' => 3, 'percentage' => 15, 'display_name' => 'Diskon 15%'],
            ]);
            
            $taxTemplates = collect([
                (object)['id' => 1, 'percentage' => 0, 'display_name' => 'Tanpa Pajak'],
                (object)['id' => 2, 'percentage' => 11, 'display_name' => 'PPN 11%'],
            ]);
            
            // Coba ambil dari database jika ada
            try {
                $dbDiscounts = DiscountTemplate::all();
                if ($dbDiscounts->count() > 0) {
                    $discountTemplates = $dbDiscounts;
                }
            } catch (\Exception $e) {
                Log::info('DiscountTemplate table not found, using default');
            }
            
            try {
                $dbTaxes = TaxTemplate::all();
                if ($dbTaxes->count() > 0) {
                    $taxTemplates = $dbTaxes;
                }
            } catch (\Exception $e) {
                Log::info('TaxTemplate table not found, using default');
            }
            
            return view('cashier.index', compact(
                'categories',
                'products',
                'discountTemplates',
                'taxTemplates'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error loading cashier page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan saat memuat halaman kasir: ' . $e->getMessage());
        }
    }

    public function getProduct($id)
    {
        try {
            $product = Product::where('is_active', true)->with('category')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'selling_price' => $product->selling_price,
                    'cost_price' => $product->cost_price,
                    'quantity_per_serving' => $product->quantity_per_serving,
                    'image_url' => $product->image_url,
                    'category' => $product->category->name ?? 'No Category'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function processTransaction(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'required|in:cash,card,digital'
        ]);

        try {
            DB::beginTransaction();

            // Generate transaction code
            $transactionCode = 'TXN-' . now()->format('Ymd') . '-' . str_pad(
                Transaction::whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $cart = $request->cart;
            $subtotal = 0;
            $totalCost = 0;
            $items = [];

            // Validasi dan hitung subtotal, total cost
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['price'];
                
                $totalPrice = $unitPrice * $quantity;
                $itemCost = ($product->cost_price ?? 0) * $quantity;
                
                $subtotal += $totalPrice;
                $totalCost += $itemCost;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $product->cost_price ?? 0,
                    'unit_price' => $unitPrice,
                    'total_cost' => $itemCost,
                    'total_price' => $totalPrice,
                    'profit' => $totalPrice - $itemCost
                ];
            }

            // Hitung diskon
            $discountPercentage = (float) ($request->discount_percentage ?? 0);
            $discountAmount = ($subtotal * $discountPercentage) / 100;

            // Hitung pajak (dari subtotal setelah diskon)
            $taxPercentage = (float) ($request->tax_percentage ?? 0);
            $afterDiscount = $subtotal - $discountAmount;
            $taxAmount = ($afterDiscount * $taxPercentage) / 100;

            // Hitung total akhir
            $totalAmount = $afterDiscount + $taxAmount;
            
            // Hitung keuntungan
            $grossProfit = $subtotal - $totalCost;
            $netProfit = $grossProfit - $discountAmount; // Profit setelah diskon

            // Buat transaksi
            $transaction = Transaction::create([
                'transaction_code' => $transactionCode,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'total_cost' => $totalCost,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'gross_profit' => $grossProfit,
                'net_profit' => $netProfit,
                'profit_margin' => $totalAmount > 0 ? ($netProfit / $totalAmount) * 100 : 0,
                'payment_method' => $request->payment_method
            ]);

            // Buat item transaksi
            foreach ($items as $item) {
                TransactionItem::create(array_merge($item, [
                    'transaction_id' => $transaction->id
                ]));
            }

            DB::commit();

            Log::info('Transaction processed successfully', [
                'transaction_code' => $transactionCode,
                'total_amount' => $totalAmount,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction' => [
                    'code' => $transactionCode,
                    'total_amount' => $totalAmount,
                    'profit' => $netProfit,
                    'items_count' => count($items),
                    'payment_method' => $request->payment_method
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Transaction processing failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionReceipt($transactionCode)
    {
        try {
            $transaction = Transaction::where('transaction_code', $transactionCode)
                ->with(['items.product', 'user'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
    }
}
