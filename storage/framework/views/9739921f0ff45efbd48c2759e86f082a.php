<?php
    $advantages = getItemByPivotKey($contentBlock->dataCollections, 'advantages')?->dataEntities;
    $block_bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
?>

<?php if(isset($advantages) && count($advantages)): ?>
<section id='block-9fed0d77-ba88-4728-9b4e-54f6da125b27' class="app-profits-block text-white bg-cover bg-no-repeat bg-center" style="background-image: url(<?php echo e($block_bg); ?>)">
    <div class="centered lg:px-0">
        <div class="md:grid md:grid-cols-2">
            <?php $__currentLoopData = $advantages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $evenClass = $loop->index % 2 == 0 ? 'md:border-r' : '';
                $small_letters = getAttributeByKey($profit, 'small_letters')?->pivot->value;
            ?>
            <div class="py-5 px-2.5 border-b border-light border-opacity-20 <?php echo e($evenClass); ?>">
                <div class="mb-8 xl:mb-[230px] text-simple text-simple--light"><?php echo e($profit->link->title); ?></div>
                <div class="leading-[1.1] font-medium text-[48px] md:text-[72px] xl:text-[96px]">
                    <?php echo e($profit->link->subtitle); ?> 
                    <span class="text-[50%]"><?php echo e($small_letters); ?></span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?><?php /**PATH /var/www/storage/framework/views/5cb76a9c80defe2d0047f0d61bef5d8a.blade.php ENDPATH**/ ?>