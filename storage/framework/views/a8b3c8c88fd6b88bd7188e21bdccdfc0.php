<?php
    $image = getItemByPivotKey($contentBlock->images, 'image')?->url;
?>

<section id='block-9ffeb974-c2c0-41a2-af02-269b88788ccd' class="app-image-block bg-stone-100 py-3 text-brand">
    <div class="centered">
        <div class="flex flex-col gap-5 md:flex-row md:items-end">
            <div class="md:w-1/3">
                <?php if(isset($image)): ?>
                    <img class="mx-auto" src="<?php echo e($image); ?>" alt="<?php echo e($contentBlock->link->subtitle); ?>">
                <?php endif; ?>
            </div>
            <div class="pb-5 md:flex-1 md:min-w-0">
                <h2 class="lg:ml-[25%]"><?php echo e($contentBlock->link->title); ?></h2>
                <div class="text-simple text-simple--dark lg:ml-[50%]"><?php echo e($contentBlock->link->subtitle); ?></div>
            </div>
        </div>
    </div>
</section><?php /**PATH /var/www/storage/framework/views/a18047ee2ec24072081faa69e1f543f0.blade.php ENDPATH**/ ?>