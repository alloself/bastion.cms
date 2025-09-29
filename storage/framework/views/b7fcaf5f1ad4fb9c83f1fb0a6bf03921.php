<?php
  $image = getItemByPivotKey($contentBlock->images, 'bg')?->url;
?>

<section id='block-9ffec698-f7a4-4716-8e1c-06730c09d20e'
    class="catalog-main-block relative z-[5] overflow-hidden bg-left-bottom bg-no-repeat"
    style="background-image: url(<?php echo e($image); ?>)"
  >
    <div class="swiper js-catalog-main-slider">
      <div class="swiper-wrapper">
        <?php $__currentLoopData = $contentBlock->renderedChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $children): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
           <?php echo $children; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
    <div
      class="flex gap-1 absolute left-1/2 -translate-x-1/2 top-[44%] z-[5] lg:top-auto lg:bottom-12"
    >
      <button
        type="button"
        class="app-button app-button--square app-button--secondary js-catalog-main-slider-prev after:content-['<']"
      ></button>
      <button
        type="button"
        class="app-button app-button--secondary pointer-events-none min-w-[200px]"
      >
        Листать акции
      </button>
      <button
        type="button"
        class="app-button app-button--square app-button--secondary js-catalog-main-slider-next after:content-['>']"
      ></button>
    </div>
  </section><?php /**PATH /var/www/storage/framework/views/fe874e2891e1a26e45d6a8810298e6ee.blade.php ENDPATH**/ ?>