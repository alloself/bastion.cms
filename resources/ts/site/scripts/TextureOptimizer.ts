// @ts-ignore
import * as THREE from 'three';

interface TextureConfig {
    path: string;
    format?: 'jpg' | 'webp' | 'ktx2' | 'basis';
    quality?: 'low' | 'medium' | 'high';
    generateMipmaps?: boolean;
    wrapS?: any; // THREE.Wrapping
    wrapT?: any; // THREE.Wrapping
    repeat?: [number, number];
    flipY?: boolean;
}

interface CompressedTextureSupport {
    s3tc: boolean;
    etc1: boolean;
    etc2: boolean;
    pvrtc: boolean;
    astc: boolean;
    basisUniversal: boolean;
}

export class TextureOptimizer {
    private static instance: TextureOptimizer;
    private textureCache = new Map<string, any>(); // THREE.Texture
    private cubeTextureCache = new Map<string, any>(); // THREE.CubeTexture
    private loader: any; // THREE.TextureLoader
    private cubeLoader: any; // THREE.CubeTextureLoader
    private ktx2Loader: any = null;
    private basisLoader: any = null;
    private compressionSupport: CompressedTextureSupport;
    private memoryBudget: number = 256 * 1024 * 1024; // 256MB по умолчанию
    private currentMemoryUsage: number = 0;
    private textureRegistry = new Map<any, number>(); // Размер текстуры в байтах

    private constructor() {
        // @ts-ignore
        this.loader = new THREE.TextureLoader();
        // @ts-ignore
        this.cubeLoader = new THREE.CubeTextureLoader();
        this.compressionSupport = this.detectCompressionSupport();
        this.setupAdvancedLoaders();
        this.calculateMemoryBudget();
    }

    public static getInstance(): TextureOptimizer {
        if (!TextureOptimizer.instance) {
            TextureOptimizer.instance = new TextureOptimizer();
        }
        return TextureOptimizer.instance;
    }

    /**
     * Определяет поддержку сжатых форматов текстур
     */
    private detectCompressionSupport(): CompressedTextureSupport {
        const canvas = document.createElement('canvas');
        const gl = canvas.getContext('webgl') as WebGLRenderingContext || canvas.getContext('experimental-webgl') as WebGLRenderingContext;
        
        if (!gl) {
            return {
                s3tc: false,
                etc1: false,
                etc2: false,
                pvrtc: false,
                astc: false,
                basisUniversal: false
            };
        }

        return {
            s3tc: !!gl.getExtension('WEBGL_compressed_texture_s3tc'),
            etc1: !!gl.getExtension('WEBGL_compressed_texture_etc1'),
            etc2: !!gl.getExtension('WEBGL_compressed_texture_etc'),
            pvrtc: !!gl.getExtension('WEBGL_compressed_texture_pvrtc'),
            astc: !!gl.getExtension('WEBGL_compressed_texture_astc'),
            basisUniversal: !!gl.getExtension('KHR_texture_basisu')
        };
    }

    /**
     * Настраивает продвинутые загрузчики для сжатых текстур
     */
    private async setupAdvancedLoaders() {
        try {
            // Загружаем KTX2Loader для Basis Universal текстур
            if (this.compressionSupport.basisUniversal) {
                try {
                    // @ts-ignore
                    const module = await import('three/addons/loaders/KTX2Loader.js');
                    const KTX2Loader = (module as any).KTX2Loader;
                    this.ktx2Loader = new KTX2Loader();
                    this.ktx2Loader.setTranscoderPath('/public/libs/basis/');
                    // @ts-ignore
                    this.ktx2Loader.detectSupport(new THREE.WebGLRenderer());
                } catch (importError) {
                    console.warn('KTX2Loader недоступен:', importError);
                }
            }
        } catch (error) {
            console.warn('Не удалось загрузить продвинутые загрузчики текстур:', error);
        }
    }

