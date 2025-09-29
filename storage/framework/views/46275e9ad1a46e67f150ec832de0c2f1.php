<?php
    $bg = getItemByPivotKey($block->images, 'bg')?->path;
    $advantages = getItemByPivotKey($block->dataCollections, 'advantages')?->dataEntities;
?>

<?php if(isset($advantages) && count($advantages)): ?>
<section id='block-9ffeb721-a752-4e34-bbe8-7f17eb5d7af6' class="app-about-profits relative z-[1] text-light">
    <div class="absolute inset-0 pointer-events-none -z-[1] bg-cover bg-no-repeat bg-center" style="background-image: url(<?php echo e($bg); ?>);"></div>
    <div class="centered">
        <?php $__currentLoopData = $advantages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $evenClass = $loop->index % 2 == 0 ? 'md:border-r' : '';
                $small_letters = getAttributeByKey($profit, 'small_letters')?->pivot->value;
            ?>
            <div class="border-b border-light border-opacity-20">
                <div class="pt-10 p-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between lg:border-l lg:border-light lg:border-opacity-20 lg:ml-[50%] lg:w-1/2">
                    <div class="flex items-end">
                        <div class="leading-[1] font-medium text-[72px] md:text-[96px] xl:text-[160px]">
                        <?php echo e($profit->link->subtitle); ?>

                        </div>
                        <div class="pb-3 text-[22px] md:text-[28px]"><?php echo e($small_letters); ?></div>
                    </div>
                    <div class="text-simple text-light text-opacity-40"><?php echo e($profit->link->title); ?></div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?><?php /**PATH /var/www/storage/framework/views/af760de5fd1bdd1128608b348f477f25.blade.php ENDPATH**/ ?>