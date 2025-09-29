<?php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
?>

<div id='block-9ffafaca-2864-41df-90e3-ab7e063095a6' class="pb-1 border-b border-brand border-opacity-20">
    <div class="h3 mb-1"><a href="<?php echo e($link); ?>"><?php echo e($contentBlock->link->title); ?></a></div>
    <div class="text-simple text-brand text-opacity-60"><?php echo e($contentBlock->link->subtitle); ?></div>
</div><?php /**PATH /var/www/storage/framework/views/9fc4357f8fc20bde76193478c3927a87.blade.php ENDPATH**/ ?>