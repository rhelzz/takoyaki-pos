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
        <div class="no-print mb-4 p-2">
            <div class="flex flex-col sm:flex-row justify-center items-center gap-2">
                <button onclick="window.print()"
                        class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-print mr-2"></i><span>Print Receipt</span>
                </button>
                <button id="print-bluetooth-btn"
                        class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bluetooth-b mr-2"></i><span>Print Bluetooth</span>
                </button>
                <button onclick="window.close()"
                        class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i><span>Tutup</span>
                </button>
            </div>
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

    <script>
        // Pass transaction data from Laravel to JavaScript
        const transactionData = @json($transaction);

        document.getElementById('print-bluetooth-btn').addEventListener('click', () => {
            printBluetooth(transactionData);
        });

        // Helper to format currency
        function formatCurrency(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        // Generate ESC/POS commands from transaction data
        function generateEscPosData(transaction) {
            const encoder = new TextEncoder();
            let commands = [];

            // Helper to add commands
            const add = (data) => {
                if (typeof data === 'string') {
                    commands.push(encoder.encode(data));
                } else {
                    commands.push(new Uint8Array(data));
                }
            };

            // ESC/POS Commands
            const INIT = [0x1B, 0x40]; // Initialize printer
            const CENTER = [0x1B, 0x61, 0x31];
            const LEFT = [0x1B, 0x61, 0x30];
            const BOLD_ON = [0x1B, 0x45, 0x01];
            const BOLD_OFF = [0x1B, 0x45, 0x00];
            const LF = '\n'; // Line Feed
            const CUT = [0x1D, 0x56, 0x42, 0x00]; // Partial cut

            // --- Start Receipt ---
            add(INIT);
            add(CENTER);
            add(BOLD_ON);
            add('TAKONATION' + LF);
            add(BOLD_OFF);
            add('Jl. Raya Ciomas/Pagelaran' + LF);
            add('Telp: +62 812-8425-4724' + LF);
            add(LF);

            add(LEFT);
            add(`No: ${transaction.transaction_code}` + LF);
            add(`Tgl: ${new Date(transaction.created_at).toLocaleString('id-ID')}` + LF);
            add(`Bayar: ${transaction.payment_method_label}` + LF);
            add('--------------------------------' + LF);

            // Items
            transaction.items.forEach(item => {
                add(item.product.name + LF);
                const qtyPrice = `${item.quantity} x ${formatCurrency(item.unit_price)}`;
                const subtotal = formatCurrency(item.total_price);
                const line = qtyPrice.padEnd(32 - subtotal.length) + subtotal;
                add(line + LF);
            });

            add('--------------------------------' + LF);

            // Summary
            const subtotalLine = `Subtotal:`.padEnd(32 - formatCurrency(transaction.subtotal).length) + formatCurrency(transaction.subtotal);
            add(subtotalLine + LF);

            if (transaction.discount_amount > 0) {
                const discountLine = `Diskon (${transaction.discount_percentage}%):`.padEnd(32 - `-${formatCurrency(transaction.discount_amount)}`.length) + `-${formatCurrency(transaction.discount_amount)}`;
                add(discountLine + LF);
            }

            if (transaction.tax_amount > 0) {
                const taxLine = `Pajak (${transaction.tax_percentage}%):`.padEnd(32 - formatCurrency(transaction.tax_amount).length) + formatCurrency(transaction.tax_amount);
                add(taxLine + LF);
            }

            add('--------------------------------' + LF);
            add(BOLD_ON);
            const totalLine = `TOTAL:`.padEnd(32 - formatCurrency(transaction.total_amount).length) + formatCurrency(transaction.total_amount);
            add(totalLine + LF);
            add(BOLD_OFF);

            // Cash details
            if (transaction.payment_method === 'cash' && transaction.customer_money > 0) {
                add('--------------------------------' + LF);
                const cashLine = `Bayar:`.padEnd(32 - formatCurrency(transaction.customer_money).length) + formatCurrency(transaction.customer_money);
                add(cashLine + LF);
                const changeLine = `Kembalian:`.padEnd(32 - formatCurrency(transaction.change_amount).length) + formatCurrency(transaction.change_amount);
                add(changeLine + LF);
            }

            // Footer
            add(LF);
            add(CENTER);
            add('Terima kasih atas kunjungan Anda!' + LF);
            add('Selamat menikmati takoyaki kami' + LF + LF + LF);
            add(CUT);

            // Combine all commands into a single buffer
            const totalLength = commands.reduce((acc, val) => acc + val.length, 0);
            const buffer = new Uint8Array(totalLength);
            let offset = 0;
            commands.forEach(cmd => {
                buffer.set(cmd, offset);
                offset += cmd.length;
            });

            return buffer;
        }

        async function printBluetooth(transaction) {
            if (!navigator.bluetooth) {
                alert('Web Bluetooth API tidak didukung di browser ini.');
                return;
            }

            try {
                console.log('Requesting Bluetooth device...');
                const device = await navigator.bluetooth.requestDevice({
                    // Filter untuk printer thermal (generic serial port service)
                    filters: [{ services: ['00001101-0000-1000-8000-00805f9b34fb'] }],
                    // acceptAllDevices: true, // Uncomment jika filter tidak berhasil
                });

                console.log('Connecting to GATT Server...');
                const server = await device.gatt.connect();

                console.log('Getting Service...');
                const service = await server.getPrimaryService('00001101-0000-1000-8000-00805f9b34fb');

                console.log('Getting Characteristic...');
                const characteristic = await service.getCharacteristics().then(chars => chars[0]);

                console.log('Generating ESC/POS data...');
                const data = generateEscPosData(transaction);

                console.log('Sending data to printer...');
                await characteristic.writeValue(data);
                alert('Data berhasil dikirim ke printer!');

            } catch (error) {
                console.error('Error:', error);
                alert(`Gagal mencetak: ${error.message}`);
            }
        }
    </script>
</body>
</html>