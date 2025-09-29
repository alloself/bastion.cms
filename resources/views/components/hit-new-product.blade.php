@props(['data' => []])

@php
    $title = $data->link->title;
    $subtitle = $data->link->subtitle;
    $image = getItemByPivotKey($data->images, 'image')?->url;
    $url = false; //$data->link->url;
@endphp

<div class="p-3 bg-stone-100 flex flex-col gap-3 relative transition-all md:p-5 hover:bg-stone-200">
    @if ($url)
        <a href="{{ $url }}" class="absolute inset-0"></a>
    @endif
    <div class="text-[18px] font-medium">{{ $title }}</div>
    <div class="mb-5 text-[13px] text-brand/30">{{ $subtitle }}</div>
    <div class="mx-auto max-w-full w-[160px] h-[190x] lg:w-[214px] lg:h-[266px]">
        <img class="w-full h-full object-cover" src="{{ $image }}" alt="{{ $title }}" />
    </div>
</div>
