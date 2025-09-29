@props([
    'data',
])

@php
    $block = $data;
    $icon_sprite = getAttributeByKey($block, 'icon_name_sprite')?->pivot->value;
    $bg = getItemByPivotKey($block->images, 'bg')?->path;
@endphp

<div class="service-card {{empty($bg) ? 'service-card--noimage' : ''}}">
    <div class="service-card__bg" style="background-image: url({{$bg}});"></div>
    <div class="service-card__title">{{$block->link->title}}</div>
    <div class="service-card__middle">
        @if (isset($icon_sprite))
        <div class="service-card__icon">
            <div class="svg-icon">
                <svg><use xlink:href="#{{$icon_sprite}}"></use></svg>
            </div>
        </div>
        @endif
    </div>
    <div class="service-card__tooltip">
        <div class="svg-icon">
            <svg><use xlink:href="#alert"></use></svg>
        </div>
    </div>
    @if (!empty($block->content))
    <div class="service-card__content text-simple">
        {!!$block->content!!}
    </div>
    @endif
</div>