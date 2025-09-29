<?php
    $section_subtitle = $section_subtitle ?? 'faq';
    $section_title = $section_title ?? 'Вопрос-ответ';
    $open_indexes = $open_indexes ?? [];
    $items = $items ?? [
        [
            'title' => 'Вопрос 1',
            'body' => 'Если вам нужны качественные строительные материалы в Санкт-Петербурге, обращайтесь в наш магазин. Мы предлагаем широкий ассортимент стройматериалов по оптовым ценам. У нас только качественные товары с гарантией и быстрой доставкой. Мы предлагаем широкий выбор строительных материалов от ведущих производителей по доступным ценам.',
        ],
        [
            'title' => 'Вопрос 2 Система водоснабжения и канализации',
            'body' => 'Если вам нужны качественные строительные материалы в Санкт-Петербурге, обращайтесь в наш магазин. Мы предлагаем широкий ассортимент стройматериалов по оптовым ценам. У нас только качественные товары с гарантией и быстрой доставкой. Мы предлагаем широкий выбор строительных материалов от ведущих производителей по доступным ценам.',
        ],
        [
            'title' => 'Вопрос 3 Тепловые сети',
            'body' => 'Если вам нужны качественные строительные материалы в Санкт-Петербурге, обращайтесь в наш магазин. Мы предлагаем широкий ассортимент стройматериалов по оптовым ценам. У нас только качественные товары с гарантией и быстрой доставкой. Мы предлагаем широкий выбор строительных материалов от ведущих производителей по доступным ценам.',
        ],
        [
            'title' => 'Вопрос 4 Отопление, вентиляция и кондиционирование воздуха',
            'body' => 'Если вам нужны качественные строительные материалы в Санкт-Петербурге, обращайтесь в наш магазин. Мы предлагаем широкий ассортимент стройматериалов по оптовым ценам. У нас только качественные товары с гарантией и быстрой доставкой. Мы предлагаем широкий выбор строительных материалов от ведущих производителей по доступным ценам.',
        ],
    ];
?>

<section id='block-9ffece55-09f2-4e87-8011-72b912685093' class="py-12">
    <div class="centered">
        <div class="text-simple text-brand/60 mb-5"><?php echo e($section_subtitle); ?></div>
        <h2 class="h2 mb-10"><?php echo e($section_title); ?></h2>

        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $is_open = in_array($index, $open_indexes); ?>
            <div class="app-acc <?php echo e($is_open ? 'is-open' : ''); ?>">
                <div class="app-acc__top">
                    <div class="app-acc__title">
                        <div class="text-[18px] font-medium lg:text-[28px]"><?php echo e($item['title'] ?? ''); ?></div>
                    </div>
                    <div class="app-acc__arrow transition-all duration-300 pointer">
                        <div class="svg-icon">
                            <svg><use xlink:href="#arrow-right"></use></svg>
                        </div>
                    </div>
                </div>
                <div class="app-acc__body">
                    <div style="min-height: 0">
                        <div class="text-[13px] text-brand/60">
                            <?php echo e($item['body'] ?? ''); ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/20979fe556bffed619ecfd7135c24591.blade.php ENDPATH**/ ?>