<nav class="flex flex-wrap gap-2 mb-6 bg-white/5 p-3 rounded-xl border border-white/10">
    <a href="/explain"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition
              {{ request()->is('explain') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-white/10' }}">
        📦 Dashboard
    </a>
    <a href="{{ route('visual.explain') }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition
              {{ request()->is('visual-explain') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-white/10' }}">
        🔍 Visual Explain
    </a>
    <a href="{{ route('index.recommend') }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition
              {{ request()->is('index-recommend') ? 'bg-yellow-600 text-white' : 'text-gray-300 hover:bg-white/10' }}">
        💡 Index Advisor
    </a>
    <a href="{{ route('performance.history') }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition
              {{ request()->is('performance-history') ? 'bg-green-600 text-white' : 'text-gray-300 hover:bg-white/10' }}">
        📈 Performance History
    </a>
    <a href="{{ route('schema.analyzer') }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition
              {{ request()->is('schema-analyzer') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-white/10' }}">
        🗄 Schema Analyzer
    </a>
</nav>
