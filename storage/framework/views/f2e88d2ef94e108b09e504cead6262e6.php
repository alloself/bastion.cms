<?php
    $subtitle = $subtitle ?? 'О нас';
    $title = $title ?? 'Строительные материалы в Санкт-Петербурге: оптовый магазин с широким ассортиментом и удобной доставкой';
    $paragraphs = $paragraphs ?? [
        'Если вам нужны качественные строительные материалы в Санкт-Петербурге, обращайтесь в наш магазин. Мы предлагаем широкий ассортимент стройматериалов по оптовым ценам. У нас только качественные товары с гарантией и быстрой доставкой. Мы предлагаем широкий выбор строительных материалов от ведущих производителей по доступным ценам.',
        'У нас вы можете купить товары как оптом, так и в розницу. Наш опытный персонал всегда готов помочь вам подобрать подходящий материал для вашего проекта.',
    ];
    $more_url = $more_url ?? '#';
    $plus_url = $plus_url ?? '#';
?>

<section id='block-9ffed235-28a9-45ab-8c91-67ba16da31d8' class="py-10 bg-stone/30 pb-10">
    <div class="centered">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center">
            <div class="text-simple text-brand/60 flex-none lg:w-1/3"><?php echo e($subtitle); ?></div>
            <div class="lg:flex-1 lg:min-w-0">
                <h2 class="h2"><?php echo e($title); ?></h2>
                <div class="lg:pl-[25%]">
                    <div class="text-brand/60 text-[13px] md:grid md:grid-cols-2 md:gap-8">
                        <?php $__currentLoopData = $paragraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p>
                            <?php echo e($p); ?>

                        </p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="flex flex-wrap gap-1 mt-6">
                        <a href="<?php echo e($more_url); ?>" class="app-button app-button--primary flex-1 min-w-0 sm:flex-none">Подробнее</a>
                        <a href="<?php echo e($plus_url); ?>" class="app-button app-button--primary app-button--square">+</a>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</section><?php /**PATH /var/www/storage/framework/views/b7dc95f703931cc8d739de038e290244.blade.php ENDPATH**/ ?>