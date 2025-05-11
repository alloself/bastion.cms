@props([
    'addClass' => '',
    'products' => [],
])

<div class="swiper w-full js-tape-slider {{ $addClass }}">
    @if (isset($products) && count($products))
        <div class="swiper-wrapper">
            @foreach ($products as $product)
                @php
                    $image = count($product->images ?? []) ? $product->images[0]->url : '';
                    $link = $product->dataEntityables[0]->link ?? null;
                @endphp
                @if ($link && $link->url)
                    <a href="{{ $link?->url }}"
                            class="swiper-slide !w-[160px] !h-[200px] py-3 px-2 md:!w-[220px] md:!h-[240px] bg-no-repeat bg-contain bg-top bg-clip-content"
                        style="background-image: url( {{ $image }} );">
                    </a>
                @else
                    <div class="swiper-slide !w-[160px] !h-[200px] py-3 px-2 md:!w-[220px] md:!h-[240px] bg-no-repeat bg-contain bg-top bg-clip-content"
                        style="background-image: url( {{ $image }} );">
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
