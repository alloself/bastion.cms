// Конфигурация рабочих часов по дням недели
interface WorkingHoursConfig {
    [key: number]: { // 0 = воскресенье, 1 = понедельник, и т.д.
        start: string; // формат "HH:MM"
        end: string;   // формат "HH:MM"
        isWorkingDay: boolean;
    };
}

// Дефолтная конфигурация рабочих часов
const defaultWorkingHours: WorkingHoursConfig = {
    0: { start: "10:00", end: "19:00", isWorkingDay: true }, // воскресенье
    1: { start: "09:00", end: "21:00", isWorkingDay: true },  // понедельник
    2: { start: "09:00", end: "21:00", isWorkingDay: true },  // вторник
    3: { start: "09:00", end: "21:00", isWorkingDay: true },  // среда
    4: { start: "09:00", end: "21:00", isWorkingDay: true },  // четверг
    5: { start: "10:00", end: "20:00", isWorkingDay: true },  // пятница
    6: { start: "10:00", end: "19:00", isWorkingDay: true }, // суббота
};

/**
 * Функция для проверки времени и окрашивания SVG с id work-clocks в красный цвет
 * @param config - конфигурация рабочих часов по дням недели
 * @returns функция очистки ресурсов
 */
export function initWorkClocksNightMode(config: WorkingHoursConfig = defaultWorkingHours): () => void {
    // Проверяем текущее время
    function isNightTime(): boolean {
        const moscowTime = new Date().toLocaleString('en-US', { timeZone: 'Europe/Moscow' });
        const moscowDate = new Date(moscowTime);
        
        const dayOfWeek = moscowDate.getDay();
        const hour = moscowDate.getHours();
        const minutes = moscowDate.getMinutes();
        const currentTimeMinutes = hour * 60 + minutes;
        
        const dayConfig = config[dayOfWeek];
        
        // Если не рабочий день - всегда "ночь"
        if (!dayConfig.isWorkingDay) {
            return true;
        }
        
        // Преобразуем время начала и конца в минуты
        const [startHour, startMin] = dayConfig.start.split(':').map(Number);
        const [endHour, endMin] = dayConfig.end.split(':').map(Number);
        const startMinutes = startHour * 60 + startMin;
        const endMinutes = endHour * 60 + endMin;
        
        // Проверяем находится ли время вне рабочего интервала
        return currentTimeMinutes < startMinutes || currentTimeMinutes >= endMinutes;
    }

    // Обновляем элемент рабочего времени
    function updateWorkingTimeElement(): void {
        const workingTimeElement = document.getElementById('working-time');
        if (workingTimeElement) {
            const moscowTime = new Date().toLocaleString('en-US', { timeZone: 'Europe/Moscow' });
            const moscowDate = new Date(moscowTime);
            const dayOfWeek = moscowDate.getDay();
            const dayConfig = config[dayOfWeek];
            
            if (!dayConfig.isWorkingDay) {
                workingTimeElement.textContent = "Выходной";
            } else {
                // Преобразуем формат HH:MM в HH.MM
                const start = dayConfig.start.replace(':', '.');
                const end = dayConfig.end.replace(':', '.');
                workingTimeElement.textContent = `${start}-${end}`;
            }
        }
    }

    // Функция для окрашивания SVG в красный цвет
    function setNightMode(): void {
        const svgElements = document.querySelectorAll('svg#work-clocks');
        if (svgElements.length > 0) {
            const isNight = isNightTime();
            
            svgElements.forEach((svg) => {
                if (isNight) {
                    (svg as SVGElement).style.fill = '#c92a2a';
                    (svg as SVGElement).style.color = '#c92a2a';
                    
                    const container = svg.closest('.flex');
                    if (container) {
                        const textBlock = container.querySelector('.font-medium');
                        if (textBlock) {
                            (textBlock as HTMLElement).style.color = '#c92a2a';
                        }
                    }
                } else {
                    (svg as SVGElement).style.fill = '';
                    (svg as SVGElement).style.color = '';
                    
                    const container = svg.closest('.flex');
                    if (container) {
                        const textBlock = container.querySelector('.font-medium');
                        if (textBlock) {
                            (textBlock as HTMLElement).style.color = '';
                        }
                    }
                }
            });
        }

        // Обновляем элемент рабочего времени
        updateWorkingTimeElement();
    }

    // Устанавливаем начальное состояние
    setNightMode();
    
    // Устанавливаем интервал для обновления каждую минуту
    const intervalId = setInterval(setNightMode, 60000);
    
    // Для очистки при необходимости
    return () => {
        clearInterval(intervalId);
    };
}

/**
 * Инициализирует ночной режим для SVG элементов с id work-clocks
 * @param config - конфигурация рабочих часов по дням недели
 */
export function applyNightModeToWorkClocks(config?: WorkingHoursConfig): void {
    let cleanup: (() => void) | null = null;
    
    const initNightMode = () => {
        cleanup = initWorkClocksNightMode(config);
    };
    
    // Если документ уже загружен, применяем сразу
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initNightMode();
    } else {
        // Иначе ждем загрузки документа
        document.addEventListener('DOMContentLoaded', initNightMode);
    }
    
    // При выгрузке страницы очищаем ресурсы
    window.addEventListener('unload', () => {
        if (cleanup) {
            cleanup();
        }
    });
}

// Экспортируем для использования
export type { WorkingHoursConfig };
export { defaultWorkingHours }; 