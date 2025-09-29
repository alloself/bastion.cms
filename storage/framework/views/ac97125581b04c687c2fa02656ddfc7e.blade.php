@php
    $title = $title ?? 'Категории товаров';
    $subtitle = $subtitle ?? 'Каталог';
    $description = $description ?? 'Краткий текст-описание в 2-4 предложения. Профилирующими направлениями деятельности компании являются\n                ПИР и СМР по устройству внутренних инженерных систем, оборудования зданий и сооружений';
    $list = $items ?? [
        [
            'title' => 'Сухие строительные смести и грунты',
            'subtitle' => 'Подзагололакокрасочные материалы / грунты / сухие смеси / цементвок',
            'link' => '#',
        ],
        [
            'title' => 'Теплоизоляция и шумоизоляция',
            'subtitle' => 'экструдированный  пенополистирол / утеплитель / звукоизоляция / ветро-влагозащита и пароизоляция',
            'link' => '#',
        ],
        [
            'title' => 'Материалы для сухого строительства',
            'subtitle' => 'профиль для гипсокартона / листовые материалы',
            'link' => '#',
        ],
        [
            'title' => 'Хозяйственные товары',
            'subtitle' => 'ИТП, тепловые сети, пуско-наладка',
            'link' => '#',
        ],
        [
            'title' => 'Транспортные услуги',
            'subtitle' => 'Внутренние системы отопления, вентиляция, АОВ, водоснабжение, канализация ',
            'link' => '#',
        ],
        [
            'title' => 'Стеновые и фасадные материалы',
            'subtitle' => 'кирпич / газобетонные блоки / фасадные панели / газобетонные перемычки',
            'link' => '#',
        ],
        [
            'title' => 'Кровля',
            'subtitle' => 'металлочерепица и комплектующие направляемая кровля / ПВХ мембрана / мастики / праймеры / комплектующие',
            'link' => '#',
        ],
        [
            'title' => 'Геоматериалы',
            'subtitle' => 'оборотный капитал',
            'link' => '#',
        ],
    ];
@endphp

<section id='block-9ffecc08-b092-4317-a866-3c7cb878cfe7' class="py-12 bg-stone-200">
    <div class="centered">
        <div class="md:flex mb-8">
            <div class="mb-3 text-simple text-dark text-opacity-60 md:mb-0 md:w-1/2">{{$subtitle}}</div>
            <h1 class="h2 mb-0 md:w-1/2 lg:w-1/3">{{$title}}</h1>
        </div>
        <div class="text-simple text-dark text-opacity-60 md:ml-[50%] lg:w-1/3">
            {{$description}}
        </div>
        <nav class="mt-10 md:mt-16">
            <ul>
                @foreach($list as $item)
                <li class="pb-2 mb-3 border-b border-dark border-opacity-10 md:flex md:items-end md:flex-gap-4 md:flex-wrap">
                    <div class="md:w-1/2 md:pr-4">
                        <a class="h4 mb-3 text-dark decor-link md:mb-0" href="{{ $item['link'] ?? '#' }}">{{ $item['title'] ?? '' }}</a>
                    </div>
                    <div class="md:w-1/2 lg:flex lg:gap-4">
                        <div class="mb-2 text-simple text-dark text-opacity-60 md:min-w-0 md:flex-1 lg:mb-0">
                            {{ $item['subtitle'] ?? '' }}
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </nav>
    </div>
    </section>