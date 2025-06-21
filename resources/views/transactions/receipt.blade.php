<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction->transaction_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                font-size: 12px; 
                color: black !important;
            }
            .no-print { display: none !important; }
            .receipt-container { 
                width: 80mm; 
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-sm mx-auto">
        <!-- Print Button -->
        <div class="no-print mb-4 text-center">
            <button onclick="window.print()" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mr-2">
                <i class="fas fa-print mr-2"></i>Print Receipt
            </button>
            <button onclick="window.close()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>

        <!-- Receipt -->
        <div class="receipt-container bg-white p-6 rounded-lg shadow-lg font-mono text-sm">
            <!-- Header -->
            <div class="text-center mb-4 border-b border-dashed border-gray-400 pb-4">
                <h1 class="text-lg font-bold">TAKOYAKI POS</h1>
                <p class="text-xs">Jl. Takoyaki No. 123</p>
                <p class="text-xs">Telp: (021) 1234-5678</p>
            </div>

            <!-- Transaction Info -->
            <div class="mb-4 text-xs">
                <div class="flex justify-between">
                    <span>No. Transaksi:</span>
                    <span>{{ $transaction->transaction_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kasir:</span>
                    <span>{{ $transaction->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pembayaran:</span>
                    <span>{{ $transaction->payment_method_label }}</span>
                </div>
            </div>

            <!-- Items -->
            <div class="border-t border-dashed border-gray-400 pt-2 mb-4">
                @foreach($transaction->items as $item)
                    <div class="mb-2">
                        <div class="flex justify-between">
                            <span class="flex-1">{{ $item->product->name }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            <span>{{ number_format($item->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="border-t border-dashed border-gray-400 pt-2 mb-4">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($transaction->hasDiscount())
                    <div class="flex justify-between">
                        <span>Diskon ({{ $transaction->discount_percentage }}%):</span>
                        <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($transaction->hasTax())
                    <div class="flex justify-between">
                        <span>Pajak ({{ $transaction->tax_percentage }}%):</span>
                        <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="border-t border-solid border-gray-400 mt-2 pt-2">
                    <div class="flex justify-between font-bold text-base">
                        <span>TOTAL:</span>
                        <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs border-t border-dashed border-gray-400 pt-4">
                <p>Terima kasih atas kunjungan Anda!</p>
                <p>Selamat menikmati takoyaki kami</p>
                <p class="mt-2">{{ config('app.name') }}</p>
                <p>{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>