<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'collection',
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
    'collection',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $paginator = $collection?->dataEntities;

    if (! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        return;
    }

    $key = $collection?->pivot?->key;

    $total = $paginator->total();
    $per_page = $paginator->perPage();
    $last_page = $paginator->lastPage();
    $current_page = $paginator->currentPage();

    $prev_page_url = $paginator->previousPageUrl();
    $next_page_url = $paginator->nextPageUrl();
    $current_path = Request::segment(1);

    $prev_link = "/". $current_path . "?" . $key . "_page=" . ($current_page - 1);
    $next_link = "/". $current_path . "?" . $key . "_page=" . ($current_page + 1);
?>

<div class="flex flex-wrap justify-center gap-1 mt-10">
    <?php if(isset($prev_page_url)): ?> 
        <a href="<?php echo e($prev_link); ?>" class="app-button app-button--primary">Назад</a>
    <?php endif; ?>
    <?php if(isset($next_page_url)): ?>
        <a href="<?php echo e($next_link); ?>" class="ml-3 app-button app-button--light">Показать еще</a>
        <a href="<?php echo e($next_link); ?>" class="app-button app-button--light app-button--square">+</a>
    <?php endif; ?>
</div><?php /**PATH /var/www/resources/views/components/pagination.blade.php ENDPATH**/ ?>