    /**
     * Вычисляет бюджет памяти на основе размера экрана
     */
    private calculateMemoryBudget() {
        const pixelCount = window.innerWidth * window.innerHeight * Math.min(window.devicePixelRatio, 2);
        // Увеличиваем бюджет памяти для текстур
        const baseBudget = pixelCount * 4 * 12; // Увеличили коэффициент с 8 до 12
        const minBudget = 256 * 1024 * 1024; // Увеличили минимум до 256MB
        const maxBudget = 512 * 1024 * 1024; // Максимум 512MB
        
        this.memoryBudget = Math.min(Math.max(baseBudget, minBudget), maxBudget);
        console.log(`Бюджет памяти для текстур: ${(this.memoryBudget / 1024 / 1024).toFixed(1)}MB`);
    }

    /**
     * Оценивает размер текстуры в байтах
     */
    private estimateTextureSize(texture: any): number {
        const image = texture.image;
        if (!image) return 0;

        // Используем реальные размеры изображения
        const width = image.naturalWidth || image.width || 512;
        const height = image.naturalHeight || image.height || 512;
        
        // Базовый размер для RGBA
        let bytesPerPixel = 4;
        
        // Корректируем для разных форматов
        // @ts-ignore
        switch (texture.format) {
            // @ts-ignore
            case THREE.RGBFormat:
                bytesPerPixel = 3;
                break;
            // @ts-ignore
            case THREE.LuminanceFormat:
                bytesPerPixel = 1;
                break;
            // @ts-ignore
            case THREE.LuminanceAlphaFormat:
                bytesPerPixel = 2;
                break;
        }

        let size = width * height * bytesPerPixel;
        
        // Добавляем размер мипмапов (примерно +33%)
        if (texture.generateMipmaps) {
            size *= 1.33;
        }

        // Ограничиваем максимальный размер для предотвращения переполнения
        const maxSize = 16 * 1024 * 1024; // 16MB максимум для одной текстуры
        size = Math.min(size, maxSize);

        return Math.round(size);
    }

    /**
     * Выбирает оптимальный формат текстуры
     */
    private selectOptimalFormat(config: TextureConfig): string {
        // Временно отключаем автоматический выбор сжатых форматов
        // и используем оригинальные файлы для стабильности
        
        // В будущем можно включить после подготовки сжатых версий текстур
        /*
        const basePath = config.path.replace(/\.[^/.]+$/, "");
        
        // Приоритет: Basis Universal > Platform-specific compressed > WebP > JPG
        if (this.compressionSupport.basisUniversal && config.format !== 'jpg') {
            return `${basePath}.ktx2`;
        }
        
        // Для desktop - S3TC (DXT)
        if (this.compressionSupport.s3tc && config.quality === 'high') {
            return `${basePath}.dds`;
        }
        
        // Для mobile - ETC2 или PVRTC
        if (this.compressionSupport.etc2) {
            return `${basePath}.ktx`;
        }
        
        if (this.compressionSupport.pvrtc) {
            return `${basePath}.pvr`;
        }

        // Fallback к WebP или JPG
        if (config.format === 'webp' || (!config.format && this.supportsWebP())) {
            return `${basePath}.webp`;
        }
        */
        
        // Используем оригинальный путь
        return config.path;
    }

    /**
     * Проверяет поддержку WebP
     */
    private supportsWebP(): boolean {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    }

    /**
     * Освобождает память от неиспользуемых текстур
     */
    private freeUnusedTextures() {
        const texturesToRemove: string[] = [];
        const cubeTexturesToRemove: string[] = [];
        
        // Освобождаем обычные текстуры
        this.textureCache.forEach((texture, key) => {
            // Проверяем, используется ли текстура (упрощенная проверка)
            if (texture.userData.lastUsed && Date.now() - texture.userData.lastUsed > 60000) {
                const size = this.textureRegistry.get(texture) || 0;
                this.currentMemoryUsage -= size;
                texture.dispose();
                texturesToRemove.push(key);
                this.textureRegistry.delete(texture);
            }
        });

        // Освобождаем cube текстуры
        this.cubeTextureCache.forEach((texture, key) => {
            if (texture.userData.lastUsed && Date.now() - texture.userData.lastUsed > 60000) {
                const size = this.textureRegistry.get(texture) || 0;
                this.currentMemoryUsage -= size;
                texture.dispose();
                cubeTexturesToRemove.push(key);
                this.textureRegistry.delete(texture);
            }
        });

        texturesToRemove.forEach(key => this.textureCache.delete(key));
        cubeTexturesToRemove.forEach(key => this.cubeTextureCache.delete(key));
        
        const totalRemoved = texturesToRemove.length + cubeTexturesToRemove.length;
        if (totalRemoved > 0) {
            console.log(`Освобождено ${totalRemoved} текстур, память: ${(this.currentMemoryUsage / 1024 / 1024).toFixed(1)}MB`);
        }
    }

