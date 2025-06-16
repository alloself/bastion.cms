@props(['product', 'addClass' => '', 'dataCollection' => null])

@php
    $price = $product ? getAttributeByKey($product, 'price')?->pivot->value : '';
    $priceOld = $product ? getAttributeByKey($product, 'price_old')?->pivot->value : '';
    $image = $product && isset($product->images) && count($product->images) ? $product->images[0]->url : '';

    // Если есть dataEntityables, используем их, иначе пробуем получить из dataCollection
    $productLink = $product && isset($product->dataEntityables) && count($product->dataEntityables) ? 
                  $product->dataEntityables[0]->link ?? null : null;

    // Получаем dataCollection либо из переданного параметра, либо из первого dataEntityable
    $productDataCollection = $dataCollection ?? 
                            ($product && isset($product->dataEntityables) && count($product->dataEntityables) ? 
                            $product->dataEntityables[0]->dataEntityable ?? null : null);
@endphp

<div class="product-item relative {{ $addClass }}">
    <div
        class="relative mb-2.5 min-h-[156px] bg-white flex items-center justify-center transition-all duration-300 lg:min-h-[310px] hover:shadow-lg">
        <div class="pointer-events-none absolute z-[10] top-2 right-2 flex items-center gap-2 md:top-4 md:right-4">
            @if(false)
            <div class="rounded-icon bg-white w-8 h-8 md:w-11 md:h-11">
                <div class="svg-icon svg-icon--nostyle">
                    <svg>
                        <use xlink:href="#percent"></use>
                    </svg>
                </div>
            </div>
            <div class="rounded-icon bg-white w-8 h-8 md:w-11 md:h-11">
                <div class="svg-icon">
                    <svg>
                        <use xlink:href="#spot"></use>
                    </svg>
                </div>
            </div>
            @endif
        </div>
        @if($productLink && isset($productLink->url))
        <a href="{{ $productLink->url }}" class="absolute left-0 right-0 top-0 bottom-0 z-[5]"></a>
        @endif
        @if($image)
        <img class="w-full" src="{{ $image }}" alt="">
        @endif
    </div>
    <div class="text-[12px] leading-[1.4] uppercase text-dark text-opacity-50 mb-4">
        {{$productDataCollection?->link?->title}}
    </div>
    <div class="text-[14px] text-dark font-semibold leading-[1.1] md:text-[20px]"> {{ $productLink?->title }}</div>
    <div
        class="flex flex-wrap justify-end gap-2.5 mt-1.5 text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] md:text-[20px]">
        @if ($priceOld && $priceOld != $price)
            <div class="opacity-40 line-through">{{ number_format($priceOld, 0, ',', ' ') }} ₽</div>
        @endif
        @if ($price)
            <div>{{ number_format($price, 0, ',', ' ') }} ₽</div>
        @endif
    </div>
</div>
