@php
    $email = getAttributeByKey($contentBlock, 'email')?->pivot->value;
    $address = getAttributeByKey($contentBlock, 'address')?->pivot->value;
    $map_coords = getAttributeByKey($contentBlock, 'map_coords')?->pivot->value;
    
    // Убедимся, что координаты в правильном формате [широта, долгота]
    $coordinates = '[55.751574, 37.573856]'; // Москва по умолчанию
    if (!empty($map_coords)) {
        // Если координаты уже в формате JSON-массива, оставляем как есть
        if (str_starts_with(trim($map_coords), '[') && str_ends_with(trim($map_coords), ']')) {
            $coordinates = $map_coords;
        } else {
            // Пытаемся сконвертировать из других форматов (например, "55.751574, 37.573856")
            $parts = preg_split('/[,\s]+/', trim($map_coords));
            if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                $coordinates = '[' . $parts[0] . ', ' . $parts[1] . ']';
            }
        }
    }
    
    $global = getItemByPivotKey($footer->dataCollections, 'global');
    $phone = getAttributeByKey($global, 'phone')?->pivot->value;
    $phone_link = $phone ? preg_replace('/[^0-9]/', '', $phone) : '';
    $work_schedule = getAttributeByKey($global, 'work_schedule')?->pivot->value;
    $socials = getItemByPivotKey($footer->dataCollections, 'socials')?->dataEntities;
@endphp
<main class="py-12 lg:py-20 relative overflow-hidden">
    <div class="three-lamp three-lamp--main-right"></div>
    <div class="three-lamp three-lamp--main-left"></div>
    <div class="centered">
        <div class="font-semibold text-center flex flex-col items-center max-w-[540px] mx-auto">
            <div class="text-base font-normal mb-20 lg:mb-32">{{ $contentBlock->link->title }}</div> <a
                class="text-[28px] leading-[1.1] tracking-[-0.8px] lg:text-[48px]"
                href="tel:{{ $phone_link }}">{{ $phone }}</a>
            <div class="text-dark text-opacity-40 text-[14px] tracking-[-0.28px]">{!! $work_schedule !!}</div> <a
                class="mt-3 text-[28px] leading-[1.1] tracking-[-0.8px] lg:text-[48px]"
                href="mailto:{{ $email }}"> {{ $email }} </a>
            <div class="mt-3 text-[20px] lg:text-[28px] leading-[1.0]">{{ $address }}</div> <button
                v-modal-call="{name: 'callbackModal'}" class="mt-20 app-button app-button--secondary">написать
                нам</button>
            <div class="mt-10 flex justify-center items-center flex-wrap gap-1 lg:mt-20">
                @if (isset($socials) && count($socials))
                    @foreach ($socials as $social)
                        @php
                            $social_icon = getAttributeByKey($social, 'sprite_icon')?->pivot->value;
                            $social_link = getAttributeByKey($social, 'link')?->pivot->value;
                        @endphp <a target="_blank" href="{{ $social_link }}"
                            class="rounded-icon rounded-icon--dark">
                            <div class="svg-icon"> <svg>
                                    <use xlink:href="#{{ $social_icon }}"></use>
                                </svg> </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</main>
<div data-map class="bg-neutral grayscale" data-coords="{{ $coordinates }}"
    style="height: 520px; width: 100%;"></div>
