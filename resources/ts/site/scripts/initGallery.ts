// Динамический импорт BaguetteBox для избежания проблем с глобальными объектами
export async function initGallery() {
    // Проверяем наличие элементов перед инициализацией
    const galleryElements = document.querySelectorAll('.js-lightbox');
    if (galleryElements.length === 0) {
        return;
    }

    try {
        // Динамически импортируем BaguetteBox
        const baguetteModule = await import('baguettebox.js');
        
        // Получаем правильную ссылку на baguetteBox с правильной типизацией
        const baguetteBox = baguetteModule.default || (baguetteModule as any).baguetteBox || (window as any).baguetteBox;
        
        if (baguetteBox && typeof baguetteBox.run === 'function') {
            baguetteBox.run('.js-lightbox', {
                animation: 'slideIn',
                noScrollbars: true,
                buttons: 'auto',
                fullScreen: false,
                async: true
            });
        } else {
            console.warn('BaguetteBox не найден или не имеет метода run');
        }
    } catch (error) {
        console.warn('Ошибка инициализации галереи:', error);
        
        // Fallback: пытаемся использовать глобальный объект
        try {
            const globalBaguette = (window as any).baguetteBox;
            if (globalBaguette && typeof globalBaguette.run === 'function') {
                globalBaguette.run('.js-lightbox', {});
            }
        } catch (fallbackError) {
            console.warn('Fallback для галереи также не сработал:', fallbackError);
        }
    }
}