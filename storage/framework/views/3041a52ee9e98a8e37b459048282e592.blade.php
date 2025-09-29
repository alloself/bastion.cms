@php
  $global = getItemByPivotKey($header->dataCollections, 'global');
  $navigation = getItemByPivotKey($header->dataCollections, 'navigation')?->dataEntities;
  $phone = getAttributeByKey($global->attributes, 'phone')?->pivot->value;
  $phone_link = $phone ? preg_replace("/[^0-9]/", "", $phone) : '';
  $slogan = getAttributeByKey($global->attributes, 'slogan')?->pivot->value;

  function getLink($item) {
    return $item->dataEntityables[0]->link;
  };
@endphp

<header id='block-9fecee10-ee92-424d-8765-f66e2f450b7b' class="app-header-main">
    <div class="centered relative">
        <div class="absolute z-[1] left-4 bottom-4 pointer-events-none">
            <img class="opacity-40 md:opacity-100" src="/storage/img/header-main-bg.png" alt="">
        </div>
        <div class="flex py-5 md:py-8">
            <div class="md:w-1/2 md:ml-auto xl:w-1/4 md:pl-4">
                <div class="font-medium leading-[1.1] mb-5 text-[32px] lg:text-[58px] lg:mb-8">{{$global->link->subtitle}}</div>
                <div class="text-[12px] md:text-[13px] text-opacity-60 text-brand">{{$slogan}}</div>
            </div>
            <div class="xl:w-1/4"></div>
        </div>
        <div class="md:flex items-center gap-3 py-4">
            <div class="hidden md:block lg:w-[220px]"></div>
            @if (isset($navigation) && count($navigation))
            <nav class="mx-auto hidden lg:block">
                <ul class="flex items-center gap-5 text-[13px] text-dark text-opacity-70">
                    @foreach ($navigation as $nav_item)
                        @php
                            $nav_link = getLink($nav_item);
                            
                        @endphp
                        <li><a href="{{$nav_link->url}}" class="decor-link {{isActivePage($nav_link->url)}}">{{$nav_link->title}}</a></li>
                    @endforeach
                </ul>
            </nav>
            @endif
            <div class="flex gap-1 md:ml-auto lg:ml-0">
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
                <mobilemenutrigger class="ml-auto lg:hidden"></mobilemenutrigger>
            </div>
        </div>
    </div>
</header>