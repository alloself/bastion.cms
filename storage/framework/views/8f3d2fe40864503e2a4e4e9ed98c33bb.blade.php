@php
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
@endphp

<section id='block-9fecee54-a605-4642-8210-f3e0d753d5f2' class="app-contacts-block {{$contacts_block_padding}} pb-20 xl:pb-[230px] bg-stone-100 bg-left-bottom bg-no-repeat" style="background-image: url(/storage/img/contacts-block-bg.png);">
    <div class="centered">
        <h2 class="h2 mb-10 md:mb-16 lg:max-w-[60%] xl:max-w-[45%]">{{$global->link->title}}</h2>
        <div class="flex flex-col gap-3 lg:max-w-[40%] md:ml-auto">
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">телефон</div>
                <div><a class="font-medium text-brand text-[20px] md:text-[28px] decor-link" href="tel:{{$phone_link}}">{{$phone}}</a></div>
            </div>
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">факс</div>
                <div><a class="font-medium text-brand text-[20px] md:text-[28px] decor-link" href="tel:{{$fax_link}}">{{$fax}}</a></div>
            </div>
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">e-mail</div>
                <div><a class="font-medium text-brand text-[20px] md:text-[28px] decor-link" href="mailto:{{$email}}">{{$email}}</a></div>
            </div>
            <div class="md:flex md:gap-3 md:items-end">
                <div class="mb-2 text-label text-brand text-opacity-40 md:mb-0 md:flex-none md:w-[30%]">адрес</div>
                <div class="text-brand text-[13px] text-opacity-60">{{$address}}</div>
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
    <div class="pointer-events-none absolute left-5 right-5 bottom-0 font-medium text-center leading-[1.0] text-[13vw] opacity-[0.12]">{{$email}}</div>
    <div class="centered">
        <div class="sm:flex sm:gap-4">
            <ul class="flex flex-col items-center text-white text-[16px] font-normal text-opacity-40 mb-10 sm:mb-0 sm:items-start">
            @if (isset($navigation) && count($navigation))
                @foreach ($navigation as $nav_item)
                    @php
                        $nav_link = $nav_item->dataEntityables[0]->link;
                    @endphp
                    <li><a href="{{$nav_link}}" class="decor-link {{isActivePage($nav_link)}}">{{$nav_item->link->title}}</a></li>
                @endforeach
            @endif
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
                    <a class="decor-link" href="{{$privacy_policy_link}}">Политика конфедициальности</a>
                    <a class="decor-link" href="{{$site_devepment_link}}" target="_blank">Разработка сайта</a>
                    <div>{{$copyright}}</div>
                </div>
            </div>
        </div>
    </div>
</footer>