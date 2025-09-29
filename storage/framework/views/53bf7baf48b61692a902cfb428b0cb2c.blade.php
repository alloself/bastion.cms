@php
    $block_link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<section id='block-9fed0daa-ada7-484e-b7e7-5bdcc0d82189' class="app-cards-block py-8">
    <div class="centered">
        <div class="xl:flex">
            <div class="text-simple text-dark text-opacity-60 mb-6 xl:mb-0 xl:flex-none xl:pr-4 xl:w-1/3">{{$contentBlock->link->subtitle}}</div>
            <div class="xl:min-w-0 xl:flex-1">
                <h2 class="mb-6 xl:max-w-[80%]">{{$contentBlock->link->title}}</h2>
            </div>
        </div>
        <div class="xl:flex">
            <div class=" xl:ml-[50%] xl:pl-5 xl:w-1/3">
                <div class="text-dark text-opacity-60 text-simple">{!!$contentBlock->content!!}</div>
                <div class="mt-6 flex gap-1">
                    <a href="{{$block_link}}" class="md:min-w-[200px] app-button app-button--secondary">Узнать больше</a>
                    <a href="{{$block_link}}" class="app-button app-button--secondary app-button--square">+</a>
                </div>
            </div>
        </div>
        @if (isset($contentBlock->renderedChildren) && count($contentBlock->renderedChildren))
        <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3 lg:mt-20">
            @foreach($contentBlock->renderedChildren as $children)
                {!!$children!!}
            @endforeach
        </div>
        @endif
    </div>
</section>