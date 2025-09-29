<?php
    $image = getItemByPivotKey($contentBlock->images, 'image')?->url;
?>

<section id='block-9ffaf957-5d67-4676-bd9f-d96e01b953ca' class="app-objects-type py-2.5">
    <div class="centered">
        <div class="md:flex">
            <div class="py-5 md:w-1/2 md:pr-5 xl:w-3/4">
                <div class="xl:w-1/2">
                    <h2><?php echo e($contentBlock->link->title); ?></h2>
                    <div class="text-simple text-brand text-opacity-60"><?php echo $contentBlock->content; ?></div>
                    <div class="mt-14 space-y-8 xl:mt-[80px]">
                        <?php if(isset($contentBlock->renderedChildren) && count($contentBlock->renderedChildren)): ?>
                            <?php $__currentLoopData = $contentBlock->renderedChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $children): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $children; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if(isset($image)): ?>
            <div class="mt-10 md:mt-0 md:w-1/2 xl:w-auto">
                <img src="<?php echo e($image); ?>" alt="">
            </div>
            <?php endif; ?>
        </div>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/8862892a4b99a71b7c2f13b75d588aec.blade.php ENDPATH**/ ?>