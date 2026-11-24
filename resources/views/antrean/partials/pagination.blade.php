@if ($paginator->hasPages())
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        <div class="text-xs text-slate-500">
            Menampilkan <span class="font-semibold text-slate-700">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai <span class="font-semibold text-slate-700">{{ $paginator->lastItem() ?? 0 }}</span>
            dari <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span> antrean
        </div>

        <div class="flex items-center space-x-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-xs font-medium text-slate-400 bg-white border border-slate-200 rounded-lg cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                </span>
            @else
                <button type="button" onclick="loadTableData({{ $paginator->currentPage() - 1 }})"
                        class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                </button>
            @endif

            {{-- Current Page Info --}}
            <span class="px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button type="button" onclick="loadTableData({{ $paginator->currentPage() + 1 }})"
                        class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg shadow-sm transition-colors">
                    Next <i class="fa-solid fa-chevron-right ml-1"></i>
                </button>
            @else
                <span class="px-3 py-1.5 text-xs font-medium text-slate-400 bg-white border border-slate-200 rounded-lg cursor-not-allowed">
                    Next <i class="fa-solid fa-chevron-right ml-1"></i>
                </span>
            @endif
        </div>
    </div>
@endif
