<section id='block-9ffeb6a8-4621-4ab5-b893-cca63b06dc01' class="app-about-head-block pt-[100px] lg:pt-[140px] pb-10 lg:pb-16">
    <div class="centered">
        <div class="xl:w-1/2">
            <div class="text-simple text-brand text-opacity-60 mb-2"><?php echo e($block->link->subtitle); ?></div>
            <h1 class="h2"><?php echo e($block->link->title); ?></h1>
            <div class="flex gap-1">
                <button 
                    v-modal-call="{name: 'callbackModal'}"
                    class="app-button app-button--secondary min-w-[200px]"
                >Выбрать услугу</button>
                <button 
                    v-modal-call="{name: 'callbackModal'}"
                    class="app-button app-button--square app-button--secondary"
                >+</button>
            </div>
        </div>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/ed1b78fd7b9e2bc80faaebaaf9675e15.blade.php ENDPATH**/ ?>