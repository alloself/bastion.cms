@php
  $global = getItemByPivotKey($header->dataCollections, 'global');
  $navigation = getItemByPivotKey($header->dataCollections, 'navigation')?->dataEntities;
  $phone = getAttributeByKey($global, 'phone')?->pivot->value;
  $phone_link = $phone ? preg_replace("/[^0-9]/", "", $phone) : '';

  function getLink($item) {
    return $item->dataEntityables[0]->link;
  };
@endphp

<header id='block-9ffaf4cf-1249-4c3f-a205-3aaa5b381150' class="app-header app-header--inner fixed z-[100] left-0 right-0 top-0 lg:pt-4">
    <div class="centered px-0 lg:px-5">
        <div class="p-2 bg-stone-100 flex items-center shadow-md lg:rounded-md lg:p-1">
            <div class="app-header__logo">
                <a href="/" class="flex items-center gap-4">
                    <img class="flex-none max-w-10" src="/storage/img/logo-dark.svg" alt="ИСУ">
                    <span class="text-[15px] font-normal leading-[1.1] uppercase max-w-[230px] hidden xl:block">
                        {{$global->link->subtitle}}
                    </span>
                </a>
            </div>
            <div class="flex items-center ml-auto gap-1">
                @if (isset($navigation) && count($navigation))
                    <nav class="hidden lg:block mr-5 xl:mr-14">
                        <ul class="flex items-center gap-5 text-[13px] text-dark text-opacity-70">
                            @foreach ($navigation as $nav_item)
                                @php
                                    $nav_link = getLink($nav_item);
                                @endphp
                                <li><a href="{{$nav_link->link->url}}" class="decor-link {{isActivePage($nav_link->link->url)}}">{{$nav_item->link->title}}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                @endif
                <a 
                    href="tel:{{$phone_link}}" 
                    class="app-button app-button--secondary hidden md:inline-flex"
                >{{$phone}}</a>
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
</header>