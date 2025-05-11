@php
    // Получаем все необходимые атрибуты продукта
    $price = getAttributeByKey($entity, 'price')?->pivot->value ?? '';
    $priceOld = getAttributeByKey($entity, 'price_old')?->pivot->value ?? '';
    $article = getAttributeByKey($entity, 'article')?->pivot->value ?? '';
    $barcode = getAttributeByKey($entity, 'barcode')?->pivot->value ?? '';
    $availability = getAttributeByKey($entity, 'availability')?->pivot->value ?? 0;
    $collection = getAttributeByKey($entity, 'collection')?->pivot->value ?? '';
    $manufacturer = getAttributeByKey($entity, 'manufacturer')?->pivot->value ?? '';
    
    // Получаем характеристики продукта для табов
    $style = getAttributeByKey($entity, 'style')?->pivot->value ?? '';
    $country = getAttributeByKey($entity, 'country')?->pivot->value ?? '';
    
    // Получаем изображения
    $images = $entity->images ?? collect([]);
    
    // Получаем заголовок продукта
    // Проверяем наличие связи dataEntityables с загруженной связью link
    $title = null;
    if ($entity->relationLoaded('dataEntityables') && $entity->dataEntityables->isNotEmpty()) {
        foreach ($entity->dataEntityables as $entityable) {
            if ($entityable->relationLoaded('link') && $entityable->link) {
                $title = $entityable->link->title;
                break;
            }
        }
    }
    // Если title не найден, используем fallback
    $title = $title ?? 'Название продукта';
    
    // Получаем коллекцию товаров
    $relatedProducts = isset($dataCollection) && isset($dataCollection->dataEntities) ? $dataCollection->dataEntities->take(4) : collect([]);
    
    // Получаем root коллекцию для категорий
    $rootCollection = $relations['root'] ?? null;
    $categoryProducts = isset($rootCollection) && isset($rootCollection->dataEntities) ? $rootCollection->dataEntities->take(4) : collect([]);
@endphp

