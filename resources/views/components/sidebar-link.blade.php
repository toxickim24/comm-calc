@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => 'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150 ' .
           ($active
               ? 'bg-brand-600/90 text-white shadow-sm'
               : 'text-gray-300 hover:bg-white/[0.07] hover:text-white')
   ]) }}>
    <span class="{{ $active ? 'text-white' : 'text-gray-500 group-hover:text-gray-200' }} transition-colors">
        {{ $icon }}
    </span>
    {{ $slot }}
</a>
