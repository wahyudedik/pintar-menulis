<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Layanan - Smart Copy SMK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">Smart Copy SMK</h1>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-blue-600">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Pilih Paket yang Sesuai</h2>
            <p class="text-xl text-gray-600">Copywriting berkualitas, harga pelajar, hasil profesional</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($packages as $package)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition {{ $package->name === 'Professional' ? 'ring-2 ring-blue-600' : '' }}">
                @if($package->name === 'Professional')
                <div class="bg-blue-600 text-white text-center py-2 text-sm font-semibold">
                    PALING POPULER
                </div>
                @endif
                
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>
                    <p class="text-gray-600 mb-6">{{ $package->description }}</p>
                    
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        <span class="text-gray-600">/bulan</span>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $package->caption_quota }} caption media sosial</span>
                        </li>
                        @if($package->product_description_quota > 0)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $package->product_description_quota }} deskripsi produk</span>
                        </li>
                        @endif
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Revisi {{ $package->revision_limit }}x</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Response time {{ $package->response_time_hours }} jam</span>
                        </li>
                        @if($package->consultation_included)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Konsultasi strategi konten</span>
                        </li>
                        @endif
                        @if($package->content_calendar_included)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Content calendar planning</span>
                        </li>
                        @endif
                    </ul>

                    @auth
                        <a href="{{ route('orders.create', $package) }}" class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Pilih Paket
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-16 bg-blue-50 rounded-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Kenapa Pilih Smart Copy SMK?</h3>
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <div class="text-blue-600 text-3xl mb-2">💰</div>
                    <h4 class="font-semibold mb-2">Harga Terjangkau</h4>
                    <p class="text-gray-600">Harga khusus untuk UMKM, lebih murah dari jasa copywriting profesional</p>
                </div>
                <div>
                    <div class="text-blue-600 text-3xl mb-2">🤖</div>
                    <h4 class="font-semibold mb-2">AI + Human Touch</h4>
                    <p class="text-gray-600">Kombinasi teknologi AI dengan editing manual oleh siswa terlatih</p>
                </div>
                <div>
                    <div class="text-blue-600 text-3xl mb-2">⚡</div>
                    <h4 class="font-semibold mb-2">Cepat & Responsif</h4>
                    <p class="text-gray-600">Response time maksimal 24 jam, siap bantu UMKM berkembang</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2026 Smart Copy SMK. Dibuat dengan ❤️ oleh siswa SMK</p>
        </div>
    </footer>
</body>
</html>
