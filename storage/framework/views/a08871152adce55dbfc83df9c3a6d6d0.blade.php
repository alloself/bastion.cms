@php
    $bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<section id='block-9ffafbfb-4f27-427b-af62-7f6ebcd024eb' class="app-services-head-contentBlock text-white py-8 bg-cover bg-no-repeat bg-center" style="background-image: url({{$bg}});">
    <div class="centered">
        <div class="pt-[140px] md:ml-[25%] md:w-3/4 lg:ml-[50%] lg:w-1/2 lg:pt-[330px]">
            <h1 class="h2 lg:mb-14">{{$contentBlock->link->title}}</h1>
            <div class="p-5 bg-stone-100">
                <div class="text-dark text-opacity-60 text-simple">{!!$contentBlock->content!!}</div>
                @if (isset($link))
                <div class="flex justify-end gap-1 mt-8" style="justify-content: end;">
                    <a href="{{$link}}" class="min-w-[200px] app-button app-button--secondary">Узнать больше</a>
                    <a href="{{$link}}" class="app-button app-button--secondary app-button--square">+</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>