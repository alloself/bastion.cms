<section id='block-9ffec698-f7a4-4716-8e1c-06730c09d20e'
    class="catalog-main-block relative z-[5] overflow-hidden bg-left-bottom bg-no-repeat"
    style="background-image: url('/img/catalog-main-bg.png')"
  >
    <div class="swiper js-catalog-main-slider">
      <div class="swiper-wrapper">
        <div v-for="s in 5" :key="s" class="swiper-slide">
          <div class="grid lg:grid-cols-2">
            <div class="px-5 pt-[140px] pb-10 xl:pt-[200px]">
              <div class="lg:max-w-[680px] lg:ml-auto [&>*]:md:max-w-[80%]">
                <div class="text-simple text-brand/60 mb-5">
                  Строительные товары
                </div>
                <h1 class="h2 mb-5 text-balance">
                  Название акции в 1-2 предложения
                </h1>
                <p class="text-[13px] text-brand/60 text-balance">
                  Создаем проекты любой сложности благодаря
                  высококвалифицированным инженерам и современному программно
                  аппаратному комплексу.
                </p>
                <div class="flex flex-wrap gap-1 pt-8">
                  <a href="#" class="app-button app-button--secondary"
                    >Узнать подробнее</a
                  >
                  <a
                    href="#"
                    class="app-button app-button--secondary app-button--square"
                    >+</a
                  >
                </div>
              </div>
            </div>
            <div class="h-[680px] lg:h-[780px]">
              <img
                class="block w-full h-full object-cover"
                src="/img/catalog-main-pic.png"
                alt=""
              />
            </div>
          </div>
        </div>
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