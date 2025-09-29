@php
    $contentBlock_bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
    $contract_sum = getAttributeByKey($contentBlock, 'contract_sum')?->pivot->value;
@endphp

<section id='block-9fed0e51-a2bd-449f-9251-cdca0cf70cfd' 
    class="app-projects-map py-8 text-white bg-cover bg-no-repeat bg-center" 
    style="background-image: url({{$contentBlock_bg}})"
>
    <div class="centered">
        <h2 class="h1 lg:text-[58px] lg:max-w-[65%] xl:max-w-[50%]">{{$contentBlock->link->title}}</h2>
        <div class="lg:ml-[50%] lg:max-w-[35%]">
            <div class="text-simple text-white text-opacity-80 mb-5">{!!$contentBlock->content!!}</div>
            @if (isset($link))
            <div class="flex gap-1">
                <a href="{{$link}}" class="min-w-[200px] app-button app-button--secondary">Узнать больше</a>
                <a href="{{$link}}" class="app-button app-button--secondary app-button--square">+</a>
            </div>
            @endif
        </div>
        <div class="mt-[120px] md:flex md:gap-[60px] xl:mt-[280px]">
            <div class="font-medium leading-[1.0] text-[96px] md:text-[120px] xl:text-[260px]">{{$contract_sum}}</div>
            <div class="text-label mt-8 md:max-w-[170px]">{{$contentBlock->link->subtitle}}</div>
        </div>
    </div>
</section>