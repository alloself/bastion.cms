<section id='block-9fed0ff0-2e7a-41e1-8fe3-949ce7c503ea' class="app-services-description py-8 bg-brand text-white">
    <div class="centered">
        <h2 class="h2 lg:max-w-[45%]">{{$contentBlock->link->title}}</h2>
        <div class="lg:w-1/2 lg:ml-auto">
            <div class="text-white text-opacity-40 text-[13px] xl:max-w-[60%]">
                {!!$contentBlock->content!!}
            </div>
            <div class="flex gap-1 mt-6">
                <button 
                    v-modal-call="{name: 'callbackModal'}" 
                    type="button" 
                    class="min-w-[200px] app-button app-button--secondary"
                >Обсудить проект</button>
                <button 
                    v-modal-call="{name: 'callbackModal'}" 
                    type="button" 
                    class="app-button app-button--secondary app-button--square"
                >+</button>
            </div>
        </div>
        @if (isset($contentBlock->renderedChildren) && count($contentBlock->renderedChildren))
        <div class="mt-12 lg:mt-16">
            @foreach($contentBlock->renderedChildren as $children)
            {!!$children!!}
            @endforeach
        </div>
        @endif
    </div>
</section>