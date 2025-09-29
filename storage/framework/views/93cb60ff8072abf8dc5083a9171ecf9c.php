<section id='block-9ffb1deb-eed0-4b64-86ae-76ebf98f6906' class="app-stages py-8 lg:pb-16 bg-brand text-white">
    <div class="centered">
        <div class="xl:flex">
            <div class="text-simple text-white text-opacity-60 mb-6 xl:mb-0 xl:flex-none xl:pr-4 xl:w-1/3"><?php echo e($contentBlock->link->subtitle); ?></div>
            <div class="xl:min-w-0 xl:flex-1">
                <h2 class="mb-10 xl:mb-16 xl:max-w-[80%]"><?php echo e($contentBlock->link->title); ?></h2>
            </div>
        </div>
        <?php if(isset($contentBlock->renderedChildren) && count($contentBlock->renderedChildren)): ?>
        <div class="xl:w-1/2 xl:mx-auto space-y-4">
            <?php $__currentLoopData = $contentBlock->renderedChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $children): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $children; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/a446d298609b20c83f231586c6155a14.blade.php ENDPATH**/ ?>