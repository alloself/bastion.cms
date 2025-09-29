@props([
    'collection',
])

@php
    $paginator = $collection?->dataEntities;

    if (! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        return;
    }

    $key = $collection?->pivot?->key;

    $total = $paginator->total();
    $per_page = $paginator->perPage();
    $last_page = $paginator->lastPage();
    $current_page = $paginator->currentPage();

    $prev_page_url = $paginator->previousPageUrl();
    $next_page_url = $paginator->nextPageUrl();
    $current_path = Request::segment(1);

    $prev_link = "/". $current_path . "?" . $key . "_page=" . ($current_page - 1);
    $next_link = "/". $current_path . "?" . $key . "_page=" . ($current_page + 1);
@endphp

<div class="flex flex-wrap justify-center gap-1 mt-10">
    @if (isset($prev_page_url)) 
        <a href="{{$prev_link}}" class="app-button app-button--primary">Назад</a>
    @endif
    @if (isset($next_page_url))
        <a href="{{$next_link}}" class="ml-3 app-button app-button--light">Показать еще</a>
        <a href="{{$next_link}}" class="app-button app-button--light app-button--square">+</a>
    @endif
</div>