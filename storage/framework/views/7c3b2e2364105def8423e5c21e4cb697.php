<?php
    $section_title = $section_title ?? 'Товары';
    $cards_count = $cards_count ?? 8;
?>

<section id='block-9ffed971-1f51-4c58-8eae-9be3f8c568eb' class="py-20">
    <div class="centered">
        <h2 class="text-simple text-brand/50"><?php echo e($section_title); ?></h2>
        <div class="app-tabs__body-item">
            <div class="grid gap-5 grid-cols-2 lg:grid-cols-3">
               <?php $__currentLoopData = $entity->dataEntities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if (isset($component)) { $__componentOriginal8d61e40013fa40253d44b1590c34549d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d61e40013fa40253d44b1590c34549d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hit-new-product','data' => ['data' => $item]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('hit-new-product'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d61e40013fa40253d44b1590c34549d)): ?>
<?php $attributes = $__attributesOriginal8d61e40013fa40253d44b1590c34549d; ?>
<?php unset($__attributesOriginal8d61e40013fa40253d44b1590c34549d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d61e40013fa40253d44b1590c34549d)): ?>
<?php $component = $__componentOriginal8d61e40013fa40253d44b1590c34549d; ?>
<?php unset($__componentOriginal8d61e40013fa40253d44b1590c34549d); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
           <?php if(isset($entity->dataEntities) && method_exists($entity->dataEntities, 'lastPage') && $entity->dataEntities->lastPage() > 1): ?>
              <pagination search-key="''" current="<?php echo e($currentPage); ?>"
                :total="<?php echo e($entity->dataEntities->total()); ?>" :per-page="<?php echo e($perPage); ?>" class="mt-10 lg:mt-14">
              </pagination>
          <?php endif; ?>
        </div>
    </div>
  </section><?php /**PATH /var/www/storage/framework/views/099087b78fc1863468784556048cd033.blade.php ENDPATH**/ ?>