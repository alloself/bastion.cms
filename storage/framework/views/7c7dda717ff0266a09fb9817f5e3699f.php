<?php
    $icon_sprite = getAttributeByKey($contentBlock, 'icon_name_sprite')?->pivot->value;
    $bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
?>

<div id='block-9fee6b34-58ed-44da-819e-eb37f2f3bdea' class="service-card <?php echo e(empty($bg) ? 'service-card--noimage' : ''); ?>">
    <div class="service-card__bg" style="background-image: url(<?php echo e($bg); ?>);"></div>
    <div class="service-card__title"><?php echo e($contentBlock->link->title); ?></div>
    <?php if(isset($icon_sprite)): ?>
    <div class="service-card__middle">
        <div class="service-card__icon">
            <div class="svg-icon">
                <svg><use xlink:href="#<?php echo e($icon_sprite); ?>"></use></svg>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="service-card__tooltip">
        <div class="svg-icon">
            <svg><use xlink:href="#alert"></use></svg>
        </div>
    </div>
    <?php if(!empty($contentBlock->content)): ?>
    <div class="service-card__content text-simple">
        <?php echo $contentBlock->content; ?>

    </div>
    <?php endif; ?>
</div><?php /**PATH /var/www/storage/framework/views/e3d0f5eaa8276b24c3347f0f58462ca9.blade.php ENDPATH**/ ?>