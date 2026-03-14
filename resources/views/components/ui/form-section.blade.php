@props([
    'title',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm']) }}>
    <div class="mb-5 flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-800">{{ $title }}</h3>
            @if($description)
                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">{{ $description }}</p>
            @endif
        </div>

        @if(trim((string) ($aside ?? '')) !== '')
            <div class="text-sm text-slate-500">
                {{ $aside }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-12 gap-5">
        {{ $slot }}
    </div>
</section>
