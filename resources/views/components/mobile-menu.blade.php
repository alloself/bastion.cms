@props(['navigation' => []])

<offcanvas name="mobileMenu" v-cloak>
    <nav class="mb-8 w-[600px] max-w-full mx-auto">
        <ul class="text-[20px] leading-[1.1] flex flex-col">
            @if(isset($navigation) && count($navigation))
                @foreach ($navigation as $item)
                    <li class="border-b border-neutral-alpha">
                        <a href="{{ getAttributeByKey($item, 'link')?->pivot->value }}"
                           class="block py-2 font-semibold [&.is-active-link]:text-brand hover:text-brand">
                            {{ $item->name }}
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </nav>
    <div class="mt-auto flex flex-col items-center">
        <geolocation class="mb-8 font-medium"></geolocation>
        <div class="text-center flex flex-col items-center">
            <a class="text-[20px] font-semibold leading-[1.1] tracking-[-0.8px]" href="tel:88126678226">
                8 (812) 667-82-26
            </a>
            <div class="flex items-center leading-[1.4] text-[12px] text-accent">
                <div class="svg-icon flex-none mr-1 w-[6px] h-[6px]">
                    <svg><use xlink:href="#dot"></use></svg>
                </div>
                <div class="font-medium tracking-[0.24px]">10.00-20.00</div>
            </div>
        </div>
    </div>
</offcanvas>