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
                font-size: 11px; 
                color: black !important;
                margin: 0;
                padding: 0;
            }
            .no-print { display: none !important; }
            .receipt-container { 
                width: 58mm; 
                max-width: 58mm;
                margin: 0;
                padding: 2mm;
                box-shadow: none;
                font-family: 'Courier New', monospace;
            }
            .receipt-header {
                text-align: center;
                margin-bottom: 2mm;
            }
            .receipt-section {
                margin-bottom: 1mm;
            }
            .receipt-line {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5mm;
            }
            .receipt-divider {
                border-top: 1px dashed #000;
                margin: 1mm 0;
            }
        }
        
        /* Mobile/Preview styling */
        .receipt-container {
            width: 220px; /* 58mm in pixels for preview */
            max-width: 220px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
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
        <div class="receipt-container bg-white p-3 rounded-lg shadow-lg">
            <!-- Header -->
            <div class="receipt-header text-center mb-2">
                <h1 class="text-sm font-bold">TAKOYAKI POS</h1>
                <p class="text-xs">Jl. Takoyaki No. 123</p>
                <p class="text-xs">Telp: (021) 1234-5678</p>
            </div>

            <!-- Transaction Info -->
            <div class="receipt-section text-xs mb-2">
                <div class="receipt-line">
                    <span>No:</span>
                    <span>{{ $transaction->transaction_code }}</span>
                </div>
                <div class="receipt-line">
                    <span>Tgl:</span>
                    <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="receipt-line">
                    <span>Kasir:</span>
                    <span>{{ $transaction->user->name }}</span>
                </div>
                <div class="receipt-line">
                    <span>Bayar:</span>
                    <span>{{ $transaction->payment_method_label }}</span>
                </div>
            </div>

            <!-- Items -->
            <div class="receipt-divider"></div>
            <div class="receipt-section mb-2">
                @foreach($transaction->items as $item)
                    <div class="mb-1">
                        <div class="text-xs font-medium">{{ $item->product->name }}</div>
                        <div class="receipt-line text-xs">
                            <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            <span>{{ number_format($item->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="receipt-divider"></div>
            <div class="receipt-section text-xs mb-2">
                <div class="receipt-line">
                    <span>Subtotal:</span>
                    <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($transaction->hasDiscount())
                    <div class="receipt-line">
                        <span>Diskon ({{ $transaction->discount_percentage }}%):</span>
                        <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($transaction->hasTax())
                    <div class="receipt-line">
                        <span>Pajak ({{ $transaction->tax_percentage }}%):</span>
                        <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="receipt-divider"></div>
                <div class="receipt-line font-bold text-sm">
                    <span>TOTAL:</span>
                    <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
                
                <!-- Cash Payment Details -->
                @if($transaction->payment_method === 'cash')
                    <div class="receipt-divider"></div>
                    @if($transaction->customer_money)
                        <div class="receipt-line">
                            <span>Bayar:</span>
                            <span>{{ number_format($transaction->customer_money, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    @if($transaction->hasChange())
                        <div class="receipt-line font-bold">
                            <span>KEMBALIAN:</span>
                            <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Footer -->
            <div class="receipt-divider"></div>
            <div class="text-center text-xs">
                <p>Terima kasih atas kunjungan Anda!</p>
                <p>Selamat menikmati takoyaki kami</p>
                <p class="mt-1">{{ config('app.name') }}</p>
                <p>{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>