@props([
    'collections'
])

<nav class="mb-10 pb-2.5 border-b border-neutral-alpha">
  <div class="text-[12px] leading-[1.4] uppercase mb-5 text-dark">Категории:</div>
  <div class="text-[20px] font-semibold leading-[1.1] tracking-[-0.8px] space-y-1">
      @foreach($collections as $collection)
      <appdetails>
          <template #title>
              <a href="{{ $collection->link->url ?? '#' }}" class="text-dark decor-link">{{ $collection->link->title ?? 'Категория' }}</a>
              <span class="text-[14px] text-dark text-opacity-40 ml-2">{{ $collection->dataEntities->count() ?? 0 }}</span>
          </template>
          @if($collection->children && $collection->children->isNotEmpty())
          <template #content>
              <ul class="px-5 py-2.5 text-[16px] font-semibold leading-[1.1] tracking-[-0.8px] space-y-1">
                  @foreach($collection->children as $child)
                  <li class="flex items-center">
                      <a href="{{ $child->link->url ?? '#' }}" class="decor-link">{{ $child->link->title ?? 'Подкатегория' }}</a>
                      <span class="text-[14px] text-dark text-opacity-40 ml-2">{{ $child->dataEntities->count() ?? 0 }}</span>
                  </li>
                  @endforeach
              </ul>
          </template>
          @endif
      </appdetails>
      @endforeach
  </div>
</nav>

