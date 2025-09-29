<?php
    $link = getAttributeByKey($contentBlock, 'link')?->pivot->value;
?>

<div id='block-9ffaf9cd-9ddf-4408-87cf-f165df800086' class="pb-1 border-b border-brand border-opacity-20">
    <div class="h3 mb-1"><a href="<?php echo e($link); ?>"><?php echo e($contentBlock->link->title); ?></a></div>
    <div class="text-simple text-brand text-opacity-60"><?php echo e($contentBlock->link->subtitle); ?></div>
</div><?php /**PATH /var/www/storage/framework/views/494af73d5e2cbb670635a691cc43a126.blade.php ENDPATH**/ ?>