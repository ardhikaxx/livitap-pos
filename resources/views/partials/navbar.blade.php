<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('pos.index') }}" class="text-xl font-bold text-blue-600">
                    LIVITAP POS
                </a>
                
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="{{ route('pos.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2">
                        POS
                    </a>
                    <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2">
                        Produk
                    </a>
                    <a href="{{ route('stocks.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2">
                        Stok
                    </a>
                    <a href="{{ route('reports.sales') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2">
                        Laporan
                    </a>
                    <a href="{{ route('settings.business') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2">
                        Pengaturan
                    </a>
                </div>
            </div>

            <div class="flex items-center">
                <div class="relative">
                    <span class="text-gray-700">
                        {{ auth()->user()->name }}
                    </span>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                        <a href="{{ route('settings.business') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
