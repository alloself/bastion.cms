@props([
    'navigation',
    'global'
])

@php
    $phone = getAttributeByKey($global, 'phone')?->pivot->value;
    $phone_link = $phone ? preg_replace("/[^0-9]/", "", $phone) : '';  
    $email = getAttributeByKey($global, 'email')?->pivot->value;
@endphp

<offcanvas name="mobileMenu" v-cloak>
    @if (isset($navigation) && count($navigation))
    <nav class="mb-6">
        <ul class="text-[16px] font-normal">
            @foreach ($navigation as $nav_item)
                @php
                    $nav_link = getAttributeByKey($nav_item, 'link')?->pivot->value;
                @endphp
                <li><a href="{{$nav_link}}" class="decor-link">{{$nav_item->link->title}}</a></li>
            @endforeach
        </ul>
    </nav>
    @endif
    @if (isset($phone) && isset($phone_link))
    <div class="mb-1">
        <a 
            href="tel:{{$phone_link}}" 
            class="w-full app-button app-button--secondary"
        >{{$phone}}</a>
    </div>
    @endif
    <div class="flex gap-1 mb-6">
        <button 
            v-modal-call="{name: 'callbackModal'}"
            class="app-button app-button--primary flex-1"
        >Связаться</button>
        <button 
            v-modal-call="{name: 'callbackModal'}"
            class="app-button app-button--square app-button--primary"
        >+</button>
    </div>
    <div>
        <a class="decor-link font-bold" href="mailto:{{$email}}">{{$email}}</a>
    </div>
</offcanvas>