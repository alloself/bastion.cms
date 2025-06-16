@php

    $global = getItemByPivotKey($header->dataCollections, 'global');

    $logo_dark = getItemByPivotKey($global->images, 'logo_dark')?->url;
    $phone = getAttributeByKey($global, 'phone')?->pivot->value;
    $phone_link = $phone ? preg_replace('/[^0-9]/', '', $phone) : '';
    $work_schedule = getAttributeByKey($global, 'work_schedule')?->pivot->value;
    $navigation = getItemByPivotKey($header->dataCollections, 'navigation')?->dataEntities->sortByDesc('pivot.order');
@endphp

<header class="app-header sticky top-0 z-[100] flex-none">
    <div class="app-header__top py-4 border-b border-neutral bg-white">
        <div class="centered flex items-center">
            <div class="app-logo app-logo--header">
                <a href="/">
                    <img src="{{ $logo_dark }}" alt="">
                </a>
            </div>
            <div class="ml-auto flex items-center gap-4 pl-4 lg:gap-8">
                <GeoLocation class="text-[12px] font-medium hidden md:flex lg:text-[16px]"></GeoLocation>
                <div class="text-center pt-2 hidden md:block lg:pt-4">
                    <a class="text-[14px] font-semibold leading-[1.1] tracking-[-0.8px] lg:text-[20px]"
                        href="tel:{{ $phone_link }}">
                        {{ $phone }}
                    </a>
                    <div class="flex items-center justify-center leading-[1.4] text-[10px] text-accent lg:text-[12px]">
                        <div class="svg-icon flex-none mr-1 w-[6px] h-[6px]">
                            <svg id="work-clocks">
                                <use xlink:href="#dot"></use>
                            </svg>
                        </div>
                        <div class="font-medium tracking-[0.24px]" id="working-time">10.00-20.00</div>
                    </div>
                </div>
            </div>
            <mobilemenuburger class="md:hidden"></mobilemenuburger>
        </div>
    </div>
    <div class="app-header__menu bg-white">
        <div class="centered">
            <div class="relative flex items-center justify-between py-2.5 min-h-[44px]">
                <offcanvasclose></offcanvasclose>
                <nav class=" leading-[1.3] font-medium hidden md:block lg:text-base text-[14px]">
                    <ul class="select-none flex items-center justify-center gap-5 lg:gap-8">
                        @foreach ($navigation as $item)
                            <li><a href="{{ getAttributeByKey($item, 'link')?->pivot->value }}"
                                    class="decor-link [&.is-active-link]:text-brand">{{ $item->name }}</a></li>
                        @endforeach
                    </ul>
                </nav>
                <appsearch></appsearch>
            </div>
        </div>
    </div>
</header>
