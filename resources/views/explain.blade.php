<!DOCTYPE html>
<html>
<head>
    <title>Laravel Product Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
        }
    </style>
</head>

<body class="text-gray-100">

<div class="max-w-6xl mx-auto p-6">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold">📦 Product Dashboard</h1>
        <p class="text-gray-300">Manage products with search, add, delete & analytics</p>
    </div>

    <!-- ALERT -->
    @if(session('success'))
        <div class="bg-green-600 text-white p-3 rounded mb-4 shadow">
            {{ session('success') }}
        </div>
    @endif

    <!-- STATS -->
    <div class="grid grid-cols-2 gap-4 mb-6">

        <div class="bg-white/10 backdrop-blur-lg p-5 rounded-xl shadow border border-white/10">
            <h2 class="text-sm text-gray-300">Total Products</h2>
            <p class="text-3xl font-bold text-white">{{ $totalProducts }}</p>
        </div>

        <div class="bg-white/10 backdrop-blur-lg p-5 rounded-xl shadow border border-white/10">
            <h2 class="text-sm text-gray-300">High Price (>500)</h2>
            <p class="text-3xl font-bold text-green-400">{{ $highPriceProducts }}</p>
        </div>

    </div>

    <!-- ADD PRODUCT -->
    <div class="bg-white/10 backdrop-blur-lg p-5 rounded-xl mb-6 border border-white/10">

        <form method="POST" action="{{ route('product.store') }}">
            @csrf

            <div class="grid grid-cols-3 gap-4">
                <input type="text" name="name" placeholder="Product Name"
                    class="p-2 rounded bg-gray-900 border border-gray-700 text-white">

                <input type="text" name="description" placeholder="Description"
                    class="p-2 rounded bg-gray-900 border border-gray-700 text-white">

                <input type="number" name="price" placeholder="Price"
                    class="p-2 rounded bg-gray-900 border border-gray-700 text-white">
            </div>

            <button class="mt-4 bg-blue-500 hover:bg-blue-600 px-5 py-2 rounded text-white">
                ➕ Add Product
            </button>
        </form>

    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-5">
        <input type="text"
               name="search"
               value="{{ $search }}"
               placeholder="🔎 Find products using name, description, or price..."
               class="w-full p-3 rounded bg-white/10 text-white border border-white/20 backdrop-blur-lg">
    </form>

    <!-- TABLE -->
    <div class="bg-white/10 backdrop-blur-lg rounded-xl overflow-hidden border border-white/10">

        <table class="w-full text-sm">

            <thead class="bg-gray-900 text-gray-300">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Description</th>
                <th class="p-3 text-left">Price</th>
                <th class="p-3 text-left">Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($products as $product)
                <tr class="border-t border-gray-700 hover:bg-white/5 transition">

                    <td class="p-3">{{ $product->id }}</td>
                    <td class="p-3 font-semibold text-white">{{ $product->name }}</td>
                    <td class="p-3 text-gray-300">{{ $product->description }}</td>
                    <td class="p-3 text-green-400 font-bold">${{ $product->price }}</td>

                    <td class="p-3">

                        <form method="POST" action="{{ route('product.delete', $product->id) }}">
                            @csrf
                            @method('DELETE')

                            <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white">
                                Delete
                            </button>
                        </form>

                    </td>

                </tr>
            @endforeach
            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="mt-6 text-white">
        {{ $products->links() }}
    </div>

</div>

</body>
</html>