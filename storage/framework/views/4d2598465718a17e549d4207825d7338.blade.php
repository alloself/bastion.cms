@php
    $services_list = getItemByPivotKey($header->dataCollections, 'services')?->dataEntities;
    $block_link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
@endphp

<section id='block-9fed0dc4-cce8-4054-9cc1-b2a8b28ec53a' class="app-services-list-main py-8 bg-stone bg-opacity-30">
    <div class="centered">
        <div class="xl:flex">
            <div class="text-simple text-dark text-opacity-60 mb-6 xl:mb-0 xl:flex-none xl:pr-4 xl:w-1/3">{{$contentBlock->link->subtitle}}</div>
            <div class="xl:min-w-0 xl:flex-1">
                <h2 class="mb-10 xl:mb-16 xl:max-w-[80%]">{{$contentBlock->link->title}}</h2>
                @if (isset($services_list) && count($services_list))
                <nav>
                    <ul>
                        @foreach ($services_list as $service_item)
                        @php
                            $item_link = getAttributeByKey($service_item, 'link')?->pivot->value;
                            $iteration = $loop->index < 10 ? "0" . $loop->index + 1 : $loop->index + 1  
                        @endphp
                        <li class="mb-6 border-b border-dark border-opacity-20 pb-1.5 flex gap-4 items-end xl:gap-14">
                            <span class="text-label text-dark text-opacity-40">{{$iteration}}</span>
                            <a href="{{$item_link}}" class="text-brand font-medium text-[22px] decor-link xl:text-[28px]">{{$service_item->link->title}}</a>
                        </li>
                        @endforeach
                    </ul>
                </nav>
                @endif
                <div class="mt-14 flex flex-wrap gap-1 lg:mt-20">
                    <a href="{{$block_link}}" class="md:min-w-[200px] app-button app-button--primary">Узнать больше</a>
                    <button type="button" 
                        v-modal-call="{name: 'callbackModal'}" 
                        class="md:min-w-[200px] app-button app-button--light"
                    >Обсудить проект</button>
                    <button type="button" 
                        v-modal-call="{name: 'callbackModal'}" 
                        class="app-button app-button--light app-button--square"
                    >+</button>
                </div>
            </div>
        </div>
    </div>
</section>