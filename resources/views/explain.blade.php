<!DOCTYPE html>
<html>
<head>
    <title>Laravel Product Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #0f172a, #1e293b); }
    </style>
</head>

<body class="text-gray-100">

<div class="max-w-6xl mx-auto p-6">

    @include('partials.nav')

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold">📦 Product Dashboard</h1>
        <p class="text-gray-400 text-sm mt-1">Add, search, and manage your products</p>
    </div>

    <!-- SUCCESS ALERT -->
    @if(session('success'))
        <div class="bg-green-600 text-white p-3 rounded-lg mb-4 shadow flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- STATS -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white/10 backdrop-blur-lg p-5 rounded-xl shadow border border-white/10">
            <h2 class="text-sm text-gray-400">Total Products</h2>
            <p class="text-3xl font-bold text-white mt-1">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white/10 backdrop-blur-lg p-5 rounded-xl shadow border border-white/10">
            <h2 class="text-sm text-gray-400">High Price (&gt;500)</h2>
            <p class="text-3xl font-bold text-green-400 mt-1">{{ $highPriceProducts }}</p>
        </div>
    </div>

    <!-- ADD PRODUCT FORM -->
    <div class="bg-white/10 backdrop-blur-lg p-5 rounded-xl mb-6 border border-white/10">
        <h2 class="text-lg font-semibold mb-4">➕ Add New Product</h2>

        <form method="POST" action="{{ route('product.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Name -->
                <div>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Product Name *"
                        class="w-full p-3 rounded-lg bg-gray-900 border
                               {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }}
                               text-white placeholder-gray-500 focus:outline-none focus:border-blue-500">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">⚠ {{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <input
                        type="text"
                        name="description"
                        value="{{ old('description') }}"
                        placeholder="Description *"
                        class="w-full p-3 rounded-lg bg-gray-900 border
                               {{ $errors->has('description') ? 'border-red-500' : 'border-gray-700' }}
                               text-white placeholder-gray-500 focus:outline-none focus:border-blue-500">
                    @error('description')
                        <p class="text-red-400 text-xs mt-1">⚠ {{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <input
                        type="number"
                        name="price"
                        value="{{ old('price') }}"
                        placeholder="Price *"
                        min="0"
                        step="0.01"
                        class="w-full p-3 rounded-lg bg-gray-900 border
                               {{ $errors->has('price') ? 'border-red-500' : 'border-gray-700' }}
                               text-white placeholder-gray-500 focus:outline-none focus:border-blue-500">
                    @error('price')
                        <p class="text-red-400 text-xs mt-1">⚠ {{ $message }}</p>
                    @enderror
                </div>

            </div>

            <button
                type="submit"
                class="mt-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-6 py-2.5 rounded-lg text-white font-semibold transition">
                ➕ Add Product
            </button>

        </form>
    </div>

    <!-- SEARCH -->
    <form method="GET" action="/explain" class="mb-5">
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔎</span>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by name, description, or price..."
                class="w-full pl-9 pr-4 p-3 rounded-lg bg-white/10 text-white border border-white/20
                       placeholder-gray-500 focus:outline-none focus:border-blue-500">
        </div>
    </form>

    <!-- PRODUCT TABLE -->
    <div class="bg-white/10 backdrop-blur-lg rounded-xl overflow-hidden border border-white/10">

        @if($products->isEmpty())
            <div class="p-10 text-center text-gray-400">
                <p class="text-4xl mb-3">📭</p>
                <p class="text-lg font-semibold">No products found</p>
                <p class="text-sm mt-1">
                    {{ $search ? 'Try a different search term.' : 'Add your first product using the form above.' }}
                </p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-900 text-gray-400 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">Description</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr class="border-t border-gray-700/50 hover:bg-white/5 transition">
                            <td class="p-3 text-gray-400">{{ $product->id }}</td>
                            <td class="p-3 font-semibold text-white">{{ $product->name }}</td>
                            <td class="p-3 text-gray-300">{{ $product->description }}</td>
                            <td class="p-3 text-green-400 font-bold">${{ number_format($product->price, 2) }}</td>
                            <td class="p-3 text-center">
                                <form method="POST" action="{{ route('product.delete', $product->id) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg text-white text-xs font-semibold transition">
                                        🗑 Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

    <!-- PAGINATION -->
    @if($products->hasPages())
        <div class="mt-5 flex justify-center">
            {{ $products->appends(['search' => $search])->links('pagination::tailwind') }}
        </div>
    @endif

</div>

</body>
</html>
