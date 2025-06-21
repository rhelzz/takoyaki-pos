<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center p-8">
        <div class="mb-8">
            <i class="fas fa-search text-9xl text-gray-300 mb-4"></i>
            <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-600 mb-4">Halaman Tidak Ditemukan</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Maaf, halaman yang Anda cari tidak dapat ditemukan. 
                Mungkin halaman telah dipindahkan atau dihapus.
            </p>
        </div>
        
        <div class="space-x-4">
            <a href="{{ route('dashboard') }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-home mr-2"></i>Kembali ke Dashboard
            </a>
            <button onclick="history.back()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </button>
        </div>
    </div>
</body>
</html>