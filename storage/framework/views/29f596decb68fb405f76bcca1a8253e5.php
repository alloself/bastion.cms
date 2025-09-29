<?php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
    $team_list = getItemByPivotKey($contentBlock->dataCollections, 'team_collection')?->dataEntities;;
?>

<?php if(isset($team_list) && count($team_list)): ?>
<section id='block-9fed104c-b98a-413d-a345-7879003a203f' class="app-team-block relative overflow-hidden py-8">
    <div class="centered mb-12 lg:mb-16">
        <div class="lg:flex">
            <div class="mb-6 lg:mb-0 lg:w-1/3 lg:flex-none lg:pr-4 text-simple text-simple--dark"><?php echo e($contentBlock->link->subtitle); ?></div>
            <h2 class="h4 mb-0 md:max-w-[50%] lg:max-w-[40%]"><?php echo e($contentBlock->link->title); ?></h2>
        </div>
    </div>
    <div class="swiper app-team-slider js-team-slider">
        <div class="swiper-wrapper">
            <?php $__currentLoopData = $team_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $photo = getItemByPivotKey($item->images, 'photo')?->url;
                ?>
                <div class="swiper-slide">
                    <div class="app-team-slider__item">
                        <div class="app-team-slider__image">
                            <img src="<?php echo e($photo); ?>" alt="<?php echo e($item->link->title); ?>">
                        </div>
                        <div class="app-team-slider__name h4 mb-3"><?php echo e($item->link->title); ?></div>
                        <div class="app-team-slider__func text-brand text-opacity-40 text-label"><?php echo e($item->link->subtitle); ?></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="slider-prev">
            <div class="svg-icon">
                <svg><use xlink:href="#arrow-left"></use></svg>
            </div>
        </div>
        <div class="slider-next">
            <div class="svg-icon">
                <svg><use xlink:href="#arrow-right"></use></svg>
            </div>
        </div>
    </div>
    <?php if(isset($link)): ?>
    <div class="centered mt-8 lg:mt-12">
        <div class="flex justify-center gap-1">
            <a href="<?php echo e($link); ?>" class="min-w-[200px] app-button app-button--secondary">Больше о компании</a>
            <a href="<?php echo e($link); ?>" class="app-button app-button--secondary app-button--square">+</a>
        </div>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?><?php /**PATH /var/www/storage/framework/views/bf5851ec99c94508a5dad24cf9ee0827.blade.php ENDPATH**/ ?>