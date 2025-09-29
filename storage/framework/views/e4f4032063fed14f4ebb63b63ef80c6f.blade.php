@php
    $contentBlock_bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
    $reviews_list = getItemByPivotKey($contentBlock->dataCollections, 'reviews')?->dataEntities;
@endphp

@if (isset($reviews_list) && count($reviews_list))
<section id='block-9fed101a-8d9e-4c95-8b43-3031485ce570'
    class="app-reviews-block relative flex py-12 bg-cover bg-no-repeat bg-center lg:min-h-[780px]"
    style="background-image: url({{$contentBlock_bg}})"
>
    <div class="swiper static js-reviews-slider lg:my-auto">
        <div class="swiper-wrapper">
            @foreach( $reviews_list as $review_item )
                @php
                    $logo = getItemByPivotKey($review_item->images, 'logo')?->url;
                    $thanks_letter = getItemByPivotKey($review_item->files, 'thanks_letter')?->url;
                @endphp
                <div class="swiper-slide">
                    <div class="centered px-10">
                        <div class="bg-light p-5 max-w-[720px] mx-auto">
                            <div class="flex gap-4 pb-5 mb-5 border-b border-brand border-opacity-20">
                                <div class="flex-1">
                                    <div class="text-label text-brand text-opacity-40 mb-3">{{ $loop->index + 1}} / {{count($reviews_list)}}</div>
                                    <div class="h4 text-brand mb-0">{{$review_item->link->title}}</div>
                                </div>
                                @if (isset($logo))
                                <div class="size-[70px] border border-brand border-opacity-20">
                                    <img class="object-cover" src="{{$logo}}" alt="">
                                </div>
                                @endif
                            </div>
                            <div class="text-simple text-brand text-opacity-60">
                                {!!$review_item->content!!}
                            </div>
                            @if (isset($logo))
                            <div class="text-[14px] font-medium mt-8 md:text-[16px]">
                                <a class="underline hover:no-underline" target="_blank" href="{{$thanks_letter}}">Открыть благодарственное письмо</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="slider-pagination bottom-4 lg:bottom-10 text-primary"></div>
        <div class="slider-prev text-light">
            <div class="svg-icon">
                <svg><use xlink:href="#arrow-left"></use></svg>
            </div>
        </div>
        <div class="slider-next text-light">
            <div class="svg-icon">
                <svg><use xlink:href="#arrow-right"></use></svg>
            </div>
        </div>
    </div>
</section>
@endif