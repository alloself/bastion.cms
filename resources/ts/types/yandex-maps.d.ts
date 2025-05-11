declare namespace ymaps {
  function ready(callback: () => void): void;
  
  class Map {
    constructor(
      element: string | HTMLElement,
      options: {
        center?: number[];
        zoom?: number;
        controls?: string[];
        [key: string]: any;
      }
    );
    
    geoObjects: GeoObjectCollection;
    behaviors: Behaviors;
  }
  
  interface Behaviors {
    disable(behavior: string): void;
    enable(behavior: string): void;
  }
  
  class Placemark {
    constructor(
      coordinates: number[],
      properties?: {
        [key: string]: any;
      },
      options?: {
        [key: string]: any;
      }
    );

    balloon: {
      events: {
        add(events: string[], callback: (event: any) => void): void;
      }
    };
  }
  
  class GeoObjectCollection {
    add(geoObject: Placemark | any): this;
    remove(geoObject: Placemark | any): this;
    removeAll(): this;
    getLength(): number;
  }
} 