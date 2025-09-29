@php
    $section_title = $section_title ?? 'Товары';
    $cards_count = $cards_count ?? 8;
    $page_buttons = $page_buttons ?? [1, 2, 3, '...', 40];
    $current_page = $current_page ?? 1;
@endphp

<section id='block-9ffed971-1f51-4c58-8eae-9be3f8c568eb' class="py-20">
    <div class="centered">
        <h2 class="text-simple text-brand/50">{{$section_title}}</h2>
        <div class="app-tabs__body-item">
            <div class="grid gap-5 grid-cols-2 lg:grid-cols-3">
               @foreach ($entity->dataEntities as $item)
                  <x-hit-new-product :data="$item"></x-hit-new-product>
                @endforeach
            </div>
            <div class="flex flex-wrap justify-center gap-1 mt-10">
                @foreach ($page_buttons as $btn)
                    @php
                        $is_current = is_int($btn) && $btn === $current_page;
                    @endphp
                    <a href="#" class="app-button {{ $is_current ? 'app-button--brand' : 'app-button--secondary' }} app-button--square">{{ $btn }}</a>
                @endforeach
            </div>
        </div>
    </div>
  </section>