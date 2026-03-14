@props([
    'searchPlaceholder' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 border-b border-slate-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between']) }}>
    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
        @if($searchPlaceholder)
            <label class="relative block min-w-0 flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                </span>
                <input
                    type="search"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-orange-100"
                >
            </label>
        @endif

        @if(trim((string) ($filters ?? '')) !== '')
            <div class="flex flex-wrap items-center gap-3">
                {{ $filters }}
            </div>
        @endif
    </div>

    @if(trim((string) ($actions ?? '')) !== '')
        <div class="flex flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
