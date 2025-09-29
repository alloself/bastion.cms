@php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<div id='block-9ffaf9cd-9ddf-4408-87cf-f165df800086' class="pb-1 border-b border-brand border-opacity-20">
    <div class="h3 mb-1"><a href="{{$link}}">{{$contentBlock->link->title}}</a></div>
    <div class="text-simple text-brand text-opacity-60">{{$contentBlock->link->subtitle}}</div>
</div>