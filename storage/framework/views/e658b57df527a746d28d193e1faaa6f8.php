<?php
    $services_list = getItemByPivotKey($footer->dataCollections, 'services')?->dataEntities;
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
?>

<section id='block-9ffaf67a-26d7-4940-87cf-3bbc9c39f4b4' class="app-services-list py-8 bg-stone bg-opacity-30 lg:py-16">
    <div class="centered">
        <div class="mb-8 md:flex lg:mb-14">
            <div class="mb-3 text-simple text-dark text-opacity-60 md:mb-0 md:w-1/2"><?php echo e($contentBlock->link->subtitle); ?></div>
            <h2 class="h2 mb-0 md:w-1/2 md:ml-4 lg:w-1/3"><?php echo e($contentBlock->link->title); ?></h2>
        </div>
        <?php if(isset($services_list) && count($services_list)): ?>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            <?php $__currentLoopData = $services_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginale804957ecdb153e8c822de5ed47a4ace = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale804957ecdb153e8c822de5ed47a4ace = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.service-card','data' => ['data' => $service_item]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('service-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($service_item)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale804957ecdb153e8c822de5ed47a4ace)): ?>
<?php $attributes = $__attributesOriginale804957ecdb153e8c822de5ed47a4ace; ?>
<?php unset($__attributesOriginale804957ecdb153e8c822de5ed47a4ace); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale804957ecdb153e8c822de5ed47a4ace)): ?>
<?php $component = $__componentOriginale804957ecdb153e8c822de5ed47a4ace; ?>
<?php unset($__componentOriginale804957ecdb153e8c822de5ed47a4ace); ?>
<?php endif; ?>                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-1 mt-12 lg:mt-[100px]">
            <button
                type="button"
                class="app-button app-button--primary"
                v-modal-call="{name: 'callbackModal'}"
            >Заказать услугу</button>
            <a href="<?php echo e($link); ?>" class="app-button app-button--secondary">Смотреть кейсы</a>
            <a href="<?php echo e($link); ?>" class="hidden app-button app-button--secondary app-button--square sm:inline-flex">+</a>
        </div>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/5f8f0ac02e84cfe7c646cd9b077b9402.blade.php ENDPATH**/ ?>