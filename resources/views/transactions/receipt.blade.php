<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=220, initial-scale=1.0">
    <title>Receipt - {{ $transaction->transaction_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --receipt-width-mm: 58;
            --receipt-width-px: 220; /* 58mm x 3.78 ~ 219px */
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: #fff !important;
                color: #000 !important;
                font-size: 11px;
            }
            .no-print { display: none !important; }
            .receipt-container {
                width: var(--receipt-width-mm)mm !important;
                max-width: var(--receipt-width-mm)mm !important;
                min-width: var(--receipt-width-mm)mm !important;
                margin: 0;
                padding: 2mm 2mm 0 2mm;
                box-shadow: none;
                font-family: 'Courier New', Courier, monospace;
            }
            .receipt-header,
            .receipt-footer {
                text-align: center;
                margin: 0 0 2mm 0;
            }
            .receipt-section {
                margin-bottom: 1mm;
            }
            .receipt-line {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5mm;
                white-space: nowrap;
            }
            .receipt-divider {
                border-top: 1px dashed #000;
                margin: 1mm 0;
            }
        }
        /* Preview/Mobile styling */
        body {
            background: #f3f4f6;
        }
        .receipt-container {
            background: #fff;
            width: var(--receipt-width-px)px;
            max-width: var(--receipt-width-px)px;
            min-width: var(--receipt-width-px)px;
            margin: 0 auto;
            padding: 12px 8px 0 8px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.2;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .receipt-header,
        .receipt-footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .receipt-section {
            margin-bottom: 7px;
        }
        .receipt-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            white-space: nowrap;
        }
        .receipt-divider {
            border-top: 1px dashed #000;
            margin: 7px 0;
        }
        /* Prevent text wrapping in lines */
        .receipt-line > * {
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="p-2">
    <div class="max-w-sm mx-auto">
        <!-- Print Button -->
        <div class="no-print mb-3 text-center">
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
        <div class="receipt-container">
            <!-- Header -->
            <div class="receipt-header">
                <h1 class="text-base font-bold">TAKONATION</h1>
                <div class="text-xs">Jl. Raya Ciomas/Pagelaran</div>
                <div class="text-xs">Telp: +62 812-8425-4724</div>
            </div>

            <!-- Transaction Info -->
            <div class="receipt-section text-xs">
                <div class="receipt-line">
                    <span>No:</span>
                    <span>{{ $transaction->transaction_code }}</span>
                </div>
                <div class="receipt-line">
                    <span>Tgl:</span>
                    <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="receipt-line">
                    <span>Bayar:</span>
                    <span>{{ $transaction->payment_method_label }}</span>
                </div>
            </div>

            <!-- Items -->
            <div class="receipt-divider"></div>
            <div class="receipt-section">
                @foreach($transaction->items as $item)
                    <div>
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
            <div class="receipt-section text-xs">
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
            <div class="receipt-footer text-xs">
                <div>Terima kasih atas kunjungan Anda!</div>
                <div style="margin-bottom: 10px">Selamat menikmati takoyaki kami</div>
                <div>{{ now()->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>