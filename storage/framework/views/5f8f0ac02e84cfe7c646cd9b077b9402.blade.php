@php
    $services_list = getItemByPivotKey($footer->dataCollections, 'services')?->dataEntities;
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<section id='block-9ffaf67a-26d7-4940-87cf-3bbc9c39f4b4' class="app-services-list py-8 bg-stone bg-opacity-30 lg:py-16">
    <div class="centered">
        <div class="mb-8 md:flex lg:mb-14">
            <div class="mb-3 text-simple text-dark text-opacity-60 md:mb-0 md:w-1/2">{{$contentBlock->link->subtitle}}</div>
            <h2 class="h2 mb-0 md:w-1/2 md:ml-4 lg:w-1/3">{{$contentBlock->link->title}}</h2>
        </div>
        @if (isset($services_list) && count($services_list))
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($services_list as $service_item)
                <x-service-card :data="$service_item"></x-service-card>                
            @endforeach
        </div>
        @endif
        <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-1 mt-12 lg:mt-[100px]">
            <button
                type="button"
                class="app-button app-button--primary"
                v-modal-call="{name: 'callbackModal'}"
            >Заказать услугу</button>
            <a href="{{$link}}" class="app-button app-button--secondary">Смотреть кейсы</a>
            <a href="{{$link}}" class="hidden app-button app-button--secondary app-button--square sm:inline-flex">+</a>
        </div>
    </div>
</section>