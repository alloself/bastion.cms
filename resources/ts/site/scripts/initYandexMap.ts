// Кеш для проверки загрузки API
let isYandexMapsLoaded = false;
let yandexMapsPromise: Promise<void> | null = null;

// Функция для ленивой загрузки Яндекс карт API
function loadYandexMapsAPI(): Promise<void> {
    if (yandexMapsPromise) {
        return yandexMapsPromise;
    }

    yandexMapsPromise = new Promise((resolve, reject) => {
        // Проверяем, не загружен ли уже API
        if (typeof window !== 'undefined' && 'ymaps' in window && (window as any).ymaps?.ready) {
            isYandexMapsLoaded = true;
            resolve();
            return;
        }

        // Создаем скрипт для загрузки API
        const script = document.createElement('script');
        script.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
        script.async = true;
        script.defer = true;
        
        script.onload = () => {
            isYandexMapsLoaded = true;
            resolve();
        };
        
        script.onerror = () => {
            reject(new Error('Не удалось загрузить Яндекс.Карты API'));
        };

        document.head.appendChild(script);
    });

    return yandexMapsPromise;
}

export async function initContactsMap() {
    try {
        // Проверяем наличие элемента карты
        const mapElement = document.querySelector("[data-map]") as HTMLElement;
        if (!mapElement) {
            return;
        }

        // Загружаем API только если он еще не загружен
        if (!isYandexMapsLoaded) {
            await loadYandexMapsAPI();
        }

        // Ждем готовности API
        //@ts-ignore
        ymaps.ready(() => {
            const mapLatLng = mapElement?.dataset["coords"]
                ? JSON.parse(mapElement.dataset["coords"])
                : [55.751574, 37.573856]; // Москва по умолчанию

            //@ts-ignore
            const map = new ymaps.Map(mapElement, {
                center: mapLatLng,
                zoom: 16,
                controls: [],
            });

            //@ts-ignore
            const addressMarker = new ymaps.Placemark(
                mapLatLng,
                {
                    balloonContent: "",
                    iconCaption: "",
                },
                {
                    iconLayout: "default#imageWithContent",
                    iconImageOffset: [-15, -15],
                    iconImageHref: '/public/img/geo-marker.svg',
                    iconImageSize: [30, 30],
                }
            );

            map.geoObjects.add(addressMarker);

            // Отключаем интерактивность для лучшей производительности
            map.behaviors.disable("scrollZoom");
            map.behaviors.disable("drag");
        });

    } catch (error) {
        console.error('Ошибка при инициализации карты:', error);
    }
}
