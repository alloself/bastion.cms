<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['data' => []]));

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

foreach (array_filter((['data' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $title = $data->link->title;
    $subtitle = $data->link->subtitle;
    $image = getItemByPivotKey($data->images, 'image')?->url;
    $url = false; //$data->link->url;
?>

<div class="p-3 bg-stone-100 flex flex-col gap-3 relative transition-all md:p-5 hover:bg-stone-200">
    <?php if($url): ?>
        <a href="<?php echo e($url); ?>" class="absolute inset-0"></a>
    <?php endif; ?>
    <div class="text-[18px] font-medium"><?php echo e($title); ?></div>
    <div class="mb-5 text-[13px] text-brand/30"><?php echo e($subtitle); ?></div>
    <div class="mx-auto max-w-full w-[160px] h-[190x] lg:w-[214px] lg:h-[266px]">
        <img class="w-full h-full object-cover" src="<?php echo e($image); ?>" alt="<?php echo e($title); ?>" />
    </div>
</div>
<?php /**PATH /var/www/resources/views/components/hit-new-product.blade.php ENDPATH**/ ?>