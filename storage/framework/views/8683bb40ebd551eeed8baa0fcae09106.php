<?php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
?>

<div id='block-9ffaf9ff-8298-4564-8d69-4dc07a9b85b1' class="pb-1 border-b border-brand border-opacity-20">
    <div class="h3 mb-1"><a href="<?php echo e($link); ?>"><?php echo e($contentBlock->link->title); ?></a></div>
    <div class="text-simple text-brand text-opacity-60"><?php echo e($contentBlock->link->subtitle); ?></div>
</div><?php /**PATH /var/www/storage/framework/views/2aa8bd7e45e59dc38c9f6ac9ab4127c7.blade.php ENDPATH**/ ?>