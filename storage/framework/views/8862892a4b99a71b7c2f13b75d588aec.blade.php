@php
    $image = getItemByPivotKey($contentBlock->images, 'image')?->url;
@endphp

<section id='block-9ffaf957-5d67-4676-bd9f-d96e01b953ca' class="app-objects-type py-2.5">
    <div class="centered">
        <div class="md:flex">
            <div class="py-5 md:w-1/2 md:pr-5 xl:w-3/4">
                <div class="xl:w-1/2">
                    <h2>{{$contentBlock->link->title}}</h2>
                    <div class="text-simple text-brand text-opacity-60">{!!$contentBlock->content!!}</div>
                    <div class="mt-14 space-y-8 xl:mt-[80px]">
                        @if (isset($contentBlock->renderedChildren) && count($contentBlock->renderedChildren))
                            @foreach($contentBlock->renderedChildren as $children)
                                {!!$children!!}
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @if (isset($image))
            <div class="mt-10 md:mt-0 md:w-1/2 xl:w-auto">
                <img src="{{$image}}" alt="">
            </div>
            @endif
        </div>
    </div>
</section>