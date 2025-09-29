<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'navigation',
    'global'
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
    'navigation',
    'global'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $phone = getAttributeByKey($global, 'phone')?->pivot->value;
    $phone_link = $phone ? preg_replace("/[^0-9]/", "", $phone) : '';  
    $email = getAttributeByKey($global, 'email')?->pivot->value;
?>

<offcanvas name="mobileMenu" v-cloak>
    <?php if(isset($navigation) && count($navigation)): ?>
    <nav class="mb-6">
        <ul class="text-[16px] font-normal">
            <?php $__currentLoopData = $navigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nav_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $nav_link = getAttributeByKey($nav_item, 'link')?->pivot->value;
                ?>
                <li><a href="<?php echo e($nav_link); ?>" class="decor-link"><?php echo e($nav_item->link->title); ?></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </nav>
    <?php endif; ?>
    <?php if(isset($phone) && isset($phone_link)): ?>
    <div class="mb-1">
        <a 
            href="tel:<?php echo e($phone_link); ?>" 
            class="w-full app-button app-button--secondary"
        ><?php echo e($phone); ?></a>
    </div>
    <?php endif; ?>
    <div class="flex gap-1 mb-6">
        <button 
            v-modal-call="{name: 'callbackModal'}"
            class="app-button app-button--primary flex-1"
        >Связаться</button>
        <button 
            v-modal-call="{name: 'callbackModal'}"
            class="app-button app-button--square app-button--primary"
        >+</button>
    </div>
    <div>
        <a class="decor-link font-bold" href="mailto:<?php echo e($email); ?>"><?php echo e($email); ?></a>
    </div>
</offcanvas><?php /**PATH /var/www/resources/views/components/mobile-menu.blade.php ENDPATH**/ ?>