/**
 * Функция для проверки времени (с 20:00 до 10:00 GMT+3) и окрашивания SVG с id work-clocks в красный цвет
 * @returns функция очистки ресурсов
 */
export function initWorkClocksNightMode(): () => void {
    // Проверяем текущее время
    function isNightTime(): boolean {
        // Используем Date.prototype.toLocaleString для получения времени в GMT+3
        const moscowTime = new Date().toLocaleString('en-US', { timeZone: 'Europe/Moscow' });
        const moscowDate = new Date(moscowTime);
        
        // Получаем часы в GMT+3
        const hour = moscowDate.getHours();
        
        // Проверяем находится ли время в интервале с 20:00 до 10:00
        return hour >= 20 || hour < 10;
    }

    // Функция для окрашивания SVG в красный цвет
    function setNightMode(): void {
        const svgElements = document.querySelectorAll('svg#work-clocks');
        if (svgElements.length > 0) {
            const isNight = isNightTime();
            
            // Применяем цвет к SVG с id="work-clocks" и блоку с текстом
            svgElements.forEach((svg) => {
                if (isNight) {
                    // Окрашиваем SVG
                    (svg as SVGElement).style.fill = '#c92a2a';
                    (svg as SVGElement).style.color = '#c92a2a';
                    
                    // Находим родительский контейнер с текстом времени
                    const container = svg.closest('.flex');
                    if (container) {
                        // Найдем блок с текстом (обычно это последний div с текстом внутри контейнера)
                        const textBlock = container.querySelector('.font-medium');
                        if (textBlock) {
                            (textBlock as HTMLElement).style.color = '#c92a2a';
                        }
                    }
                } else {
                    // Возвращаем стандартные цвета
                    (svg as SVGElement).style.fill = '';
                    (svg as SVGElement).style.color = '';
                    
                    // Находим родительский контейнер с текстом времени
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
    }

    // Устанавливаем начальное состояние
    setNightMode();
    
    // Устанавливаем интервал для обновления каждую минуту
    const intervalId = setInterval(setNightMode, 60000);
    
    // Настраиваем MutationObserver для отслеживания изменений в DOM
    const observer = new MutationObserver((mutations) => {
        // Проверяем, добавились ли новые узлы
        let needsUpdate = false;
        
        for (const mutation of mutations) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                needsUpdate = true;
                break;
            }
        }
        
        if (needsUpdate) {
            setNightMode();
        }
    });
    
    // Запускаем наблюдение за всем документом на предмет изменений
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Для очистки при необходимости
    return () => {
        clearInterval(intervalId);
        observer.disconnect();
    };
}

/**
 * Инициализирует ночной режим для SVG элементов с id work-clocks
 * Эта функция вызывается при загрузке страницы
 */
export function applyNightModeToWorkClocks(): void {
    let cleanup: (() => void) | null = null;
    
    const initNightMode = () => {
        cleanup = initWorkClocksNightMode();
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