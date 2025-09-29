@php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<section id='block-9ffeb885-8e26-4774-8159-fb0439164b29' class="app-about-history-head bg-stone bg-opacity-30 py-8 lg:py-14">
    <div class="centered">
        <div class="flex flex-col mb-8 lg:mb-14 lg:flex-row">
            <div class="text-brand text-opacity-60 text-simple lg:pr-4 lg:w-1/4">{{$contentBlock->link->subtitle}}</div>
            <h2 class="mb-0 lg:w-3/4">{{$contentBlock->link->title}}</h2>
        </div>
        <div class="text-simple text-brand text-opacity-60 lg:ml-[50%] lg:gap-6 lg:columns-2 lg:pl-3">
            {!!$contentBlock->content!!}
        </div>
        <div class="flex gap-1 mt-8 lg:ml-[50%] lg:pl-3">
            <button 
                v-modal-call="{name: 'callbackModal'}"
                class="app-button app-button--light min-w-[200px]"
            >Стать частью истории</button>
            <button 
                v-modal-call="{name: 'callbackModal'}"
                class="app-button app-button--square app-button--light"
            >+</button>
        </div>
    </div>
</section>

@if (isset($contentBlock->renderedChildren) && count($contentBlock->renderedChildren))
    @foreach($contentBlock->renderedChildren as $children)
        {!!$children!!}
    @endforeach
@endif

<div id='block-9ffeb885-8e26-4774-8159-fb0439164b29' class="py-12 flex gap-1 justify-center">
    @if (isset($link))
        <a
            href="{{$link}}"
            class="app-button app-button--primary min-w-[200px]"
        >Смотреть еще</a>
        <a
            href="{{$link}}"
            class="app-button app-button--primary app-button--square"
        >+</a>
    @endif
</div>