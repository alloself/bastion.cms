@php
    $advantages = getItemByPivotKey($contentBlock->dataCollections, 'advantages')?->dataEntities;
    $block_bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
@endphp

@if (isset($advantages) && count($advantages))
<section id='block-9fed0d77-ba88-4728-9b4e-54f6da125b27' class="app-profits-block text-white bg-cover bg-no-repeat bg-center" style="background-image: url({{$block_bg}})">
    <div class="centered lg:px-0">
        <div class="md:grid md:grid-cols-2">
            @foreach ($advantages as $profit)
            @php
                $evenClass = $loop->index % 2 == 0 ? 'md:border-r' : '';
                $small_letters = getAttributeByKey($profit, 'small_letters')?->pivot->value;
            @endphp
            <div class="py-5 px-2.5 border-b border-light border-opacity-20 {{$evenClass}}">
                <div class="mb-8 xl:mb-[230px] text-simple text-simple--light">{{$profit->link->title}}</div>
                <div class="leading-[1.1] font-medium text-[48px] md:text-[72px] xl:text-[96px]">
                    {{$profit->link->subtitle}} 
                    <span class="text-[50%]">{{$small_letters}}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif