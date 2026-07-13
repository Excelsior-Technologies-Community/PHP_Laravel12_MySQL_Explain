<!DOCTYPE html>
<html>
<head>
    <title>Auto-Index Recommendation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background: linear-gradient(135deg, #0f172a, #1e293b); }</style>
</head>
<body class="text-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto p-6">

    @include('partials.nav')

    <h1 class="text-2xl font-bold mb-1">💡 Auto-Index Recommendation Engine</h1>
    <p class="text-gray-400 mb-5 text-sm">System analyzes your query and suggests indexes automatically</p>

    {{-- QUERY INPUT --}}
    <form method="GET" class="mb-6">
        <textarea name="query" rows="3"
            class="w-full p-3 rounded bg-white/10 text-white border border-white/20 font-mono text-sm"
            placeholder="Enter SELECT query...">{{ $rawQuery ?? 'SELECT * FROM products WHERE price > 500' }}</textarea>
        <button class="mt-2 bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded text-white font-semibold">
            🔎 Analyze Query
        </button>
    </form>

    @if(isset($error))
        <div class="bg-red-700 text-white p-3 rounded mb-4">⚠ {{ $error }}</div>
    @endif

    @if(isset($explainRows))

    {{-- RECOMMENDATIONS --}}
    @if(count($recommendations) > 0)
        <div class="mb-6">
            <h2 class="text-lg font-bold text-yellow-300 mb-3">⚠ Index Recommendations</h2>
            @foreach($recommendations as $rec)
            <div class="bg-yellow-900/40 border border-yellow-500 rounded-xl p-5 mb-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-yellow-300 text-lg">Table: {{ $rec['table'] }}</p>
                        <p class="text-sm text-gray-300 mt-1">
                            Scan Type: <span class="text-red-400 font-bold">{{ strtoupper($rec['type']) }}</span>
                            &nbsp;|&nbsp; Rows Scanned: <span class="text-red-400 font-bold">{{ $rec['rows'] }}</span>
                            &nbsp;|&nbsp; Possible Keys: <span class="text-gray-400">{{ $rec['possible_keys'] ?? 'None' }}</span>
                        </p>
                        <p class="mt-2 text-green-300 font-semibold">✅ {{ $rec['suggestion'] }}</p>

                        @if(count($rec['columns']) > 0)
                        <div class="mt-3 bg-gray-900 rounded p-3 font-mono text-sm text-green-400">
                            ALTER TABLE `{{ $rec['table'] }}` ADD INDEX idx_{{ implode('_', $rec['columns']) }} ({{ implode(', ', $rec['columns']) }});
                        </div>
                        @endif
                    </div>
                    <span class="bg-red-600 text-white text-xs px-3 py-1 rounded-full ml-4 whitespace-nowrap">
                        🔴 No Index
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-green-900/40 border border-green-500 rounded-xl p-5 mb-6">
            <p class="text-green-300 font-bold text-lg">✅ Query looks optimized!</p>
            <p class="text-gray-300 text-sm mt-1">Index is being used. No recommendation needed.</p>
        </div>
    @endif

    {{-- EXPLAIN TABLE --}}
    <div class="bg-white/10 rounded-xl p-5 border border-white/10 overflow-x-auto">
        <h2 class="font-semibold mb-3 text-gray-300">📋 EXPLAIN Output</h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-900 text-gray-300">
                <tr>
                    @foreach(['id','select_type','table','type','possible_keys','key','rows','Extra'] as $col)
                        <th class="p-2 text-left">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($explainRows as $row)
                <tr class="border-t border-gray-700">
                    <td class="p-2">{{ $row->id }}</td>
                    <td class="p-2">{{ $row->select_type }}</td>
                    <td class="p-2 font-semibold text-blue-300">{{ $row->table }}</td>
                    <td class="p-2 {{ ($row->type ?? '') === 'ALL' ? 'text-red-400 font-bold' : 'text-green-400 font-bold' }}">
                        {{ strtoupper($row->type ?? '') }}
                    </td>
                    <td class="p-2 text-yellow-300">{{ $row->possible_keys ?? 'NULL' }}</td>
                    <td class="p-2 {{ $row->key ? 'text-green-400 font-bold' : 'text-red-400 font-bold' }}">
                        {{ $row->key ?? 'NULL' }}
                    </td>
                    <td class="p-2">{{ $row->rows }}</td>
                    <td class="p-2 text-gray-300 text-xs">{{ $row->Extra }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
</div>

</body>
</html>
