<?php
    $bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
?>

<section id='block-9ffafbfb-4f27-427b-af62-7f6ebcd024eb' class="app-services-head-contentBlock text-white py-8 bg-cover bg-no-repeat bg-center" style="background-image: url(<?php echo e($bg); ?>);">
    <div class="centered">
        <div class="pt-[140px] md:ml-[25%] md:w-3/4 lg:ml-[50%] lg:w-1/2 lg:pt-[330px]">
            <h1 class="h2 lg:mb-14"><?php echo e($contentBlock->link->title); ?></h1>
            <div class="p-5 bg-stone-100">
                <div class="text-dark text-opacity-60 text-simple"><?php echo $contentBlock->content; ?></div>
                <?php if(isset($link)): ?>
                <div class="flex justify-end gap-1 mt-8" style="justify-content: end;">
                    <a href="<?php echo e($link); ?>" class="min-w-[200px] app-button app-button--secondary">Узнать больше</a>
                    <a href="<?php echo e($link); ?>" class="app-button app-button--secondary app-button--square">+</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/a08871152adce55dbfc83df9c3a6d6d0.blade.php ENDPATH**/ ?>