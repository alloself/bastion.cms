@php
    $team_list = getItemByPivotKey($contentBlock->dataCollections, 'team')?->dataEntities;
    $metric_1 = getItemByPivotKey($contentBlock->dataEntities, 'metric_1');
    $metric_2 = getItemByPivotKey($contentBlock->dataEntities, 'metric_2');
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<section id='block-9ffeb9fa-9c32-44ed-8899-8fe5ea12f3d6' class="app-about-employees bg-stone bg-opacity-30 py-8">
    <div class="centered">
        <div class="flex flex-col gap-4 mb-6 lg:flex-row">
            <div class="text-simple text-simple--dark lg:w-1/4">{{$contentBlock->link->subtitle}}</div>
            <h2 class="h4 mb-0 text-brand lg:w-3/4 lg:max-w-[75%]">{{$contentBlock->link->title}}</р>
        </div>
        <div class="text-simple text-simple--dark lg:pl-4 lg:ml-[50%] lg:columns-2 lg:gap-5">
            {!!$contentBlock->content!!}
        </div>

        @if (isset($metric_1) && isset($metric_2))
        <div class="mt-12 flex flex-col gap-8 md:gap-4 md:flex-row lg:mt-[150px] lg:pl-4 lg:ml-[50%]">
            @if (isset($metric_1))
            <div class="md:w-1/2">
                <div class="text-brand font-medium mb-1 leading-[1.0] text-[120px] xl:text-[160px]">{{$metric_1->link->subtitle}}</div>
                <div class="text-label text-label--dark">{{$metric_1->link->title}}</div>
            </div>
            @endif
            @if (isset($metric_2))
            <div class="md:w-1/2">
                <div class="text-brand font-medium mb-1 leading-[1.0] text-[120px] xl:text-[160px]">{{$metric_2->link->subtitle}}</div>
                <div class="text-label text-label--dark">{{$metric_2->link->title}}</div>
            </div>
            @endif
        </div>
        @endif

        @if(isset($team_list) && count($team_list))
        <div class="mt-8 pt-8 border-t border-brand border-opacity-20 grid gap-8 md:gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($team_list as $item)
                @php
                    $photo = getItemByPivotKey($item->images, 'photo')?->url;
                @endphp
                <div>
                    <div class="mb-5">
                        <img class="w-full" src="{{$photo}}" alt="{{$item->link->title}}">
                    </div>
                    <div class="text-brand text-[18px] font-medium mb-1">{{$item->link->title}}</div>
                    <div class="text-label text-label--dark">{{$item->link->subtitle}}</div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="py-12 flex gap-1 justify-center">
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
    </div>
</section>