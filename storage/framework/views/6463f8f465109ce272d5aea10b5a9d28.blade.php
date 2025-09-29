@php
    $contentBlock_bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
    $year = getAttributeByKey($contentBlock, 'year')?->pivot->value;
    $address = getAttributeByKey($contentBlock, 'address')?->pivot->value;
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
    $contract_sum = getAttributeByKey($contentBlock, 'contract_sum')?->pivot->value;
@endphp

<section id='block-9ffb26b6-21ce-4f23-a377-691a5b5c3896'
    class="app-completed-project py-8 bg-cover bg-no-repeat bg-center" 
    style="background-image: url({{$contentBlock_bg}})"
>
    <div class="centered">
        <div class="lg:flex lg:items-start">
            <div class="text-label text-white mb-5 lg:pr-3 lg:mb-0 lg:w-1/2">{{$contentBlock->link->subtitle}}</div>
            <div class="lg:w-1/2">
                <div class="bg-light p-5">
                    <div class="flex items-end gap-4 pb-4 mb-4 border-b border-brand border-opacity-20">
                        <div class="h4 text-brand mb-0">{{$contentBlock->link->title}}</div>
                        <div class="text-label text-dark text-opacity-40 ml-auto">{{$year}}</div>
                    </div>
                    <div class="text-simple text-brand text-opacity-60 mb-4">
                        {!!$contentBlock->content!!}
                    </div>
                    <div class="text-label text-dark text-opacity-40 mb-4">{{$address}}</div>
                    <div class="flex items-end gap-4">
                        <div class="text-[16px] font-medium text-brand">{{$contract_sum}}</div>
                        @if (isset($link))
                        <a href="{{$link}}" class="flex-none border border-brand border-opacity-20 ml-auto flex items-center justify-center size-12 lg:size-[70px]">
                            <div class="svg-icon size-6">
                                <svg><use xlink:href="#arrow-right"></use></svg>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-1 justify-center mt-10 lg:mt-[200px] xl:mt-[400px]">
            <button 
                v-modal-call="{name: 'callbackModal'}" 
                type="button" 
                class="min-w-[200px] app-button app-button--primary"
            >Обсудить проект</button>
            <button 
                v-modal-call="{name: 'callbackModal'}" 
                type="button" 
                class="app-button app-button--primary app-button--square"
            >+</button>
        </div>
    </div>
</section>