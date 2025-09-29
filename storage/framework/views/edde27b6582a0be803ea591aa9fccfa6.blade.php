@php
    $collection = getItemByPivotKey($contentBlock->dataCollections, 'cases');
    $cases_items = $collection?->dataEntities;
@endphp

<section id='block-9ffb2ab7-3597-4368-9da7-b82246afb479' class="app-cases-list pt-[90px] lg:pt-[120px] pb-8 bg-stone-200">
    <div class="centered">
        <div class="md:flex mb-8">
            <div class="mb-3 text-simple text-dark text-opacity-60 md:mb-0 md:w-1/2">{{$contentBlock->link->subtitle}}</div>
            <h1 class="h2 mb-0 md:w-1/2 lg:w-1/3">{{$contentBlock->link->title}}</h1>
        </div>
        <div class="text-simple text-dark text-opacity-60 md:ml-[50%] lg:w-1/3">
            {!!$contentBlock->content!!}
        </div>
        @if (isset($cases_items) && count($cases_items))
        <nav class="mt-10 md:mt-16">
            <ul>
                @foreach ($cases_items as $case)
                @php
                    $case_year = getAttributeByKey($case, 'year')?->pivot->value;
                @endphp
                <li class="pb-2 mb-3 border-b border-dark border-opacity-10 md:flex md:items-end md:flex-gap-4 md:flex-wrap">
                    <div class="md:w-1/2">
                        <a class="h4 mb-3 text-dark decor-link md:mb-0" href="{{$case->link->path}}">{{$case->link->title}}</a>
                    </div>
                    <div class="md:w-1/2 lg:flex lg:gap-4">
                        <div class="mb-2 text-simple text-dark text-opacity-60 md:min-w-0 md:flex-1 lg:mb-0">{{$case->link->subtitle}}</div>
                        <div class="whitespace-nowrap text-label text-dark text-opacity-60">{{$case_year}}</div>
                    </div>
                </li>
                @endforeach
            </ul>
        </nav>
        <x-pagination 
            :collection="$collection"
        ></x-pagination>
        @endif
    </div>
</section>