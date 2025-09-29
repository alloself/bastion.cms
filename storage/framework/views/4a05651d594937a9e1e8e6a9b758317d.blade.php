<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="UTF-8">
    @csrf
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="keywords" content="{{ $entity->keywords ? $entity->keywords : '' }}" />
    <meta name="description" content="{{ $entity->description ? $entity->description : '' }}" />
    <title>{{ $entity->link->title ?? $entity->link->title }}</title>
    @vite(['resources/scss/site/index.scss', 'resources/ts/site/index.ts'])
</head>
    @php
        dd($header);
        $global = getItemByPivotKey($header->dataCollections, 'global');
        $navigation = getItemByPivotKey($header->dataCollections, 'navigation')?->dataEntities;
    @endphp
    <body>
        <div id="app" class="app-wrapper">
            <div class="stripes">
                <div class="stripes__item"></div>
                <div class="stripes__item"></div>
                <div class="stripes__item"></div>
            </div>
            @if (count($contentBlocks))
                @foreach ($contentBlocks as $block)
                    {!! $block !!}
                @endforeach
            @endif
            <x-mobile-menu 
                :navigation="$navigation"
                :global="$global"
            ></x-mobile-menu>
            <x-modals></x-modals>
            <notifications></notifications>
        </div>
        <x-svg-sprite />
    </body>
</html>