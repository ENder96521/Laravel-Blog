@props(['variant' => 'text'])

@if($variant === 'outline')
    <span {{ $attributes->merge(['class' => 'inline-flex items-center border border-accent-400 px-2.5 py-1 font-mono text-[11px] tracking-wide text-accent']) }}>
        {{ $slot }}
    </span>
@else
    <span {{ $attributes->merge(['class' => 'font-mono text-[11px] font-medium tracking-wide text-accent uppercase']) }}>
        {{ $slot }}
    </span>
@endif
