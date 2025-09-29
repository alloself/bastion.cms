<?php
    $icon_sprite = getAttributeByKey($contentBlock, 'icon_name_sprite')?->pivot->value;
?>

<div id='block-9fee6c29-3eb6-4608-949f-8b66199655f8' class="app-services-desc-item mb-6">
    <div class="flex items-center gap-3 lg:gap-10 lg:items-end">
        <div class="size-12 flex-none flex items-center justify-center rounded-md border border-light border-opacity-30 lg:size-[70px]">
            <?php if(isset($icon_sprite)): ?>
            <div class="svg-icon text-primary size-5">
                <svg><use xlink:href="#<?php echo e($icon_sprite); ?>"></use></svg>
            </div>
            <?php endif; ?>
        </div>
        <div class="font-medium leading-[1.2] text-[22px] lg:flex-1 lg:text-[28px] lg:pb-2 lg:border-b lg:border-opacity-30 lg:border-light"><?php echo e($contentBlock->link->title); ?></div>
    </div>
    <div class="text-simple text-white text-opacity-40 pt-4 lg:max-w-[30%] lg:ml-[50%]">
        <?php echo $contentBlock->content; ?>

    </div>
</div><?php /**PATH /var/www/storage/framework/views/20249322bdb5cbe6ce6113a8825ad55a.blade.php ENDPATH**/ ?>