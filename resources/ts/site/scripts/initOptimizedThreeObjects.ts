import { OptimizedThreeLampScene } from './OptimizedThreeLampScene';
import { TextureOptimizer } from './TextureOptimizer';

// Ленивый импорт для избежания блокировки
let OptimizedThreeLampSceneClass: typeof OptimizedThreeLampScene | null = null;

async function loadOptimizedThreeJS() {
    if (!OptimizedThreeLampSceneClass) {
        const module = await import("./OptimizedThreeLampScene");
        OptimizedThreeLampSceneClass = module.OptimizedThreeLampScene;
    }
    return OptimizedThreeLampSceneClass;
}

export async function initOptimizedThreeObjects() {
    // Проверяем, есть ли элементы для Three.js на странице
    const threeElements = document.querySelectorAll("[class*='three-lamp']");
    if (threeElements.length === 0) {
        console.log('Three.js элементы не найдены, пропускаем инициализацию');
        return;
    }

    console.log(`Найдено ${threeElements.length} Three.js элементов для оптимизации`);

    // Загружаем оптимизированный класс только когда он действительно нужен
    const OptimizedThreeLampSceneClass = await loadOptimizedThreeJS();
    const textureOptimizer = TextureOptimizer.getInstance();

    // Предзагружаем общие текстуры для лучшей производительности
    const commonTextures = [
        {
            path: `/public/models/lamp/lamp-texture.jpg`,
            quality: 'medium' as const,
            generateMipmaps: true
        }
    ];

    // Предзагружаем environment текстуры
    const envTexturePaths = [
        "/public/models/lamp/env-13.webp",
        "/public/models/lamp/env-3.jpg",
        "/public/models/lamp/env-4.jpg",
        "/public/models/lamp/env-7.jpg",
        "/public/models/lamp/env-9.jpg"
    ];

    // Создаем конфигурации для каждого типа лампы
    const lampConfigs = [
        {
            selector: ".three-lamp--main-right",
            key: "mainRightLamp",
            config: {
                filePath: `/public/models/lamp/lamp-draco.glb`,
                texturePath: `/public/models/lamp/lamp-texture.jpg`,
                envImagePaths: [
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp"
                ],
                modelInitialRotation: {
                    x: 2.6,
                    y: 0,
                    z: 0.6,
                },
                quality: 'medium' as const
            }
        },
        {
            selector: ".three-lamp--main-left",
            key: "mainLeftLamp",
            config: {
                filePath: `/public/models/lamp/lamp-draco.glb`,
                texturePath: `/public/models/lamp/lamp-texture.jpg`,
                envImagePaths: [
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp"
                ],
                modelInitialRotation: {
                    x: -0.4,
                    z: 1.2,
                    y: 1.2,
                },
                modelMoveAnimationSettings: {
                    direction: "right",
                    axis: "y",
                    value: 0.001,
                    moreValue: [1, -1],
                },
                quality: 'medium' as const
            }
        },
        {
            selector: ".three-lamp--main-banner",
            key: "mainBannerLamp",
            config: {
                filePath: `/public/models/lamp/lamp-draco.glb`,
                texturePath: `/public/models/lamp/lamp-texture.jpg`,
                envImagePaths: [
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp"
                ],
                modelInitialRotation: {
                    x: 3.5,
                    y: 0,
                    z: -0.6,
                },
                modelMoveAnimationSettings: {
                    direction: "right",
                    axis: "y",
                    value: 0.00054,
                    moreValue: [0.7, -0.6],
                },
                quality: 'high' as const // Главный баннер - высокое качество
            }
        },
        {
            selector: ".three-lamp--subscribe-left",
            key: "subscribeLeftLamp",
            config: {
                filePath: `/public/models/lamp/lamp-draco.glb`,
                texturePath: `/public/models/lamp/lamp-texture.jpg`,
                envImagePaths: [
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp",
                    "/public/models/lamp/env-13.webp"
                ],
                modelInitialRotation: {
                    x: -0.4,
                    z: 1.2,
                    y: 1.2,
                },
                modelMoveAnimationSettings: {
                    direction: "left",
                    axis: "y",
                    value: 0.00054,
                    moreValue: [1, -1],
                },
                quality: 'low' as const // Второстепенные элементы - низкое качество
            }
        },
        {
            selector: ".three-lamp--subscribe-right",
            key: "subscribeRightLamp",
            config: {
                filePath: `/public/models/lamp/lamp-draco.glb`,
                texturePath: `/public/models/lamp/lamp-texture.jpg`,
                envImagePaths: [
                    "/public/models/lamp/env-3.jpg",
                    "/public/models/lamp/env-4.jpg",
                    "/public/models/lamp/env-7.jpg",
                    "/public/models/lamp/env-4.jpg",
                    "/public/models/lamp/env-9.jpg",
                    "/public/models/lamp/env-4.jpg"
                ],
                modelInitialRotation: {
                    x: -2.8,
                    y: 0,
                    z: 0.6,
                },
                quality: 'low' as const
            }
        },
        {
            selector: ".three-lamp--footer",
            key: "footerLamp",
            config: {
                filePath: `/public/models/lamp/lamp-draco.glb`,
                texturePath: `/public/models/lamp/lamp-texture.jpg`,
                envImagePaths: [
                    "/public/models/lamp/env-3.jpg",
                    "/public/models/lamp/env-4.jpg",
                    "/public/models/lamp/env-7.jpg",
                    "/public/models/lamp/env-4.jpg",
                    "/public/models/lamp/env-9.jpg",
                    "/public/models/lamp/env-4.jpg"
                ],
                modelInitialRotation: {
                    x: 1.75,
                    y: 0,
                    z: -0.6,
                },
                modelMoveAnimationSettings: {
                    direction: "right",
                    axis: "y",
                    value: 0.00054,
                    moreValue: [0.7, -0.6],
                },
                quality: 'low' as const // Футер - низкое качество
            }
        }
    ];

    // Предзагружаем общие текстуры в фоне
    textureOptimizer.preloadTextures(commonTextures).catch(error => {
        console.warn('Ошибка предзагрузки текстур:', error);
    });

    // Создаем экземпляры только для существующих элементов
    const lampInstances: Record<string, OptimizedThreeLampScene> = {};
    let createdInstances = 0;

    for (const lampConfig of lampConfigs) {
        const element = document.querySelector(lampConfig.selector) as HTMLElement;
        if (element) {
            try {
                lampInstances[lampConfig.key] = new OptimizedThreeLampSceneClass({
                    ...lampConfig.config,
                    renderElem: element
                });
                createdInstances++;
                console.log(`Создан оптимизированный экземпляр: ${lampConfig.key} (качество: ${lampConfig.config.quality})`);
            } catch (error) {
                console.warn(`Ошибка создания экземпляра ${lampConfig.key}:`, error);
            }
        }
    }

    console.log(`Создано ${createdInstances} оптимизированных Three.js экземпляров`);

    // Настраиваем обработчики событий для оптимизации производительности
    setupPerformanceOptimizations(lampInstances);

    // Настраиваем мониторинг производительности
    setupPerformanceMonitoring(lampInstances, textureOptimizer);

    return lampInstances;
}

