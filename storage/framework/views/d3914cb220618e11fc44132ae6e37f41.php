<?php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
?>

<div id='block-9ffaf993-7bba-408b-a6ca-1c73d6592c10' class="pb-1 border-b border-brand border-opacity-20">
    <div class="h3 mb-1"><a href="<?php echo e($link); ?>"><?php echo e($contentBlock->link->title); ?></a></div>
    <div class="text-simple text-brand text-opacity-60"><?php echo e($contentBlock->link->subtitle); ?></div>
</div><?php /**PATH /var/www/storage/framework/views/f383b13e275f5eb8c824b2f81987f1da.blade.php ENDPATH**/ ?>