    /**
     * Загружает и оптимизирует текстуру
     */
    public async loadTexture(config: TextureConfig): Promise<any> {
        const cacheKey = JSON.stringify(config);
        
        // Проверяем кэш
        if (this.textureCache.has(cacheKey)) {
            const texture = this.textureCache.get(cacheKey)!;
            texture.userData.lastUsed = Date.now();
            return texture;
        }

        // Проверяем бюджет памяти и освобождаем ресурсы при необходимости
        if (this.currentMemoryUsage > this.memoryBudget * 0.8) {
            console.log(`Превышен лимит памяти (${(this.currentMemoryUsage / this.memoryBudget * 100).toFixed(1)}%), освобождаем ресурсы...`);
            this.freeUnusedTextures();
            
            // Если все еще превышен лимит, принудительно очищаем старые текстуры
            if (this.currentMemoryUsage > this.memoryBudget * 0.9) {
                this.forceCleanupOldTextures();
            }
        }

        const optimizedPath = this.selectOptimalFormat(config);
        
        try {
            let texture: any;

            // Загружаем сжатую текстуру если возможно
            if (optimizedPath.endsWith('.ktx2') && this.ktx2Loader) {
                texture = await new Promise<any>((resolve, reject) => {
                    this.ktx2Loader.load(optimizedPath, resolve, undefined, reject);
                });
            } else {
                // Обычная загрузка
                texture = await new Promise<any>((resolve, reject) => {
                    this.loader.load(optimizedPath, resolve, undefined, (error: any) => {
                        // Fallback к оригинальному пути
                        this.loader.load(config.path, resolve, undefined, reject);
                    });
                });
            }

            // Применяем оптимизации
            this.applyTextureOptimizations(texture, config);
            
            // Регистрируем в кэше и памяти
            const size = this.estimateTextureSize(texture);
            this.textureCache.set(cacheKey, texture);
            this.textureRegistry.set(texture, size);
            this.currentMemoryUsage += size;
            texture.userData.lastUsed = Date.now();

            console.log(`Загружена текстура: ${optimizedPath}, размер: ${(size / 1024).toFixed(1)}KB`);
            return texture;

        } catch (error) {
            console.warn(`Ошибка загрузки текстуры ${optimizedPath}:`, error);
            throw error;
        }
    }

    /**
     * Применяет оптимизации к текстуре
     */
    private applyTextureOptimizations(texture: any, config: TextureConfig) {
        // Настройки качества
        switch (config.quality) {
            case 'low':
                // @ts-ignore
                texture.minFilter = THREE.LinearFilter;
                // @ts-ignore
                texture.magFilter = THREE.LinearFilter;
                texture.generateMipmaps = false;
                break;
            case 'medium':
                // @ts-ignore
                texture.minFilter = THREE.LinearMipmapLinearFilter;
                // @ts-ignore
                texture.magFilter = THREE.LinearFilter;
                texture.generateMipmaps = config.generateMipmaps !== false;
                break;
            case 'high':
            default:
                // @ts-ignore
                texture.minFilter = THREE.LinearMipmapLinearFilter;
                // @ts-ignore
                texture.magFilter = THREE.LinearFilter;
                texture.generateMipmaps = config.generateMipmaps !== false;
                // @ts-ignore
                texture.anisotropy = Math.min(4, THREE.WebGLRenderer.prototype.capabilities?.getMaxAnisotropy?.() || 1);
                break;
        }

        // Настройки wrapping
        // @ts-ignore
        texture.wrapS = config.wrapS || THREE.ClampToEdgeWrapping;
        // @ts-ignore
        texture.wrapT = config.wrapT || THREE.ClampToEdgeWrapping;

        // Повтор текстуры
        if (config.repeat) {
            texture.repeat.set(config.repeat[0], config.repeat[1]);
        }

        // Переворот по Y
        if (config.flipY !== undefined) {
            texture.flipY = config.flipY;
        }

        // Оптимизация для мобильных устройств
        if (this.isMobileDevice()) {
            texture.anisotropy = Math.min(2, texture.anisotropy);
        }
    }

