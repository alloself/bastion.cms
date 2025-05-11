export async function initContactsMap() {
    //@ts-ignore
    ymaps.ready(() => {
        const mapElement = document.querySelector(
            "[data-map]"
        ) as HTMLElement;

        if (!mapElement) {
            return;
        }

        const mapLatLng = mapElement?.dataset["coords"]
            ? JSON.parse(mapElement.dataset["coords"])
            : [];
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

        // disables
        map.behaviors.disable("scrollZoom");
        map.behaviors.disable("drag");
    });
}
