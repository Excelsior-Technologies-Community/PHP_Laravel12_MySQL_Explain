<!DOCTYPE html>
<html>
<head>
    <title>Query Performance History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>body { background: linear-gradient(135deg, #0f172a, #1e293b); }</style>
</head>
<body class="text-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto p-6">

    @include('partials.nav')

    <h1 class="text-2xl font-bold mb-1">📈 Query Performance History</h1>
    <p class="text-gray-400 mb-5 text-sm">Last 7 days average execution time per day</p>

    {{-- CHART --}}
    <div class="bg-white/10 rounded-xl p-5 mb-6 border border-white/10">
        <canvas id="perfChart" height="100"></canvas>
    </div>

    {{-- RECENT LOGS TABLE --}}
    <div class="bg-white/10 rounded-xl p-5 border border-white/10 overflow-x-auto">
        <h2 class="font-semibold mb-3 text-gray-300">🕐 Recent 20 Query Logs</h2>

        @if($recentLogs->isEmpty())
            <p class="text-gray-400 text-sm">No logs yet. Run queries from the
                <a href="{{ route('visual.explain') }}" class="text-blue-400 underline">Visual Explain</a> page.
            </p>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-900 text-gray-300">
                <tr>
                    <th class="p-2 text-left">Time</th>
                    <th class="p-2 text-left">Query</th>
                    <th class="p-2 text-left">Exec (ms)</th>
                    <th class="p-2 text-left">Rows</th>
                    <th class="p-2 text-left">Key Used</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentLogs as $log)
                <tr class="border-t border-gray-700 hover:bg-white/5">
                    <td class="p-2 text-gray-400 text-xs whitespace-nowrap">{{ $log->created_at->format('d M H:i') }}</td>
                    <td class="p-2 font-mono text-xs text-blue-300 max-w-xs truncate">{{ $log->query }}</td>
                    <td class="p-2 {{ $log->execution_time > 100 ? 'text-red-400' : 'text-green-400' }} font-bold">
                        {{ $log->execution_time }}
                    </td>
                    <td class="p-2 text-yellow-300">{{ $log->rows_examined }}</td>
                    <td class="p-2 {{ $log->key_used ? 'text-green-400' : 'text-red-400' }}">
                        {{ $log->key_used ?? 'NULL' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

<script>
const labels = @json($history->pluck('date'));
const avgData = @json($history->pluck('avg_time'));
const maxData = @json($history->pluck('max_time'));

new Chart(document.getElementById('perfChart'), {
    type: 'line',
    data: {
        labels: labels.length ? labels : ['No Data'],
        datasets: [
            {
                label: 'Avg Execution Time (ms)',
                data: avgData.length ? avgData : [0],
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.1)',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Max Execution Time (ms)',
                data: maxData.length ? maxData : [0],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.1)',
                tension: 0.4,
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#e2e8f0' } }
        },
        scales: {
            x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
        }
    }
});
</script>
</body>
</html>
