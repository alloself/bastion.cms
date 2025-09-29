<?php
  $global = getItemByPivotKey($header->dataCollections, 'global');
  $navigation = getItemByPivotKey($header->dataCollections, 'navigation')?->dataEntities;
  $phone = getAttributeByKey($global, 'phone')?->pivot->value;
  $phone_link = $phone ? preg_replace("/[^0-9]/", "", $phone) : '';

  function getLink($item) {
    return $item->dataEntityables[0]->link;
  };
?>

<header id='block-9ffaf4cf-1249-4c3f-a205-3aaa5b381150' class="app-header app-header--inner fixed z-[100] left-0 right-0 top-0 lg:pt-4">
    <div class="centered px-0 lg:px-5">
        <div class="p-2 bg-stone-100 flex items-center shadow-md lg:rounded-md lg:p-1">
            <div class="app-header__logo">
                <a href="/" class="flex items-center gap-4">
                    <img class="flex-none max-w-10" src="/storage/img/logo-dark.svg" alt="ИСУ">
                    <span class="text-[15px] font-normal leading-[1.1] uppercase max-w-[230px] hidden xl:block">
                        <?php echo e($global->link->subtitle); ?>

                    </span>
                </a>
            </div>
            <div class="flex items-center ml-auto gap-1">
                <?php if(isset($navigation) && count($navigation)): ?>
                    <nav class="hidden lg:block mr-5 xl:mr-14">
                        <ul class="flex items-center gap-5 text-[13px] text-dark text-opacity-70">
                            <?php $__currentLoopData = $navigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nav_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $nav_link = getLink($nav_item);
                                ?>
                                <li><a href="<?php echo e($nav_link); ?>" class="decor-link <?php echo e(isActivePage($nav_link)); ?>"><?php echo e($nav_item->link->title); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                <a 
                    href="tel:<?php echo e($phone_link); ?>" 
                    class="app-button app-button--secondary hidden md:inline-flex"
                ><?php echo e($phone); ?></a>
                <button 
                    v-modal-call="{name: 'callbackModal'}" 
                    type="button" 
                    class="min-w-[200px] app-button app-button--primary"
                >Обсудить проект</button>
                <button
                    v-modal-call="{name: 'callbackModal'}" 
                    type="button" 
                    class="app-button app-button--primary app-button--square"
                >+</button>
                <mobilemenutrigger class="lg:hidden"></mobilemenutrigger>
            </div>
        </div>
    </div>
</header><?php /**PATH /var/www/storage/framework/views/3e50407869e9bb7b0d7e770f5feb2a80.blade.php ENDPATH**/ ?>