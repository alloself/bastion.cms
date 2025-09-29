<section id='block-9fed0ff0-2e7a-41e1-8fe3-949ce7c503ea' class="app-services-description py-8 bg-brand text-white">
    <div class="centered">
        <h2 class="h2 lg:max-w-[45%]"><?php echo e($contentBlock->link->title); ?></h2>
        <div class="lg:w-1/2 lg:ml-auto">
            <div class="text-white text-opacity-40 text-[13px] xl:max-w-[60%]">
                <?php echo $contentBlock->content; ?>

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
        <?php if(isset($contentBlock->renderedChildren) && count($contentBlock->renderedChildren)): ?>
        <div class="mt-12 lg:mt-16">
            <?php $__currentLoopData = $contentBlock->renderedChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $children): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $children; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/251e87cc4a1b0c45def2c8b0156bb207.blade.php ENDPATH**/ ?>