/**
 * Настраивает оптимизации производительности
 */
function setupPerformanceOptimizations(lampInstances: Record<string, OptimizedThreeLampScene>) {
    // Обработчик изменения видимости страницы
    document.addEventListener('visibilitychange', () => {
        Object.values(lampInstances).forEach(instance => {
            if (document.hidden) {
                // Приостанавливаем все анимации при скрытии страницы
                instance.dispose();
            }
        });
    });

    // Обработчик изменения размера окна с дебаунсом
    let resizeTimeout: number;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = window.setTimeout(() => {
            // ResizeObserver в OptimizedThreeLampScene автоматически обработает изменения
            console.log('Размер окна изменен, Three.js сцены адаптированы');
        }, 250);
    });

    // Обработчик низкого заряда батареи (если поддерживается)
    if ('getBattery' in navigator) {
        (navigator as any).getBattery().then((battery: any) => {
            const handleBatteryChange = () => {
                if (battery.level < 0.2 && !battery.charging) {
                    // Переключаем на низкое качество при низком заряде
                    Object.values(lampInstances).forEach(instance => {
                        instance.setQuality('low');
                    });
                    console.log('Переключено на низкое качество из-за низкого заряда батареи');
                }
            };

            battery.addEventListener('levelchange', handleBatteryChange);
            battery.addEventListener('chargingchange', handleBatteryChange);
        });
    }
}

/**
 * Настраивает мониторинг производительности
 */
