@php
    $image = getItemByPivotKey($contentBlock->images, 'image')?->url;
@endphp

<section id='block-9ffeb974-c2c0-41a2-af02-269b88788ccd' class="app-image-block bg-stone-100 py-3 text-brand">
    <div class="centered">
        <div class="flex flex-col gap-5 md:flex-row md:items-end">
            <div class="md:w-1/3">
                @if (isset($image))
                    <img class="mx-auto" src="{{$image}}" alt="{{$contentBlock->link->subtitle}}">
                @endif
            </div>
            <div class="pb-5 md:flex-1 md:min-w-0">
                <h2 class="lg:ml-[25%]">{{$contentBlock->link->title}}</h2>
                <div class="text-simple text-simple--dark lg:ml-[50%]">{{$contentBlock->link->subtitle}}</div>
            </div>
        </div>
    </div>
</section>