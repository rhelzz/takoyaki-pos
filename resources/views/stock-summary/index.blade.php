@extends('layouts.app')
@section('title', 'Stock Transaksi List')

@section('content')
<div class="p-4 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6 text-blue-800 flex items-center gap-2">
        <i class="fas fa-calendar-alt text-blue-500"></i> Daftar Transaksi Stock (Masuk & Keluar)
    </h1>
    {{-- Filter Section --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-3 sm:items-center justify-between">
        <div class="flex gap-2 items-center">
            <input type="date" id="filterTanggal"
                   class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 transition"
                   placeholder="Cari tanggal..." />
            <button id="clearFilter" class="px-2 py-2 bg-gray-200 hover:bg-gray-300 rounded text-xs text-gray-600 transition hidden" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div>
            <span class="text-gray-500 text-sm" id="cardCount"></span>
        </div>
    </div>
    {{-- Card Grid --}}
    <div id="cardGrid" class="flex flex-col gap-4"></div>
    {{-- Pagination --}}
    <div class="flex justify-center mt-6" id="pagination"></div>
</div>

{{-- Modal --}}
<div id="summaryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative animate-fadeIn">
        <button onclick="closeSummaryModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-800 text-2xl">
            <i class="fas fa-times-circle"></i>
        </button>
        <h2 class="text-lg font-bold mb-2 text-blue-700 flex flex-col gap-1">
            <span id="modalJenis"></span>
            <span id="modalJudul" class="block font-semibold text-blue-800"></span>
            <span id="modalTanggal"></span>
        </h2>
        <div id="modalDetail"></div>
        <div id="modalDeskripsi" class="mt-2 text-gray-500 text-sm"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const summaryData = @json($summaryTransaksi);
let filteredData = [...summaryData];
let currentPage = 1;
const perPage = 5;

// TANGGAL SAJA, TANPA JAM
function formatDateID(isoDateTime) {
    const tgl = new Date(isoDateTime);
    return tgl.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
}

function renderCards() {
    const start = (currentPage-1) * perPage;
    const end = start + perPage;
    const pageItems = filteredData.slice(start, end);
    let html = '';
    pageItems.forEach((trx, idx) => {
        html += `
        <button class="bg-white rounded-xl shadow border p-4 flex flex-col gap-2 text-left hover:border-blue-400 hover:shadow-lg transition cursor-pointer focus:outline-none group"
            onclick="showSummaryModal(${summaryData.findIndex(s => s.id === trx.id && s.jenis === trx.jenis)})"
        >
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                    ${trx.jenis==='masuk' ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-500'}">
                    <i class="fas ${trx.jenis==='masuk' ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                </span>
                <span class="font-bold text-base ${trx.jenis==='masuk' ? 'text-green-600' : 'text-red-600'}">
                    ${trx.jenis==='masuk' ? 'Stock Masuk' : 'Stock Keluar'}
                </span>
                <span class="ml-auto text-xs text-gray-500">${formatDateID(trx.waktu)}</span>
            </div>
            <div class="font-semibold text-blue-800 mb-1">${trx.judul || '-'}</div>
            <ul class="mt-2 text-sm text-gray-700 grid grid-cols-2 gap-x-4">
                ${trx.barang.map(b => 
                    `<li class="flex justify-between"><span>${b.nama_barang}</span> <b>${b.qty}</b></li>`
                ).join('')}
            </ul>
        </button>`;
    });
    if(pageItems.length === 0) {
        html = `<div class="text-center text-gray-400 py-10">Tidak ada data untuk tanggal ini.</div>`;
    }
    document.getElementById('cardGrid').innerHTML = html;
    document.getElementById('cardCount').textContent =
        `Menampilkan ${pageItems.length} dari ${filteredData.length} transaksi`;
    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(filteredData.length / perPage);
    let html = '';
    if(totalPages <= 1) {
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    html += `<nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">`;
    html += `<button class="px-3 py-1 border border-gray-200 rounded-l bg-white ${currentPage === 1 ? 'text-gray-400' : 'hover:bg-gray-100'}"
                ${currentPage === 1 ? 'disabled' : ''} onclick="gotoPage(${currentPage-1})">&laquo;</button>`;
    for(let i=1; i<=totalPages; i++) {
        html += `<button class="px-3 py-1 border-t border-b border-gray-200 bg-white ${i===currentPage ? 'text-blue-600 font-bold underline' : 'hover:bg-gray-100'}"
                    onclick="gotoPage(${i})">${i}</button>`;
    }
    html += `<button class="px-3 py-1 border border-gray-200 rounded-r bg-white ${currentPage === totalPages ? 'text-gray-400' : 'hover:bg-gray-100'}"
                ${currentPage === totalPages ? 'disabled' : ''} onclick="gotoPage(${currentPage+1})">&raquo;</button>`;
    html += '</nav>';
    document.getElementById('pagination').innerHTML = html;
}
function gotoPage(page) {
    currentPage = page;
    renderCards();
}

// Filter by tanggal
const filterTanggal = document.getElementById('filterTanggal');
const clearFilterBtn = document.getElementById('clearFilter');
filterTanggal.addEventListener('change', function() {
    const val = this.value;
    if(val) {
        filteredData = summaryData.filter(s => s.tanggal === val);
        clearFilterBtn.classList.remove('hidden');
    } else {
        filteredData = [...summaryData];
        clearFilterBtn.classList.add('hidden');
    }
    currentPage = 1;
    renderCards();
});
clearFilterBtn.addEventListener('click', function() {
    filterTanggal.value = '';
    filteredData = [...summaryData];
    this.classList.add('hidden');
    currentPage = 1;
    renderCards();
});

// Modal logic
function showSummaryModal(idx) {
    const data = summaryData[idx];
    document.getElementById('summaryModal').classList.remove('hidden');
    document.getElementById('modalJenis').innerHTML =
        `<span class="${data.jenis==='masuk'?'text-green-600':'text-red-600'} mr-2 font-bold text-base">${data.jenis==='masuk'?'Stock Masuk':'Stock Keluar'}</span>`;
    document.getElementById('modalJudul').textContent = data.judul || '-';
    document.getElementById('modalTanggal').textContent = formatDateID(data.waktu);
    document.getElementById('modalDeskripsi').textContent = data.deskripsi || '';

    let html = `<table class="w-full text-sm mt-2">
        <thead>
            <tr class="border-b border-gray-200">
                <th class="text-left py-1">Barang</th>
                <th class="text-center py-1">${data.jenis==='masuk' ? 'Qty Masuk' : 'Qty Keluar'}</th>
            </tr>
        </thead>
        <tbody>`;
    data.barang.forEach(item => {
        html += `<tr>
            <td class="py-1">${item.nama_barang}</td>
            <td class="py-1 text-center font-bold">${item.qty}</td>
        </tr>`;
    });
    html += "</tbody></table>";
    document.getElementById('modalDetail').innerHTML = html;
}
function closeSummaryModal() {
    document.getElementById('summaryModal').classList.add('hidden');
}

// Initial render
renderCards();
</script>
<style>
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95);} to { opacity: 1; transform: scale(1);} }
.animate-fadeIn { animation: fadeIn 0.2s;}
input[type="date"]:focus { border-color: #3b82f6 !important; }
</style>
@endpush