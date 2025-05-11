import { ref, computed } from 'vue'

export interface IPickUpPoint {
    address: string,
    workHours: string,
    latlng: [number, number]
}

export interface IGeoLocation {
    city: string,
    latlng: [number, number],
    pickUpPoints: IPickUpPoint[],
}

export const locationsList = ref<IGeoLocation[]>([
    {
        city: 'Санкт-Петербург',
        latlng: [59.944220, 30.354145],
        pickUpPoints: [
            {
                address: 'Большая Московская ул, 112', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [59.925971, 30.346653]
            },
            {
                address: 'Певческий переулок, 5', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [59.958998, 30.327555]
            },
            {
                address: 'Набережная реки Смоленки, 5-7И', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [59.949668, 30.260070]
            },
            {
                address: 'Улица Профессора Попова, 43', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [59.971204, 30.291324]
            },
            {
                address: 'Студенческая улица, 5', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [59.986224, 30.330847]
            },
            {
                address: 'Парголовский переулок, 16', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [60.020455, 30.293383]
            },
        ]
    },
    {
        city: 'Москва', 
        latlng: [55.755864, 37.617698],
        pickUpPoints: [
            {
                address: 'Никольская улица, 15с1', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [55.757484, 37.622657]
            },
            {
                address: 'Садовая-Спасская улица, 19/1', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [55.769600, 37.647526]
            },
            {
                address: 'Делегатская улица, 20', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [55.776402, 37.615703]
            },
            {
                address: '1-я Миусская улица, 22с4', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [55.780499, 37.594430]
            },
            {
                address: '2-й Лесной переулок, 11с1', 
                workHours: 'пн-вс: 08:00-22:00', 
                latlng: [55.780896, 37.587315]
            },
        ]
    },
    {
        city: 'Абакан', 
        latlng: [53.721152, 91.442396],
        pickUpPoints: []
    },
    {
        city: 'Архангельск', 
        latlng: [64.539911, 40.515762],
        pickUpPoints: []
    },
    {
        city: 'Астрахань', 
        latlng: [46.347614, 48.030178],
        pickUpPoints: []
    },
    {
        city: 'Барнаул', 
        latlng: [53.346785, 83.776856],
        pickUpPoints: []
    },
    {
        city: 'Белгород', 
        latlng: [50.595414, 36.587277],
        pickUpPoints: []
    },
    {
        city: 'Благовещенск', 
        latlng: [50.257456, 127.534611],
        pickUpPoints: []
    },
    {
        city: 'Великий Новгород', 
        latlng: [58.522857, 31.269816],
        pickUpPoints: []
    },
    {
        city: 'Владивосток', 
        latlng: [43.115542, 131.885494],
        pickUpPoints: []
    },
    {
        city: 'Владимир', 
        latlng: [56.129057, 40.406635],
        pickUpPoints: []
    },
    {
        city: 'Воронеж', 
        latlng: [51.660781, 39.200296],
        pickUpPoints: []
    },
    {
        city: 'Иваново', 
        latlng: [57.000353, 40.973930],
        pickUpPoints: []
    },
    {
        city: 'Ижевск', 
        latlng: [56.852676, 53.206900],
        pickUpPoints: []
    },
    {
        city: 'Иркутск', 
        latlng: [52.289588, 104.280606],
        pickUpPoints: []
    },
    {
        city: 'Йошкар-Ола', 
        latlng: [56.631600, 47.886178],
        pickUpPoints: []
    },
    {
        city: 'Казань', 
        latlng: [55.796127, 49.106414],
        pickUpPoints: []
    },
])

export const allPickUpPointsLength = computed<number>(() => {
    const result = locationsList.value.reduce<number>((acc, cur) => {
        return acc + cur.pickUpPoints.length
    }, 0)
    return result
})

export const currentLocation = ref<IGeoLocation>(locationsList.value[0])

export const currentPickUpPoint = ref<IPickUpPoint | null>(null)