<?php
  $global = getItemByPivotKey($header->dataCollections, 'global');
 
  $navigation = getItemByPivotKey($header->dataCollections, 'navigation')?->dataEntities;
  $address = getAttributeByKey($global->attributes, 'address')?->pivot->value;
  $copyright = getAttributeByKey($global->attributes, 'copyright')?->pivot->value;
  $privacy_policy_link = getAttributeByKey($global, 'privacy_policy_link')?->pivot->value;
  $site_devepment_link = getAttributeByKey($global, 'site_devepment_link')?->pivot->value;
  $email = getAttributeByKey($global->attributes, 'email')?->pivot->value;
  $phone = getAttributeByKey($global->attributes, 'phone')?->pivot->value;
  $phone_link = $phone ? preg_replace("/[^0-9]/", "", $phone) : '';
  $fax = getAttributeByKey($global->attributes, 'fax')?->pivot->value;
  $fax_link = $fax ? preg_replace("/[^0-9]/", "", $fax) : '';
  $contacts_block_padding = Request::path() == 'kontakty' ? 'pt-[140px]' : 'pt-8';
?>

<section id='block-9fecee54-a605-4642-8210-f3e0d753d5f2' class="app-contacts-block <?php echo e($contacts_block_padding); ?> pb-20 xl:pb-[230px] bg-stone-100 bg-left-bottom bg-no-repeat" style="background-image: url(/storage/img/contacts-block-bg.png);">
    <div class="centered">
        <h2 class="h2 mb-10 md:mb-16 lg:max-w-[60%] xl:max-w-[45%]"><?php echo e($global->link->title); ?></h2>
        <div class="flex flex-col gap-3 lg:max-w-[40%] md:ml-auto">
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">телефон</div>
                <div><a class="font-medium text-brand text-[20px] md:text-[28px] decor-link" href="tel:<?php echo e($phone_link); ?>"><?php echo e($phone); ?></a></div>
            </div>
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">факс</div>
                <div><a class="font-medium text-brand text-[20px] md:text-[28px] decor-link" href="tel:<?php echo e($fax_link); ?>"><?php echo e($fax); ?></a></div>
            </div>
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">e-mail</div>
                <div><a class="font-medium text-brand text-[20px] md:text-[28px] decor-link" href="mailto:<?php echo e($email); ?>"><?php echo e($email); ?></a></div>
            </div>
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">адрес</div>
                <div class="text-brand text-[13px] text-opacity-60"><?php echo e($address); ?></div>
            </div>
            <div class="md:flex md:gap-3 md:items-end mt-6">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]"></div>
                <div>
                    <div class="flex justify-start gap-1">
                        <button 
                            v-modal-call="{name: 'callbackModal'}" 
                            class="app-button app-button--secondary sm:min-w-[200px]"
                        >Обсудить проект</button>
                        <button 
                            v-modal-call="{name: 'callbackModal'}" 
                            class="app-button app-button--square app-button--secondary"
                        >+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer id='block-9fecee54-a605-4642-8210-f3e0d753d5f2' class="app-footer relative overflow-hidden pt-8 pb-20 sm:pb-[120px] lg:pb-[220px] bg-brand text-white">
    <div class="pointer-events-none absolute left-5 right-5 bottom-0 font-medium text-center leading-[1.0] text-[13vw] opacity-[0.12]"><?php echo e($email); ?></div>
    <div class="centered">
        <div class="sm:flex sm:gap-4">
            <ul class="flex flex-col items-center text-white text-[16px] font-normal text-opacity-40 mb-10 sm:mb-0 sm:items-start">
            <?php if(isset($navigation) && count($navigation)): ?>
                <?php $__currentLoopData = $navigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nav_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $nav_link = $nav_item->dataEntityables[0]->link;
                    ?>
                    <li><a href="<?php echo e($nav_link); ?>" class="decor-link <?php echo e(isActivePage($nav_link)); ?>"><?php echo e($nav_item->link->title); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            </ul>
            <div class="sm:ml-auto sm:max-w-[300px]">
                <div class="text-neutral mb-5 font-medium leading-[1.1] text-[20px] text-center sm:text-left sm:text-[28px]">С радостью ответим на ваши вопросы</div>
                <div class="flex justify-center gap-1 mb-10 sm:mb-[120px] sm:justify-start">
                    <button 
                        v-modal-call="{name: 'callbackModal'}"
                        class="app-button app-button--light min-w-[200px]"
                    >Связаться</button>
                    <button 
                        v-modal-call="{name: 'callbackModal'}"
                        class="app-button app-button--square app-button--light"
                    >+</button>
                </div>
                <div class="text-white text-opacity-40 text-[14px] flex flex-col items-center gap-1 sm:items-start">
                    <a class="decor-link" href="<?php echo e($privacy_policy_link); ?>">Политика конфедициальности</a>
                    <a class="decor-link" href="<?php echo e($site_devepment_link); ?>" target="_blank">Разработка сайта</a>
                    <div><?php echo e($copyright); ?></div>
                </div>
            </div>
        </div>
    </div>
</footer><?php /**PATH /var/www/storage/framework/views/8f3d2fe40864503e2a4e4e9ed98c33bb.blade.php ENDPATH**/ ?>