function setupPerformanceMonitoring(
    lampInstances: Record<string, OptimizedThreeLampScene>,
    textureOptimizer: TextureOptimizer
) {
    // Периодический мониторинг производительности
    setInterval(() => {
        const memoryStats = textureOptimizer.getMemoryStats();
        
        // Предупреждение при превышении бюджета памяти
        if (memoryStats.usagePercent > 85) {
            console.warn(`Превышен бюджет памяти текстур: ${memoryStats.usagePercent.toFixed(1)}%`);
            
            // Автоматическая очистка кэша при критическом превышении
            if (memoryStats.usagePercent > 95) {
                textureOptimizer.clearCache();
                console.log('Автоматическая очистка кэша текстур выполнена');
                
                // Снижаем качество всех сцен после очистки
                Object.values(lampInstances).forEach(instance => {
                    const currentStats = instance.getPerformanceStats();
                    if (currentStats.quality !== 'low') {
                        instance.setQuality('low');
                        console.log('Качество снижено до "low" из-за нехватки памяти');
                    }
                });
            }
        }

        // Логирование статистики производительности (только в development)
        if (typeof window !== 'undefined' && (window as any).DEBUG_MODE) {
            console.log('Статистика Three.js:', {
                память: `${(memoryStats.currentUsage / 1024 / 1024).toFixed(1)}MB / ${(memoryStats.budget / 1024 / 1024).toFixed(1)}MB`,
                использование: `${memoryStats.usagePercent.toFixed(1)}%`,
                текстуры: memoryStats.textureCount,
                cubeТекстуры: memoryStats.cubeTextureCount,
                активныеСцены: Object.keys(lampInstances).length,
                поддержкаСжатия: memoryStats.compressionSupport
            });
        }
    }, 15000); // Каждые 15 секунд для более частого мониторинга

    // Мониторинг FPS (если доступно)
    if ('requestIdleCallback' in window) {
        const monitorFPS = () => {
            let lastTime = performance.now();
            let frameCount = 0;

            const measureFPS = () => {
                frameCount++;
                const currentTime = performance.now();
                
                if (currentTime - lastTime >= 1000) {
                    const fps = Math.round((frameCount * 1000) / (currentTime - lastTime));
                    
                    // Автоматическое снижение качества при низком FPS
                    if (fps < 20) {
                        Object.values(lampInstances).forEach(instance => {
                            const stats = instance.getPerformanceStats();
                            if (stats.quality !== 'low') {
                                instance.setQuality('low');
                                console.log(`Снижено качество до 'low' из-за низкого FPS: ${fps}`);
                            }
                        });
                    }
                    
                    frameCount = 0;
                    lastTime = currentTime;
                }
                
                requestAnimationFrame(measureFPS);
            };

            requestIdleCallback(() => {
                measureFPS();
            });
        };

        monitorFPS();
    }
}

/**
 * Создает текстурный атлас для оптимизации
 */
export async function createOptimizedTextureAtlas() {
    const textureOptimizer = TextureOptimizer.getInstance();
    
    // Создаем атлас из часто используемых текстур
    const commonTexturePaths = [
        "/public/models/lamp/lamp-texture.jpg",
        "/public/models/lamp/env-13.webp",
        "/public/models/lamp/env-3.jpg",
        "/public/models/lamp/env-4.jpg"
    ];

    try {
        const atlas = await textureOptimizer.createTextureAtlas(commonTexturePaths, 2048);
        console.log('Создан оптимизированный текстурный атлас');
        return atlas;
    } catch (error) {
        console.warn('Ошибка создания текстурного атласа:', error);
        return null;
    }
}

/**
 * Получает общую статистику оптимизации
 */
export function getOptimizationStats() {
    const textureOptimizer = TextureOptimizer.getInstance();
    const memoryStats = textureOptimizer.getMemoryStats();
    
    return {
        textureMemory: {
            used: `${(memoryStats.currentUsage / 1024 / 1024).toFixed(1)}MB`,
            budget: `${(memoryStats.budget / 1024 / 1024).toFixed(1)}MB`,
            percentage: `${memoryStats.usagePercent.toFixed(1)}%`
        },
        textureCount: memoryStats.textureCount,
        cubeTextureCount: memoryStats.cubeTextureCount,
        compressionSupport: memoryStats.compressionSupport,
        recommendations: generateOptimizationRecommendations(memoryStats)
    };
}

/**
 * Генерирует рекомендации по оптимизации
 */
function generateOptimizationRecommendations(stats: any): string[] {
    const recommendations: string[] = [];
    
    if (stats.usagePercent > 80) {
        recommendations.push('Рассмотрите снижение качества текстур или очистку кэша');
    }
    
    if (!stats.compressionSupport.basisUniversal) {
        recommendations.push('Браузер не поддерживает Basis Universal - рассмотрите fallback форматы');
    }
    
    if (stats.textureCount > 20) {
        recommendations.push('Большое количество текстур - рассмотрите создание атласа');
    }
    
    if (stats.compressionSupport.s3tc || stats.compressionSupport.etc2) {
        recommendations.push('Используйте сжатые форматы текстур для лучшей производительности');
    }
    
    return recommendations;
} 