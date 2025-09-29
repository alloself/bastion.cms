<?php
    $collection = getItemByPivotKey($contentBlock->dataCollections, 'cases');
    $cases_items = $collection?->dataEntities;
?>

<section id='block-9ffb2ab7-3597-4368-9da7-b82246afb479' class="app-cases-list pt-[90px] lg:pt-[120px] pb-8 bg-stone-200">
    <div class="centered">
        <div class="md:flex mb-8">
            <div class="mb-3 text-simple text-dark text-opacity-60 md:mb-0 md:w-1/2"><?php echo e($contentBlock->link->subtitle); ?></div>
            <h1 class="h2 mb-0 md:w-1/2 lg:w-1/3"><?php echo e($contentBlock->link->title); ?></h1>
        </div>
        <div class="text-simple text-dark text-opacity-60 md:ml-[50%] lg:w-1/3">
            <?php echo $contentBlock->content; ?>

        </div>
        <?php if(isset($cases_items) && count($cases_items)): ?>
        <nav class="mt-10 md:mt-16">
            <ul>
                <?php $__currentLoopData = $cases_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $case_year = getAttributeByKey($case, 'year')?->pivot->value;
                ?>
                <li class="pb-2 mb-3 border-b border-dark border-opacity-10 md:flex md:items-end md:flex-gap-4 md:flex-wrap">
                    <div class="md:w-1/2">
                        <a class="h4 mb-3 text-dark decor-link md:mb-0" href="<?php echo e($case->link->path); ?>"><?php echo e($case->link->title); ?></a>
                    </div>
                    <div class="md:w-1/2 lg:flex lg:gap-4">
                        <div class="mb-2 text-simple text-dark text-opacity-60 md:min-w-0 md:flex-1 lg:mb-0"><?php echo e($case->link->subtitle); ?></div>
                        <div class="whitespace-nowrap text-label text-dark text-opacity-60"><?php echo e($case_year); ?></div>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </nav>
        <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['collection' => $collection]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['collection' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collection)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/edde27b6582a0be803ea591aa9fccfa6.blade.php ENDPATH**/ ?>