@php
  $image = getItemByPivotKey($contentBlock->images, 'bg')?->url;
@endphp

<section id='block-9ffec698-f7a4-4716-8e1c-06730c09d20e'
    class="catalog-main-block relative z-[5] overflow-hidden bg-left-bottom bg-no-repeat"
    style="background-image: url({{$image}})"
  >
    <div class="swiper js-catalog-main-slider">
      <div class="swiper-wrapper">
        @foreach($contentBlock->renderedChildren as $children)
           {!!$children!!}
        @endforeach
      </div>
    </div>
    <div
      class="flex gap-1 absolute left-1/2 -translate-x-1/2 top-[44%] z-[5] lg:top-auto lg:bottom-12"
    >
      <button
        type="button"
        class="app-button app-button--square app-button--secondary js-catalog-main-slider-prev after:content-['<']"
      ></button>
      <button
        type="button"
        class="app-button app-button--secondary pointer-events-none min-w-[200px]"
      >
        Листать акции
      </button>
      <button
        type="button"
        class="app-button app-button--square app-button--secondary js-catalog-main-slider-next after:content-['>']"
      ></button>
    </div>
  </section>