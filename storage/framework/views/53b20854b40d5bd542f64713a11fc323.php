<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <?php echo csrf_field(); ?>
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="keywords" content="<?php echo e($entity->keywords ? $entity->keywords : ''); ?>" />
    <meta name="description" content="<?php echo e($entity->description ? $entity->description : ''); ?>" />
    <title><?php echo e($entity->link->title ?? $entity->link->title); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/site/index.scss', 'resources/ts/site/index.ts']); ?>
</head>
    <?php
        $global = getItemByPivotKey($header->dataCollections, 'global');
        $navigation = getItemByPivotKey($header->dataCollections, 'navigation')?->dataEntities;
    ?>
    <body>
        <div id="app" class="app-wrapper">
            <div class="stripes">
                <div class="stripes__item"></div>
                <div class="stripes__item"></div>
                <div class="stripes__item"></div>
            </div>
            <?php if(count($contentBlocks)): ?>
                <?php $__currentLoopData = $contentBlocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $block; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal382ffb4e125af6203213609160accaa9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal382ffb4e125af6203213609160accaa9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-menu','data' => ['navigation' => $navigation,'global' => $global]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['navigation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navigation),'global' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($global)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal382ffb4e125af6203213609160accaa9)): ?>
<?php $attributes = $__attributesOriginal382ffb4e125af6203213609160accaa9; ?>
<?php unset($__attributesOriginal382ffb4e125af6203213609160accaa9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal382ffb4e125af6203213609160accaa9)): ?>
<?php $component = $__componentOriginal382ffb4e125af6203213609160accaa9; ?>
<?php unset($__componentOriginal382ffb4e125af6203213609160accaa9); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginala62a68fbe05a625d06a00d401e3c8399 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala62a68fbe05a625d06a00d401e3c8399 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modals','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modals'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala62a68fbe05a625d06a00d401e3c8399)): ?>
<?php $attributes = $__attributesOriginala62a68fbe05a625d06a00d401e3c8399; ?>
<?php unset($__attributesOriginala62a68fbe05a625d06a00d401e3c8399); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala62a68fbe05a625d06a00d401e3c8399)): ?>
<?php $component = $__componentOriginala62a68fbe05a625d06a00d401e3c8399; ?>
<?php unset($__componentOriginala62a68fbe05a625d06a00d401e3c8399); ?>
<?php endif; ?>
            <notifications></notifications>
        </div>
        <?php if (isset($component)) { $__componentOriginal4a63d2bc94d0926843877ab3b734b550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4a63d2bc94d0926843877ab3b734b550 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.svg-sprite','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('svg-sprite'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4a63d2bc94d0926843877ab3b734b550)): ?>
<?php $attributes = $__attributesOriginal4a63d2bc94d0926843877ab3b734b550; ?>
<?php unset($__attributesOriginal4a63d2bc94d0926843877ab3b734b550); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4a63d2bc94d0926843877ab3b734b550)): ?>
<?php $component = $__componentOriginal4a63d2bc94d0926843877ab3b734b550; ?>
<?php unset($__componentOriginal4a63d2bc94d0926843877ab3b734b550); ?>
<?php endif; ?>
    </body>
</html><?php /**PATH /var/www/storage/framework/views/68a95834fbe261770b9d631305b3aa60.blade.php ENDPATH**/ ?>