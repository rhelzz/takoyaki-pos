<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\TaxTemplate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DiscountTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function index()
    {
        $categories = Category::active()->with(['products' => function($query) {
            $query->active();
        }])->get();
        
        $products = Product::active()->with('category')->get();
        $discountTemplates = DiscountTemplate::active()->orderBy('percentage')->get();
        $taxTemplates = TaxTemplate::active()->orderBy('percentage')->get();
        
        return view('cashier.index', compact(
            'categories',
            'products',
            'discountTemplates',
            'taxTemplates'
        ));
    }

    public function getProduct($id)
    {
        $product = Product::active()->with('category')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'selling_price' => $product->selling_price,
                'cost_price' => $product->cost_price,
                'quantity_per_serving' => $product->quantity_per_serving,
                'image_url' => $product->image_url,
                'category' => $product->category->name
            ]
        ]);
    }

    public function processTransaction(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:25',
            'tax_percentage' => 'nullable|numeric|in:0,11',
            'payment_method' => 'required|in:cash,card,digital'
        ]);

        try {
            DB::beginTransaction();

            $cart = $request->cart;
            $subtotal = 0;
            $totalCost = 0;
            $items = [];

            // Validasi dan hitung subtotal, total cost
            foreach ($cart as $item) {
                $product = Product::active()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                
                $totalPrice = $product->selling_price * $quantity;
                $itemCost = $product->cost_price * $quantity;
                
                $subtotal += $totalPrice;
                $totalCost += $itemCost;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $product->cost_price,
                    'unit_price' => $product->selling_price,
                    'total_cost' => $itemCost,
                    'total_price' => $totalPrice
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
            $grossProfit = $subtotal - $totalCost - $discountAmount; // Keuntungan kotor (sebelum pajak)
            $netProfit = $grossProfit; // Keuntungan bersih (pajak tidak mengurangi profit karena dibayar customer)

            // Buat transaksi
            $transaction = Transaction::create([
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
                'payment_method' => $request->payment_method
            ]);

            // Buat item transaksi
            foreach ($items as $item) {
                $transaction->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction' => [
                    'code' => $transaction->transaction_code,
                    'total_amount' => $totalAmount,
                    'profit' => $netProfit,
                    'items_count' => count($items),
                    'payment_method' => $transaction->payment_method
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionReceipt($transactionCode)
    {
        $transaction = Transaction::where('transaction_code', $transactionCode)
            ->with(['items.product', 'user'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }
}
