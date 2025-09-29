@php
    $icon_sprite = getAttributeByKey($contentBlock, 'icon_name_sprite')?->pivot->value;
@endphp

<div id='block-9ffb259c-6c1c-4260-9fb1-ce030207a21d' class="flex gap-5 items-end">
    <div class="flex-none size-14 md:size-[70px] flex items-center justify-center border border-light border-opacity-20 rounded-md">
        <div class="svg-icon text-stone-200 size-5 {{$icon_sprite === 'result' ? 'svg-icon--stroke' : ''}}">
            <svg><use xlink:href="#{{$icon_sprite}}"></use></svg>
        </div>
    </div>
    <div class="pb-2 min-w-0 flex-1 border-b border-light border-opacity-20">
        <div class="h4 mb-1">{{$contentBlock->link->title}}</div>
        <div class="text-simple text-white text-opacity-60">{{$contentBlock->link->subtitle}}</div>
    </div>
</div>