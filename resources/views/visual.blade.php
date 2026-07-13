<!DOCTYPE html>
<html>
<head>
    <title>Visual Query Execution Plan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <style>body { background: linear-gradient(135deg, #0f172a, #1e293b); }</style>
</head>
<body class="text-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto p-6">

    {{-- NAV --}}
    @include('partials.nav')

    <h1 class="text-2xl font-bold mb-1">🔍 Visual Query Execution Plan</h1>
    <p class="text-gray-400 mb-5 text-sm">EXPLAIN FORMAT=JSON rendered as a Mermaid flowchart</p>

    {{-- QUERY INPUT --}}
    <form method="GET" class="mb-6">
        <textarea name="query" rows="3"
            class="w-full p-3 rounded bg-white/10 text-white border border-white/20 font-mono text-sm"
            placeholder="Enter SELECT query...">{{ $rawQuery ?? 'SELECT * FROM products WHERE price > 500' }}</textarea>
        <button class="mt-2 bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded text-white font-semibold">
            ▶ Run EXPLAIN
        </button>
    </form>

    {{-- ERROR --}}
    @if(isset($error))
        <div class="bg-red-700 text-white p-3 rounded mb-4">⚠ {{ $error }}</div>
    @endif

    @if(isset($explainRows))

    {{-- EXECUTION TIME --}}
    <div class="bg-white/10 rounded-xl p-4 mb-5 border border-white/10 flex items-center gap-4">
        <div>
            <p class="text-xs text-gray-400">Execution Time</p>
            <p class="text-2xl font-bold {{ $execTime > 100 ? 'text-red-400' : 'text-green-400' }}">
                {{ $execTime }} ms
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Rows Examined</p>
            <p class="text-2xl font-bold text-yellow-300">{{ $explainRows[0]->rows ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Index Used</p>
            <p class="text-2xl font-bold {{ ($explainRows[0]->key ?? null) ? 'text-green-400' : 'text-red-400' }}">
                {{ $explainRows[0]->key ?? 'NONE' }}
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Scan Type</p>
            <p class="text-lg font-bold {{ ($explainRows[0]->type ?? '') === 'ALL' ? 'text-red-400' : 'text-green-400' }}">
                {{ strtoupper($explainRows[0]->type ?? '-') }}
            </p>
        </div>
    </div>

    {{-- MERMAID FLOWCHART --}}
    <div class="bg-white/10 rounded-xl p-5 mb-5 border border-white/10">
        <h2 class="font-semibold mb-3 text-gray-300">📊 Execution Flow</h2>
        <div class="mermaid bg-white rounded-lg p-4 overflow-x-auto">
@php
$mermaid = "flowchart TD\n";
$mermaid .= "    A([\"🚀 Query Start\"]) --> B\n";

foreach ($explainRows as $i => $row) {
    $isFullScan = ($row->type ?? '') === 'ALL';
    $hasKey     = !empty($row->key);
    $nodeId     = "B" . $i;
    $nextId     = "C" . $i;

    $scanLabel  = $isFullScan ? "🔴 FULL TABLE SCAN" : "🟢 INDEX SCAN";
    $keyLabel   = $hasKey ? "Key: {$row->key}" : "No Index";
    $rowsLabel  = "Rows: " . ($row->rows ?? '?');

    $mermaid .= "    {$nodeId}[\"Table: {$row->table}\\n{$scanLabel}\\n{$keyLabel}\\n{$rowsLabel}\"]\n";
    $mermaid .= "    B --> {$nodeId}\n";
    $mermaid .= "    {$nodeId} --> {$nextId}[\"Extra: " . addslashes($row->Extra ?? 'none') . "\"]\n";
    $mermaid .= "    {$nextId} --> END\n";
}

$mermaid .= "    END([\"✅ Result Returned\"])\n";

// Color styling
foreach ($explainRows as $i => $row) {
    $isFullScan = ($row->type ?? '') === 'ALL';
    $nodeId = "B" . $i;
    $mermaid .= $isFullScan
        ? "    style {$nodeId} fill:#ef4444,color:#fff,stroke:#b91c1c\n"
        : "    style {$nodeId} fill:#22c55e,color:#fff,stroke:#15803d\n";
}
$mermaid .= "    style A fill:#3b82f6,color:#fff\n";
$mermaid .= "    style END fill:#8b5cf6,color:#fff\n";
@endphp
{{ $mermaid }}
        </div>
    </div>

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

    {{-- JSON TREE --}}
    <div class="bg-white/10 rounded-xl p-5 mt-5 border border-white/10">
        <h2 class="font-semibold mb-3 text-gray-300">🗂 EXPLAIN FORMAT=JSON</h2>
        <pre class="bg-gray-900 text-green-400 p-4 rounded text-xs overflow-x-auto">{{ json_encode($jsonData, JSON_PRETTY_PRINT) }}</pre>
    </div>

    @endif
</div>

<script>mermaid.initialize({ startOnLoad: true, theme: 'default' });</script>
</body>
</html>
