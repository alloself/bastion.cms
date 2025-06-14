@php
    // Проверяем, существует ли dataEntities и является ли он экземпляром пагинатора
    $count =
        isset($entity->dataEntities) && method_exists($entity->dataEntities, 'total')
            ? $entity->dataEntities->total()
            : count($entity->dataEntities ?? []);

    // Определяем, нужен ли префикс для параметров пагинации
    // Используем префикс только если есть явно указанный pivot.key
    $usePaginationPrefix = false;
    $paginationKey = '';

    // Определяем ключ, только если есть pivot->key
    if (isset($parent) && isset($parent->pivot) && isset($parent->pivot->key) && !empty($parent->pivot->key)) {
        // Если у родителя есть pivot с ключом, используем его
        $paginationKey = $parent->pivot->key;
        $usePaginationPrefix = true;
    } elseif (isset($entity->pivot) && isset($entity->pivot->key) && !empty($entity->pivot->key)) {
        // Если у самой коллекции есть pivot с ключом, используем его
        $paginationKey = $entity->pivot->key;
        $usePaginationPrefix = true;
    }

    // Добавляем контекст, если коллекция находится внутри контент-блока с явным ключом
    if (
        $usePaginationPrefix &&
        isset($contentBlock) &&
        isset($contentBlock->pivot) &&
        isset($contentBlock->pivot->key) &&
        !empty($contentBlock->pivot->key)
    ) {
        $paginationKey = $contentBlock->pivot->key . '_' . $paginationKey;
    }

    // Приоритетно берем значения из объекта пагинации, если он доступен
    if (isset($entity->dataEntities) && method_exists($entity->dataEntities, 'currentPage')) {
        $currentPage = $entity->dataEntities->currentPage();
    } else {
        // Иначе берем из параметров запроса
        $currentPage = $usePaginationPrefix
            ? request()->input("{$paginationKey}_page", 1)
            : request()->input('page', 1);
    }

    if (isset($entity->dataEntities) && method_exists($entity->dataEntities, 'perPage')) {
        $perPage = $entity->dataEntities->perPage();
    } else {
        $perPage = $usePaginationPrefix
            ? request()->input("{$paginationKey}_per_page", 15)
            : request()->input('per_page', 15);
    }

    // Получаем параметры сортировки
    // Приоритет отдаем sort_by_attribute, если он установлен
    $sortByAttribute = $usePaginationPrefix
        ? request()->get($paginationKey . '_sort_by_attribute')
        : request()->get('sort_by_attribute');

    // Если нет sort_by_attribute, смотрим sort_by
    $sortBy = null;
    if (!$sortByAttribute) {
        $sortBy = $usePaginationPrefix ? request()->get($paginationKey . '_sort_by') : request()->get('sort_by');
    }

    $order = $usePaginationPrefix ? request()->get($paginationKey . '_order', 'asc') : request()->get('order', 'asc');
@endphp
<main class="py-12 lg:py-20">
    <div class="centered">
        <div class="flex flex-col gap-4 mb-8 md:flex-row md:items-center md:justify-between md:flex-wrap">
            <h1 class="mb-0">{{ $entity?->link->title ?? 'Каталог' }}:</h1>
            <div class="text-[16px] text-dark text-opacity-50 font-semibold md:text-[20px]">{{ $count }}
                {{ $count > 4 ? 'позиций' : ($count > 1 ? 'позиции' : 'позиция') }}</div>
        </div>
        <div class="xl:flex xl:gap-5">
            <div class="xl:flex-1 xl:min-w-0">
                <div class="flex mb-5 justify-between xl:justify-end">
                    <mobilefiltersburger class="xl:hidden"></mobilefiltersburger>
                    <appsort v-cloak dropdown-class="right-0 origin-top-right" sort-key="{{ $paginationKey }}"
                        :use-prefix="{{ $usePaginationPrefix ? 'true' : 'false' }}"
                        :sort-items="[
                            { text: 'По умолчанию', key: 'order', value: 'asc', current: true },
                            { text: 'По цене (возр.)', key: 'price', value: 'asc', attribute: 'price', current: false },
                            { text: 'По цене (убыв.)', key: 'price', value: 'desc', attribute: 'price',
                            current: false },
                            { text: 'По названию', key: 'name', value: 'asc', current: false }
                        ]">
                    </appsort>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-6 lg:gap-x-6 lg:gap-y-12">
                    @if (isset($entity->dataEntities) && $entity->dataEntities->isNotEmpty())
                        @php
                            $itemsSinceLastClass = 9; // Начинаем с 9, чтобы первый элемент имел шанс получить класс
                        @endphp
                        @foreach ($entity->dataEntities as $item)
                            @php
                                $addClass = '';
                                // Если прошло 9 или более элементов с последнего применения класса, шанс 1/3
                                if ($itemsSinceLastClass >= 9 && rand(1, 3) === 1) {
                                    $addClass = 'md:col-span-2';
                                    $itemsSinceLastClass = 0; // Сбрасываем счетчик
                                } else {
                                    $itemsSinceLastClass++; // Увеличиваем счетчик
                                }
                            @endphp
                            <x-product-item :product="$item" :addClass="$addClass"></x-product-item>
                        @endforeach
                    @else
                        <div class="col-span-3 text-center py-8">
                            <p class="text-dark text-opacity-50">В данной категории пока нет товаров</p>
                        </div>
                    @endif
                </div>
                @if (isset($entity->dataEntities) &&
                        method_exists($entity->dataEntities, 'lastPage') &&
                        $entity->dataEntities->lastPage() > 1)
                    <pagination search-key="{{ $paginationKey }}"
                        :use-prefix="{{ $usePaginationPrefix ? 'true' : 'false' }}" current="{{ $currentPage }}"
                        :total="{{ $entity->dataEntities->total() }}" :per-page="{{ $perPage }}"
                        :sort-by="'{{ $sortBy }}'" :sort-by-attribute="'{{ $sortByAttribute }}'"
                        :order="'{{ $order }}'" class="mt-10 lg:mt-14">
                    </pagination>
                @endif
            </div>
        </div>
    </div>
</main>