    /**
     * Загружает cube texture с оптимизацией
     */
    public async loadCubeTexture(paths: string[], quality: 'low' | 'medium' | 'high' = 'medium'): Promise<any> {
        const cacheKey = JSON.stringify({ paths, quality });
        
        if (this.cubeTextureCache.has(cacheKey)) {
            const texture = this.cubeTextureCache.get(cacheKey)!;
            texture.userData.lastUsed = Date.now();
            return texture;
        }

        // Оптимизируем пути для каждой грани
        const optimizedPaths = paths.map(path => 
            this.selectOptimalFormat({ path, quality })
        );

        try {
            const cubeTexture = await new Promise<any>((resolve, reject) => {
                this.cubeLoader.load(optimizedPaths, resolve, undefined, (error: any) => {
                    // Fallback к оригинальным путям
                    this.cubeLoader.load(paths, resolve, undefined, reject);
                });
            });

            // Применяем оптимизации
            switch (quality) {
                case 'low':
                    // @ts-ignore
                    cubeTexture.minFilter = THREE.LinearFilter;
                    // @ts-ignore
                    cubeTexture.magFilter = THREE.LinearFilter;
                    cubeTexture.generateMipmaps = false;
                    break;
                case 'medium':
                    // @ts-ignore
                    cubeTexture.minFilter = THREE.LinearMipmapLinearFilter;
                    // @ts-ignore
                    cubeTexture.magFilter = THREE.LinearFilter;
                    break;
                case 'high':
                    // @ts-ignore
                    cubeTexture.minFilter = THREE.LinearMipmapLinearFilter;
                    // @ts-ignore
                    cubeTexture.magFilter = THREE.LinearFilter;
                    // @ts-ignore
                    cubeTexture.anisotropy = Math.min(4, THREE.WebGLRenderer.prototype.capabilities?.getMaxAnisotropy?.() || 1);
                    break;
            }

            const size = this.estimateTextureSize(cubeTexture) * 6; // 6 граней
            this.cubeTextureCache.set(cacheKey, cubeTexture);
            this.textureRegistry.set(cubeTexture, size);
            this.currentMemoryUsage += size;
            cubeTexture.userData.lastUsed = Date.now();

            return cubeTexture;

        } catch (error) {
            console.warn('Ошибка загрузки cube texture:', error);
            throw error;
        }
    }

    /**
     * Создает оптимизированную текстуру атласа
     */
    public async createTextureAtlas(texturePaths: string[], atlasSize: number = 1024): Promise<any> {
        const canvas = document.createElement('canvas');
        canvas.width = atlasSize;
        canvas.height = atlasSize;
        const ctx = canvas.getContext('2d')!;

        const texturesPerRow = Math.ceil(Math.sqrt(texturePaths.length));
        const textureSize = atlasSize / texturesPerRow;

        // Загружаем все изображения
        const images = await Promise.all(
            texturePaths.map(path => new Promise<HTMLImageElement>((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = path;
            }))
        );

        // Рисуем атлас
        images.forEach((img, index) => {
            const x = (index % texturesPerRow) * textureSize;
            const y = Math.floor(index / texturesPerRow) * textureSize;
            ctx.drawImage(img, x, y, textureSize, textureSize);
        });

        // Создаем текстуру из canvas
        // @ts-ignore
        const texture = new THREE.CanvasTexture(canvas);
        // @ts-ignore
        texture.minFilter = THREE.LinearMipmapLinearFilter;
        // @ts-ignore
        texture.magFilter = THREE.LinearFilter;
        texture.generateMipmaps = true;

        return texture;
    }

