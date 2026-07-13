<!DOCTYPE html>
<html>
<head>
    <title>Database Schema Analyzer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background: linear-gradient(135deg, #0f172a, #1e293b); }</style>
</head>
<body class="text-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto p-6">

    @include('partials.nav')

    <h1 class="text-2xl font-bold mb-1">🗄 Database Schema Analyzer</h1>
    <p class="text-gray-400 mb-5 text-sm">Database: <span class="text-blue-300 font-semibold">{{ $dbName }}</span> — Bottleneck & Index Audit</p>

    {{-- BOTTLENECK ALERT --}}
    @if(count($noIndexTables) > 0)
    <div class="bg-red-900/40 border border-red-500 rounded-xl p-5 mb-6">
        <h2 class="text-red-300 font-bold text-lg mb-2">🔴 Tables with No Index (Potential Bottlenecks)</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($noIndexTables as $tbl)
                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ $tbl }}</span>
            @endforeach
        </div>
        <p class="text-gray-400 text-xs mt-2">These tables have >50 rows but no non-primary index. Consider adding indexes on frequently queried columns.</p>
    </div>
    @else
    <div class="bg-green-900/40 border border-green-500 rounded-xl p-4 mb-6">
        <p class="text-green-300 font-bold">✅ All large tables have indexes. No critical bottlenecks detected.</p>
    </div>
    @endif

    {{-- UNINDEXED COLUMNS (products table) --}}
    @if(count($unindexedColumns) > 0)
    <div class="bg-yellow-900/40 border border-yellow-500 rounded-xl p-4 mb-6">
        <h2 class="text-yellow-300 font-bold mb-2">⚠ Unindexed Columns in `products` table</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($unindexedColumns as $col)
                <span class="bg-yellow-700 text-white px-3 py-1 rounded-full text-sm">{{ $col->COLUMN_NAME }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ALL TABLES --}}
    <div class="bg-white/10 rounded-xl p-5 mb-6 border border-white/10 overflow-x-auto">
        <h2 class="font-semibold mb-3 text-gray-300">📊 All Tables</h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-900 text-gray-300">
                <tr>
                    <th class="p-2 text-left">Table</th>
                    <th class="p-2 text-left">Rows (approx)</th>
                    <th class="p-2 text-left">Data Size</th>
                    <th class="p-2 text-left">Index Size</th>
                    <th class="p-2 text-left">Indexes</th>
                    <th class="p-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $table)
                @php
                    $tblIndexes = $indexMap[$table->TABLE_NAME] ?? [];
                    $nonPrimary = array_filter($tblIndexes, fn($i) => $i->INDEX_NAME !== 'PRIMARY');
                    $hasIndex   = count($nonPrimary) > 0;
                    $dataKb     = round($table->DATA_LENGTH / 1024, 1);
                    $idxKb      = round($table->INDEX_LENGTH / 1024, 1);
                @endphp
                <tr class="border-t border-gray-700 hover:bg-white/5">
                    <td class="p-2 font-semibold text-blue-300">{{ $table->TABLE_NAME }}</td>
                    <td class="p-2 text-yellow-300">{{ number_format($table->TABLE_ROWS) }}</td>
                    <td class="p-2 text-gray-300">{{ $dataKb }} KB</td>
                    <td class="p-2 text-gray-300">{{ $idxKb }} KB</td>
                    <td class="p-2">
                        @foreach($tblIndexes as $idx)
                            <span class="inline-block bg-gray-700 text-xs px-2 py-0.5 rounded mr-1 mb-1
                                {{ $idx->INDEX_NAME === 'PRIMARY' ? 'text-blue-300' : 'text-green-300' }}">
                                {{ $idx->INDEX_NAME }}({{ $idx->COLUMN_NAME }})
                            </span>
                        @endforeach
                        @if(empty($tblIndexes))
                            <span class="text-red-400 text-xs">No indexes</span>
                        @endif
                    </td>
                    <td class="p-2">
                        @if($hasIndex)
                            <span class="bg-green-700 text-white text-xs px-2 py-0.5 rounded">✅ Indexed</span>
                        @elseif(count($tblIndexes) > 0)
                            <span class="bg-blue-700 text-white text-xs px-2 py-0.5 rounded">🔑 PK Only</span>
                        @else
                            <span class="bg-red-700 text-white text-xs px-2 py-0.5 rounded">🔴 No Index</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
