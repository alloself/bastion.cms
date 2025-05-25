import { TextureCompressionUtils } from './TextureCompressionUtils';

/**
 * Утилита для создания оптимизированных версий текстур
 * Создает WebP, AVIF и различные размеры для адаптивной загрузки
 */

interface OptimizationTask {
    inputPath: string;
    outputBaseName: string;
    formats: ('webp' | 'avif' | 'jpg')[];
    qualities: ('low' | 'medium' | 'high')[];
}

class TextureOptimizationTool {
    private static instance: TextureOptimizationTool;
    private canvas: HTMLCanvasElement;
    private ctx: CanvasRenderingContext2D;

    private constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d')!;
    }

    public static getInstance(): TextureOptimizationTool {
        if (!TextureOptimizationTool.instance) {
            TextureOptimizationTool.instance = new TextureOptimizationTool();
        }
        return TextureOptimizationTool.instance;
    }

    /**
     * Создает оптимизированные версии текстуры для проекта
     */
    async optimizeProjectTextures(): Promise<void> {
        console.log('🚀 Начинаем оптимизацию текстур проекта...');

        const tasks: OptimizationTask[] = [
            {
                inputPath: '/public/models/lamp/lamp-texture.jpg',
                outputBaseName: 'lamp-texture',
                formats: ['webp', 'jpg'],
                qualities: ['low', 'medium', 'high']
            },
            {
                inputPath: '/public/models/lamp/env-13.webp',
                outputBaseName: 'env-13',
                formats: ['webp', 'jpg'],
                qualities: ['low', 'medium', 'high']
            },
            {
                inputPath: '/public/models/lamp/env-3.jpg',
                outputBaseName: 'env-3',
                formats: ['webp', 'jpg'],
                qualities: ['low', 'medium', 'high']
            },
            {
                inputPath: '/public/models/lamp/env-4.jpg',
                outputBaseName: 'env-4',
                formats: ['webp', 'jpg'],
                qualities: ['low', 'medium', 'high']
            },
            {
                inputPath: '/public/models/lamp/env-7.jpg',
                outputBaseName: 'env-7',
                formats: ['webp', 'jpg'],
                qualities: ['low', 'medium', 'high']
            },
            {
                inputPath: '/public/models/lamp/env-9.jpg',
                outputBaseName: 'env-9',
                formats: ['webp', 'jpg'],
                qualities: ['low', 'medium', 'high']
            }
        ];

        const results = [];
        for (const task of tasks) {
            try {
                const result = await this.processOptimizationTask(task);
                results.push(result);
                console.log(`✅ Обработано: ${task.inputPath}`);
            } catch (error) {
                console.error(`❌ Ошибка обработки ${task.inputPath}:`, error);
            }
        }

        console.log('🎉 Оптимизация завершена!');
        this.generateOptimizationReport(results);
    }

    /**
     * Обрабатывает одну задачу оптимизации
     */
    private async processOptimizationTask(task: OptimizationTask): Promise<any> {
        try {
            // Загружаем исходное изображение
            const img = await TextureCompressionUtils.loadImage(task.inputPath);
            const originalInfo = await TextureCompressionUtils.getTextureInfo(task.inputPath);

            console.log(`📊 Исходное изображение: ${originalInfo.width}x${originalInfo.height}, ${(originalInfo.size / 1024).toFixed(1)}KB`);

            const optimizedVersions = [];

            // Создаем версии для каждого качества и формата
            for (const quality of task.qualities) {
                for (const format of task.formats) {
                    const optimized = await this.createOptimizedVersion(img, format, quality);
                    optimizedVersions.push({
                        quality,
                        format,
                        ...optimized,
                        fileName: `${task.outputBaseName}-${quality}.${format}`
                    });
                }
            }

            // Создаем WebGL-оптимизированную версию
            const webglOptimized = await TextureCompressionUtils.optimizeForWebGL(img, {
                quality: 85,
                format: 'webp'
            });

            optimizedVersions.push({
                quality: 'webgl',
                format: 'webp',
                ...webglOptimized,
                fileName: `${task.outputBaseName}-webgl.webp`
            });

            return {
                original: originalInfo,
                optimized: optimizedVersions,
                inputPath: task.inputPath,
                outputBaseName: task.outputBaseName
            };

        } catch (error) {
            console.error(`Ошибка обработки задачи:`, error);
            throw error;
        }
    }

    /**
     * Создает оптимизированную версию изображения
     */
    private async createOptimizedVersion(
        img: HTMLImageElement,
        format: 'webp' | 'avif' | 'jpg',
        quality: 'low' | 'medium' | 'high'
    ) {
        const qualitySettings = {
            low: { quality: 60, maxWidth: 512, maxHeight: 512 },
            medium: { quality: 75, maxWidth: 1024, maxHeight: 1024 },
            high: { quality: 90, maxWidth: 2048, maxHeight: 2048 }
        };

        const settings = qualitySettings[quality];

        return await TextureCompressionUtils.convertImage(img, {
            format,
            quality: settings.quality,
            maxWidth: settings.maxWidth,
            maxHeight: settings.maxHeight
        });
    }

    /**
     * Генерирует отчет об оптимизации
     */
    private generateOptimizationReport(results: any[]) {
        console.log('\n📋 ОТЧЕТ ОБ ОПТИМИЗАЦИИ ТЕКСТУР');
        console.log('=' .repeat(50));

        let totalOriginalSize = 0;
        let totalOptimizedSize = 0;

        results.forEach(result => {
            console.log(`\n📁 ${result.inputPath}`);
            console.log(`   Исходный размер: ${(result.original.size / 1024).toFixed(1)}KB`);
            
            totalOriginalSize += result.original.size;

            result.optimized.forEach((opt: any) => {
                const compressionRatio = ((result.original.size - opt.info.size) / result.original.size * 100);
                console.log(`   ${opt.fileName}: ${(opt.info.size / 1024).toFixed(1)}KB (${compressionRatio.toFixed(1)}% экономии)`);
                totalOptimizedSize += opt.info.size;
            });
        });

        const totalSavings = ((totalOriginalSize - totalOptimizedSize) / totalOriginalSize * 100);
        console.log('\n💾 ОБЩАЯ СТАТИСТИКА:');
        console.log(`   Исходный размер: ${(totalOriginalSize / 1024 / 1024).toFixed(1)}MB`);
        console.log(`   Оптимизированный: ${(totalOptimizedSize / 1024 / 1024).toFixed(1)}MB`);
        console.log(`   Общая экономия: ${totalSavings.toFixed(1)}%`);

        // Генерируем рекомендации
        this.generateRecommendations(results);
    }

    /**
     * Генерирует рекомендации по использованию
     */
    private generateRecommendations(results: any[]) {
        console.log('\n💡 РЕКОМЕНДАЦИИ:');
        console.log('=' .repeat(30));

        console.log('1. Для мобильных устройств используйте версии -low');
        console.log('2. Для планшетов используйте версии -medium');
        console.log('3. Для desktop используйте версии -high');
        console.log('4. WebGL-версии оптимизированы для Three.js');
        console.log('5. WebP обеспечивает лучшее сжатие при поддержке браузером');

        // Создаем пример кода для TextureOptimizer
        console.log('\n🔧 ПРИМЕР ИНТЕГРАЦИИ:');
        console.log(`
// Обновите selectOptimalFormat в TextureOptimizer.ts:
private selectOptimalFormat(config: TextureConfig): string {
    const basePath = config.path.replace(/\\.[^/.]+$/, "");
    const quality = config.quality || 'medium';
    
    // Проверяем поддержку WebP
    if (this.supportsWebP()) {
        return \`\${basePath}-\${quality}.webp\`;
    }
    
    // Fallback к JPEG
    return \`\${basePath}-\${quality}.jpg\`;
}
        `);
    }

    /**
     * Создает текстурный атлас из оптимизированных текстур
     */
    async createTextureAtlas(texturePaths: string[], atlasSize: number = 2048): Promise<any> {
        console.log('🎨 Создаем текстурный атлас...');

        try {
            // Загружаем все изображения
            const images = await Promise.all(
                texturePaths.map(path => TextureCompressionUtils.loadImage(path))
            );

            // Создаем атлас
            const atlas = await TextureCompressionUtils.createTextureAtlas(images, atlasSize);

            console.log(`✅ Атлас создан: ${atlasSize}x${atlasSize}, ${(atlas.blob.size / 1024).toFixed(1)}KB`);
            console.log('📍 Карта текстур:', atlas.mapping);

            return atlas;

        } catch (error) {
            console.error('❌ Ошибка создания атласа:', error);
            throw error;
        }
    }

    /**
     * Анализирует все текстуры проекта
     */
    async analyzeProjectTextures(): Promise<void> {
        console.log('🔍 Анализируем текстуры проекта...');

        const texturePaths = [
            '/public/models/lamp/lamp-texture.jpg',
            '/public/models/lamp/env-13.webp',
            '/public/models/lamp/env-3.jpg',
            '/public/models/lamp/env-4.jpg',
            '/public/models/lamp/env-7.jpg',
            '/public/models/lamp/env-9.jpg'
        ];

        for (const path of texturePaths) {
            try {
                const analysis = await TextureCompressionUtils.analyzeTexture(path);
                
                console.log(`\n📊 ${path}:`);
                console.log(`   Размер: ${analysis.info.width}x${analysis.info.height}`);
                console.log(`   Формат: ${analysis.info.format.toUpperCase()}`);
                console.log(`   Размер файла: ${(analysis.info.size / 1024).toFixed(1)}KB`);
                console.log(`   Потенциальная экономия: ${(analysis.estimatedSavings * 100).toFixed(1)}%`);
                
                if (analysis.recommendations.length > 0) {
                    console.log('   Рекомендации:');
                    analysis.recommendations.forEach(rec => console.log(`   • ${rec}`));
                }

            } catch (error) {
                console.error(`❌ Ошибка анализа ${path}:`, error);
            }
        }
    }

    /**
     * Создает превью всех оптимизированных версий
     */
    async createPreviewGallery(imagePath: string): Promise<string[]> {
        console.log(`🖼️ Создаем превью для ${imagePath}...`);

        try {
            const img = await TextureCompressionUtils.loadImage(imagePath);
            const previews: string[] = [];

            // Создаем превью для разных качеств
            const qualities = ['low', 'medium', 'high'];
            
            for (const quality of qualities) {
                const optimized = await this.createOptimizedVersion(img, 'webp', quality as any);
                
                // Создаем превью 256x256
                const previewImg = new Image();
                previewImg.src = optimized.dataUrl;
                
                await new Promise(resolve => {
                    previewImg.onload = resolve;
                });

                const preview = TextureCompressionUtils.createPreview(previewImg, 256);
                previews.push(preview);
                
                console.log(`   ${quality}: ${(optimized.info.size / 1024).toFixed(1)}KB`);
            }

            return previews;

        } catch (error) {
            console.error('❌ Ошибка создания превью:', error);
            throw error;
        }
    }
}

// Экспортируем функции для использования в консоли браузера
(window as any).optimizeTextures = async () => {
    const tool = TextureOptimizationTool.getInstance();
    await tool.optimizeProjectTextures();
};

(window as any).analyzeTextures = async () => {
    const tool = TextureOptimizationTool.getInstance();
    await tool.analyzeProjectTextures();
};

(window as any).createTextureAtlas = async (paths: string[], size = 2048) => {
    const tool = TextureOptimizationTool.getInstance();
    return await tool.createTextureAtlas(paths, size);
};

export { TextureOptimizationTool }; 