<main class="product-view py-12 lg:py-20">
    <div class="centered">
        <div class="mb-10 md:flex lg:mb-24">
            <div class="flex-none md:w-[310px] lg:w-[400px] xl:w-[640px]">
                <div class="swiper js-product-item-slider select-none">
                    <div class="pointer-events-none absolute top-4 right-4 flex items-center gap-2 z-[10]">
                        @if($priceOld)
                        <div class="rounded-icon">
                            <div class="svg-icon svg-icon--nostyle">
                                <svg>
                                    <use xlink:href="#percent"></use>
                                </svg>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="swiper-wrapper">
                        @if($images->count() > 0)
                            @foreach($images as $image)
                                <div class="swiper-slide h-auto">
                                    <div class="relative flex items-center justify-center bg-neutral-100 h-full">
                                        <img src="{{ $image->url }}" alt="{{ $title }}">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="swiper-slide h-auto">
                                <div class="relative flex items-center justify-center bg-neutral-100 h-full">
                                    <img src="/public/img/no-image.png" alt="Нет изображения">
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($images->count() > 1)
                    <div class="slider-prev">
                        <div class="svg-icon">
                            <svg>
                                <use xlink:href="#arrow-left"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="slider-next">
                        <div class="svg-icon">
                            <svg>
                                <use xlink:href="#arrow-right"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="slider-pagination"></div>
                    @endif
                </div>
            </div>
            <div class="pt-10 md:pt-0 md:min-w-0 md:pl-5 md:flex md:flex-1 md:flex-col">
                <div class="mb-6 md:flex md:items-start">
                    <h1 class="mb-0">{{ $title }}</h1>
                    <div class="hidden lg:flex flex-none flex-col items-end md:pl-2">
                        <div class="rounded-icon">
                            <div class="svg-icon">
                                <svg>
                                    <use xlink:href="#heart-filled"></use>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-5 text-[20px] leading-[1.2] font-semibold xl:text-[28px]">{{ $price }}
                            ₽</div>
                        <a href="#" class="text-[10px] underline">Нашли дешевле?</a>
                    </div>
                </div>
                <div class="mb-8 text-[12px] uppercase text-dark text-opacity-60 leading-[1.4]">
                    @if($article)
                        <div>Артикул: {{ $article }}</div>
                    @endif
                    @if($barcode)
                        <div>Штрихкод: {{ $barcode }}</div>
                    @endif
                </div>
                <div class="mb-4">
                    <div
                        class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                        <div class="text-dark text-opacity-50">Наличие (на складе):</div>
                        <div>{{ $availability }} шт.</div>
                    </div>
                    @if($collection)
                    <div
                        class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                        <div class="text-dark text-opacity-50">Коллекция:</div>
                        <div>{{ $collection }}</div>
                    </div>
                    @endif
                    @if($manufacturer)
                    <div
                        class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                        <div class="text-dark text-opacity-50">Производитель:</div>
                        <div>{{ $manufacturer }}</div>
                    </div>
                    @endif
                </div>
                <a v-scroll-to="{target: '#product-chars', offsetY: 140}" href="#"
                    class="underline self-start text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">Все
                    характеристики</a>
                <div class="mt-auto pt-5 lg:pt-8">
                    <div
                        class="flex items-end gap-4 mb-5 text-dark text-opacity-50 text-[14px] leading-[1.3] lg:text-[20px] lg:font-semibold lg:gap-10 lg:mb-10">
                        <div class="rounded-icon mr-auto lg:hidden">
                            <div class="svg-icon">
                                <svg>
                                    <use xlink:href="#heart-filled"></use>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="mb-2.5">Количество:</div>
                            <countbuttons buttonsClass="w-[24px] h-[24px] lg:w-[44px] lg:h-[44px]"></countbuttons>
                        </div>
                        <div class="self-stretch">
                            <div class="mb-2.5">Цена:</div>
                            <div class="text-[20px] text-dark font-semibold leading-[1.1] lg:text-[36px]">{{ $price }}
                                ₽</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-stretch gap-2 lg:flex-row md:max-w-[80%] lg:max-w-none">
                        <button type="button" class="whitespace-nowrap app-button app-button--primary">Добавить в
                            корзину</button>
                        <button type="button" class="whitespace-nowrap app-button app-button--secondary">Купить в 1
                            клик</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="product-chars" class="pt-5 border-t border-neutral-alpha">
            <div class="h1">Характеристики</div>
            <AppTabs
                :items="[{ label: 'Основные', key: 'general' }, { label: 'Внешний вид', key: 'view' }, {
                    label: 'Габариты',
                    key: 'dimensions'
                }, { label: 'Электрика', key: 'electrics' }, {
                    label: 'Доп. информация',
                    key: 'add'
                }, { label: 'Размер упаковки', key: 'packing_size' }, ]">
                <template #general>
                    <div class="md:flex md:gap-5">
                        <div class="md:flex-1 md:min-w-0">
                            @if($manufacturer)
                            <div
                                class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                                <div class="text-dark text-opacity-50">Бренд:</div>
                                <div>{{ $manufacturer }}</div>
                            </div>
                            @endif
                            @if($style)
                            <div
                                class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                                <div class="text-dark text-opacity-50">Стиль:</div>
                                <div>{{ $style }}</div>
                            </div>
                            @endif
                            @if($country)
                            <div
                                class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                                <div class="text-dark text-opacity-50">Страна производства:</div>
                                <div>{{ $country }}</div>
                            </div>
                            @endif
                            @if($collection)
                            <div
                                class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                                <div class="text-dark text-opacity-50">Коллекция:</div>
                                <div>{{ $collection }}</div>
                            </div>
                            @endif
                            
                            {{-- Выводим все остальные атрибуты продукта --}}
                            @if(isset($entity->attributes) && $entity->attributes->count() > 0)
                                @foreach($entity->attributes as $attribute)
                                    @if(!in_array($attribute->key, ['price', 'price_old', 'article', 'barcode', 'availability', 'collection', 'manufacturer', 'style', 'country']))
                                    <div
                                        class="py-2 border-b border-neutral text-[14px] font-semibold leading-[1.1] tracking-[-0.28px] flex items-end justify-between lg:text-[20px]">
                                        <div class="text-dark text-opacity-50">{{ $attribute->name }}:</div>
                                        <div>{{ $attribute->pivot->value }}</div>
                                    </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <div class="md:flex-1 md:min-w-0 hidden md:block">
                            @if($images->count() > 0)
                                <img src="{{ $images[0]->url }}" alt="{{ $title }}">
                            @else
                                <img src="/public/img/no-image.png" alt="Нет изображения">
                            @endif
                        </div>
                    </div>
                </template>
                <template #view>
                    <h3>Внешний вид</h3>
                    <p>{{ getAttributeByKey($entity, 'description_view')?->pivot->value ?? 'Информация о внешнем виде отсутствует' }}</p>
                </template>
                <template #dimensions>
                    <h3>Габариты</h3>
                    <p>{{ getAttributeByKey($entity, 'description_dimensions')?->pivot->value ?? 'Информация о габаритах отсутствует' }}</p>
                </template>
                <template #electrics>
                    <h3>Электрика</h3>
                    <p>{{ getAttributeByKey($entity, 'description_electrics')?->pivot->value ?? 'Информация об электрике отсутствует' }}</p>
                </template>
                <template #add>
                    <h3>Доп. информация</h3>
                    <p>{{ getAttributeByKey($entity, 'description_additional')?->pivot->value ?? 'Дополнительная информация отсутствует' }}</p>
                </template>
                <template #packing_size>
                    <h3>Размер упаковки</h3>
                    <p>{{ getAttributeByKey($entity, 'description_packing')?->pivot->value ?? 'Информация о размере упаковки отсутствует' }}</p>
                </template>
            </AppTabs>
        </div>
        
        @if($relatedProducts->count() > 0)
        <div class="mt-8 pt-5 border-t border-neutral-alpha lg:mt-20">
            <div class="flex flex-col items-start gap-4 mb-8 md:mb-14 md:items-center md:justify-between md:flex-row">
                <div class="h1 mb-0">Товары из коллекции</div>
                <a href="#" class="app-button app-button--secondary">Смотреть все</a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-6 lg:gap-x-6 lg:gap-y-12">
                @foreach($relatedProducts as $product)
                    <x-product-item :product="$product" :dataCollection="$dataCollection"></x-product-item>
                @endforeach
            </div>
        </div>
        @endif
        
        @if($categoryProducts->count() > 0)
        <div class="mt-8 pt-5 border-t border-neutral-alpha lg:mt-20">
            <div class="flex flex-col items-start gap-4 mb-8 md:mb-14 md:items-center md:justify-between md:flex-row">
                <div class="h1 mb-0">Товары из категории</div>
                <a href="#" class="app-button app-button--secondary">Смотреть все</a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-6 lg:gap-x-6 lg:gap-y-12">
                @foreach($categoryProducts as $product)
                    <x-product-item :product="$product" :dataCollection="$rootCollection"></x-product-item>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</main> 