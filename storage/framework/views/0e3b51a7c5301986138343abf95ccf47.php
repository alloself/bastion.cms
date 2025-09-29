<?php
    $contentBlock_bg = getItemByPivotKey($contentBlock->images, 'bg')?->url;
    $reviews_list = getItemByPivotKey($contentBlock->dataCollections, 'reviews')?->dataEntities;
?>

<?php if(isset($reviews_list) && count($reviews_list)): ?>
<section id='block-9fed101a-8d9e-4c95-8b43-3031485ce570'
    class="app-reviews-block relative flex py-12 bg-cover bg-no-repeat bg-center lg:min-h-[780px]"
    style="background-image: url(<?php echo e($contentBlock_bg); ?>)"
>
    <div class="swiper static js-reviews-slider lg:my-auto">
        <div class="swiper-wrapper">
            <?php $__currentLoopData = $reviews_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $logo = getItemByPivotKey($review_item->images, 'logo')?->url;
                    $thanks_letter = getItemByPivotKey($review_item->files, 'thanks_letter')?->url;
                ?>
                <div class="swiper-slide">
                    <div class="centered px-10">
                        <div class="bg-light p-5 max-w-[720px] mx-auto">
                            <div class="flex gap-4 pb-5 mb-5 border-b border-brand border-opacity-20">
                                <div class="flex-1">
                                    <div class="text-label text-brand text-opacity-40 mb-3"><?php echo e($loop->index + 1); ?> / <?php echo e(count($reviews_list)); ?></div>
                                    <div class="h4 text-brand mb-0"><?php echo e($review_item->link->title); ?></div>
                                </div>
                                <?php if(isset($logo)): ?>
                                <div class="size-[70px] border border-brand border-opacity-20">
                                    <img class="object-cover" src="<?php echo e($logo); ?>" alt="">
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-simple text-brand text-opacity-60">
                                <?php echo $review_item->content; ?>

                            </div>
                            <?php if(isset($logo)): ?>
                            <div class="text-[14px] font-medium mt-8 md:text-[16px]">
                                <a class="underline hover:no-underline" target="_blank" href="<?php echo e($thanks_letter); ?>">Открыть благодарственное письмо</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="slider-pagination bottom-4 lg:bottom-10 text-primary"></div>
        <div class="slider-prev text-light">
            <div class="svg-icon">
                <svg><use xlink:href="#arrow-left"></use></svg>
            </div>
        </div>
        <div class="slider-next text-light">
            <div class="svg-icon">
                <svg><use xlink:href="#arrow-right"></use></svg>
            </div>
        </div>
    </div>
</section>
<?php endif; ?><?php /**PATH /var/www/storage/framework/views/e4f4032063fed14f4ebb63b63ef80c6f.blade.php ENDPATH**/ ?>