    /**
     * Проверяет, является ли устройство мобильным
     */
    private isMobileDevice(): boolean {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    /**
     * Получает статистику использования памяти
     */
    public getMemoryStats() {
        return {
            currentUsage: this.currentMemoryUsage,
            budget: this.memoryBudget,
            usagePercent: (this.currentMemoryUsage / this.memoryBudget) * 100,
            textureCount: this.textureCache.size,
            cubeTextureCount: this.cubeTextureCache.size,
            compressionSupport: this.compressionSupport
        };
    }

    /**
     * Принудительно очищает старые текстуры
     */
    private forceCleanupOldTextures() {
        const texturesToRemove: string[] = [];
        const cubeTexturesToRemove: string[] = [];
        
        // Сортируем текстуры по времени последнего использования
        const sortedTextures = Array.from(this.textureCache.entries())
            .sort((a, b) => (a[1].userData.lastUsed || 0) - (b[1].userData.lastUsed || 0));
        
        const sortedCubeTextures = Array.from(this.cubeTextureCache.entries())
            .sort((a, b) => (a[1].userData.lastUsed || 0) - (b[1].userData.lastUsed || 0));
        
        // Удаляем половину самых старых текстур
        const texturesToDelete = Math.ceil(sortedTextures.length / 2);
        const cubeTexturesToDelete = Math.ceil(sortedCubeTextures.length / 2);
        
        for (let i = 0; i < texturesToDelete; i++) {
            const [key, texture] = sortedTextures[i];
            const size = this.textureRegistry.get(texture) || 0;
            this.currentMemoryUsage -= size;
            texture.dispose();
            texturesToRemove.push(key);
            this.textureRegistry.delete(texture);
        }
        
        for (let i = 0; i < cubeTexturesToDelete; i++) {
            const [key, texture] = sortedCubeTextures[i];
            const size = this.textureRegistry.get(texture) || 0;
            this.currentMemoryUsage -= size;
            texture.dispose();
            cubeTexturesToRemove.push(key);
            this.textureRegistry.delete(texture);
        }
        
        texturesToRemove.forEach(key => this.textureCache.delete(key));
        cubeTexturesToRemove.forEach(key => this.cubeTextureCache.delete(key));
        
        const totalRemoved = texturesToRemove.length + cubeTexturesToRemove.length;
        console.log(`Принудительно освобождено ${totalRemoved} текстур, память: ${(this.currentMemoryUsage / 1024 / 1024).toFixed(1)}MB`);
    }

    /**
     * Очищает весь кэш текстур
     */
    public clearCache() {
        this.textureCache.forEach(texture => texture.dispose());
        this.cubeTextureCache.forEach(texture => texture.dispose());
        this.textureCache.clear();
        this.cubeTextureCache.clear();
        this.textureRegistry.clear();
        this.currentMemoryUsage = 0;
        console.log('Кэш текстур полностью очищен');
    }

    /**
     * Предзагружает текстуры
     */
    public async preloadTextures(configs: TextureConfig[]): Promise<any[]> {
        console.log(`Предзагрузка ${configs.length} текстур...`);
        
        // Загружаем по частям для избежания блокировки
        const batchSize = 3;
        const results: any[] = [];
        
        for (let i = 0; i < configs.length; i += batchSize) {
            const batch = configs.slice(i, i + batchSize);
            const batchResults = await Promise.allSettled(
                batch.map(config => this.loadTexture(config))
            );
            
            batchResults.forEach(result => {
                if (result.status === 'fulfilled') {
                    results.push(result.value);
                }
            });
            
            // Небольшая пауза между батчами
            if (i + batchSize < configs.length) {
                await new Promise(resolve => setTimeout(resolve, 16));
            }
        }
        
        console.log(`Предзагружено ${results.length} из ${configs.length} текстур`);
        return results;
    }
} 