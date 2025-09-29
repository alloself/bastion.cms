@php
    $section_title = $section_title ?? 'Товары';
    $cards_count = $cards_count ?? 8;
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
           @if (isset($entity->dataEntities) && method_exists($entity->dataEntities, 'lastPage') && $entity->dataEntities->lastPage() > 1)
              <pagination search-key="''" current="{{ $currentPage }}"
                :total="{{ $entity->dataEntities->total() }}" :per-page="{{ $perPage }}" class="mt-10 lg:mt-14">
              </pagination>
          @endif
        </div>
    </div>
  </section>