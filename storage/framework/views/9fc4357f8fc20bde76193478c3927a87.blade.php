@php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<div id='block-9ffafaca-2864-41df-90e3-ab7e063095a6' class="pb-1 border-b border-brand border-opacity-20">
    <div class="h3 mb-1"><a href="{{$link}}">{{$contentBlock->link->title}}</a></div>
    <div class="text-simple text-brand text-opacity-60">{{$contentBlock->link->subtitle}}</div>
</div>