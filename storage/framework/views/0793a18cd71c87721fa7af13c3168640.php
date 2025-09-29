<?php
    $icon_sprite = getAttributeByKey($contentBlock, 'icon_name_sprite')?->pivot->value;
?>

<div id='block-9fee8942-2fb4-4808-b78e-acb761a6270a' class="app-services-desc-item mb-6">
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
</div><?php /**PATH /var/www/storage/framework/views/367d850678fe2968d0084e7eafceefe9.blade.php ENDPATH**/ ?>