@php
    $icon_sprite = getAttributeByKey($contentBlock, 'icon_name_sprite')?->pivot->value;
    $bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
@endphp

<div id='block-9fee52a6-b403-448c-9313-52df45540b82' class="service-card {{empty($bg) ? 'service-card--noimage' : ''}}">
    <div class="service-card__bg" style="background-image: url({{$bg}});"></div>
    <div class="service-card__title">{{$contentBlock->link->title}}</div>
    @if (isset($icon_sprite))
    <div class="service-card__middle">
        <div class="service-card__icon">
            <div class="svg-icon">
                <svg><use xlink:href="#{{$icon_sprite}}"></use></svg>
            </div>
        </div>
    </div>
    @endif
    <div class="service-card__tooltip">
        <div class="svg-icon">
            <svg><use xlink:href="#alert"></use></svg>
        </div>
    </div>
    @if (!empty($contentBlock->content))
    <div class="service-card__content text-simple">
        {!!$contentBlock->content!!}
    </div>
    @endif
</div>