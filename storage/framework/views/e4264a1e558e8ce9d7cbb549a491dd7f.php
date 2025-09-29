<?php
    $icon_sprite = getAttributeByKey($contentBlock, 'icon_name_sprite')?->pivot->value;
?>

<div id='block-9ffb255d-2813-4f84-8497-94aa63bc96b5' class="flex gap-5 items-end">
    <div class="flex-none size-14 md:size-[70px] flex items-center justify-center border border-light border-opacity-20 rounded-md">
        <div class="svg-icon text-stone-200 size-5 <?php echo e($icon_sprite === 'result' ? 'svg-icon--stroke' : ''); ?>">
            <svg><use xlink:href="#<?php echo e($icon_sprite); ?>"></use></svg>
        </div>
    </div>
    <div class="pb-2 min-w-0 flex-1 border-b border-light border-opacity-20">
        <div class="h4 mb-1"><?php echo e($contentBlock->link->title); ?></div>
        <div class="text-simple text-white text-opacity-60"><?php echo e($contentBlock->link->subtitle); ?></div>
    </div>
</div><?php /**PATH /var/www/storage/framework/views/27d1e679791dcf38ce412972f6cb8c48.blade.php ENDPATH**/ ?>