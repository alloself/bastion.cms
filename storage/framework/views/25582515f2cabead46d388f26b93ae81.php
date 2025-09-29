<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'data',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'data',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $block = $data;
    $icon_sprite = getAttributeByKey($block, 'icon_name_sprite')?->pivot->value;
    $bg = getItemByPivotKey($block->images, 'bg')?->path;
?>

<div class="service-card <?php echo e(empty($bg) ? 'service-card--noimage' : ''); ?>">
    <div class="service-card__bg" style="background-image: url(<?php echo e($bg); ?>);"></div>
    <div class="service-card__title"><?php echo e($block->link->title); ?></div>
    <div class="service-card__middle">
        <?php if(isset($icon_sprite)): ?>
        <div class="service-card__icon">
            <div class="svg-icon">
                <svg><use xlink:href="#<?php echo e($icon_sprite); ?>"></use></svg>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="service-card__tooltip">
        <div class="svg-icon">
            <svg><use xlink:href="#alert"></use></svg>
        </div>
    </div>
    <?php if(!empty($block->content)): ?>
    <div class="service-card__content text-simple">
        <?php echo $block->content; ?>

    </div>
    <?php endif; ?>
</div><?php /**PATH /var/www/resources/views/components/service-card.blade.php ENDPATH**/ ?>