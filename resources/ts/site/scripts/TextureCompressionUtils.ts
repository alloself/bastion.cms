/**
 * Утилиты для работы со сжатыми текстурами
 * Поддерживает конвертацию в различные форматы для оптимизации производительности
 */

interface CompressionOptions {
    quality?: number; // 0-100
    format?: 'webp' | 'avif' | 'jpg' | 'png';
    maxWidth?: number;
    maxHeight?: number;
    enableProgressive?: boolean;
}

interface TextureInfo {
    width: number;
    height: number;
    format: string;
    size: number;
    compressionRatio?: number;
}

export class TextureCompressionUtils {
    private static canvas: HTMLCanvasElement | null = null;
    private static ctx: CanvasRenderingContext2D | null = null;

    /**
     * Получает canvas для работы с изображениями
     */
    private static getCanvas(): { canvas: HTMLCanvasElement; ctx: CanvasRenderingContext2D } {
        if (!this.canvas) {
            this.canvas = document.createElement('canvas');
            this.ctx = this.canvas.getContext('2d')!;
        }
        return { canvas: this.canvas, ctx: this.ctx! };
    }

    /**
     * Загружает изображение из URL
     */
    static async loadImage(url: string): Promise<HTMLImageElement> {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = url;
        });
    }

    /**
     * Получает информацию о текстуре
     */
    static async getTextureInfo(url: string): Promise<TextureInfo> {
        try {
            const img = await this.loadImage(url);
            
            // Примерная оценка размера файла
            const response = await fetch(url, { method: 'HEAD' });
            const size = parseInt(response.headers.get('content-length') || '0');
            
            return {
                width: img.naturalWidth,
                height: img.naturalHeight,
                format: url.split('.').pop()?.toLowerCase() || 'unknown',
                size: size
            };
        } catch (error) {
            console.warn('Ошибка получения информации о текстуре:', error);
            throw error;
        }
    }

    /**
     * Изменяет размер изображения с сохранением пропорций
     */
    static resizeImage(
        img: HTMLImageElement, 
        maxWidth: number, 
        maxHeight: number
    ): { width: number; height: number } {
        let { width, height } = img;
        
        // Вычисляем новые размеры с сохранением пропорций
        if (width > maxWidth) {
            height = (height * maxWidth) / width;
            width = maxWidth;
        }
        
        if (height > maxHeight) {
            width = (width * maxHeight) / height;
            height = maxHeight;
        }
        
        // Округляем до четных чисел для лучшей совместимости
        width = Math.floor(width / 2) * 2;
        height = Math.floor(height / 2) * 2;
        
        return { width, height };
    }

    /**
     * Конвертирует изображение в указанный формат
     */
    static async convertImage(
        img: HTMLImageElement,
        options: CompressionOptions = {}
    ): Promise<{ blob: Blob; dataUrl: string; info: TextureInfo }> {
        const {
            quality = 85,
            format = 'webp',
            maxWidth = 2048,
            maxHeight = 2048
        } = options;

        const { canvas, ctx } = this.getCanvas();
        const { width, height } = this.resizeImage(img, maxWidth, maxHeight);
        
        canvas.width = width;
        canvas.height = height;
        
        // Очищаем canvas
        ctx.clearRect(0, 0, width, height);
        
        // Рисуем изображение с оптимизацией качества
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, 0, 0, width, height);
        
        // Определяем MIME тип
        const mimeType = this.getMimeType(format);
        const qualityValue = quality / 100;
        
        // Конвертируем в blob
        const blob = await new Promise<Blob>((resolve, reject) => {
            canvas.toBlob(
                (blob) => blob ? resolve(blob) : reject(new Error('Ошибка конвертации')),
                mimeType,
                qualityValue
            );
        });
        
        // Создаем data URL
        const dataUrl = canvas.toDataURL(mimeType, qualityValue);
        
        const info: TextureInfo = {
            width,
            height,
            format,
            size: blob.size,
            compressionRatio: img.src.length > 0 ? blob.size / img.src.length : undefined
        };
        
        return { blob, dataUrl, info };
    }

    /**
     * Получает MIME тип для формата
     */
    private static getMimeType(format: string): string {
        const mimeTypes: Record<string, string> = {
            'webp': 'image/webp',
            'avif': 'image/avif',
            'jpg': 'image/jpeg',
            'jpeg': 'image/jpeg',
            'png': 'image/png'
        };
        
        return mimeTypes[format.toLowerCase()] || 'image/jpeg';
    }

    /**
     * Проверяет поддержку формата браузером
     */
    static async checkFormatSupport(format: string): Promise<boolean> {
        const { canvas } = this.getCanvas();
        const mimeType = this.getMimeType(format);
        
        // Создаем тестовое изображение 1x1
        canvas.width = 1;
        canvas.height = 1;
        
        try {
            const dataUrl = canvas.toDataURL(mimeType, 0.5);
            return dataUrl.startsWith(`data:${mimeType}`);
        } catch {
            return false;
        }
    }

    /**
     * Создает оптимизированную версию текстуры для разных устройств
     */
    static async createResponsiveTextures(
        img: HTMLImageElement,
        baseName: string
    ): Promise<{
        high: { blob: Blob; info: TextureInfo };
        medium: { blob: Blob; info: TextureInfo };
        low: { blob: Blob; info: TextureInfo };
    }> {
        const results = await Promise.all([
            // Высокое качество - для desktop
            this.convertImage(img, {
                quality: 90,
                format: 'webp',
                maxWidth: 2048,
                maxHeight: 2048
            }),
            // Среднее качество - для планшетов
            this.convertImage(img, {
                quality: 75,
                format: 'webp',
                maxWidth: 1024,
                maxHeight: 1024
            }),
            // Низкое качество - для мобильных
            this.convertImage(img, {
                quality: 60,
                format: 'webp',
                maxWidth: 512,
                maxHeight: 512
            })
        ]);

        return {
            high: { blob: results[0].blob, info: results[0].info },
            medium: { blob: results[1].blob, info: results[1].info },
            low: { blob: results[2].blob, info: results[2].info }
        };
    }

    /**
     * Создает мипмапы для текстуры
     */
    static async createMipmaps(
        img: HTMLImageElement,
        levels: number = 4
    ): Promise<Array<{ blob: Blob; info: TextureInfo; level: number }>> {
        const mipmaps: Array<{ blob: Blob; info: TextureInfo; level: number }> = [];
        
        let currentWidth = img.naturalWidth;
        let currentHeight = img.naturalHeight;
        
        for (let level = 0; level < levels; level++) {
            const result = await this.convertImage(img, {
                quality: 85,
                format: 'webp',
                maxWidth: currentWidth,
                maxHeight: currentHeight
            });
            
            mipmaps.push({
                blob: result.blob,
                info: result.info,
                level
            });
            
            // Уменьшаем размер в 2 раза для следующего уровня
            currentWidth = Math.max(1, Math.floor(currentWidth / 2));
            currentHeight = Math.max(1, Math.floor(currentHeight / 2));
            
            if (currentWidth === 1 && currentHeight === 1) break;
        }
        
        return mipmaps;
    }

    /**
     * Оптимизирует текстуру для WebGL
     */
    static async optimizeForWebGL(
        img: HTMLImageElement,
        options: CompressionOptions = {}
    ): Promise<{ blob: Blob; dataUrl: string; info: TextureInfo }> {
        const { canvas, ctx } = this.getCanvas();
        
        // Приводим к степени двойки для лучшей совместимости с WebGL
        const powerOfTwoWidth = this.nearestPowerOfTwo(img.naturalWidth);
        const powerOfTwoHeight = this.nearestPowerOfTwo(img.naturalHeight);
        
        canvas.width = powerOfTwoWidth;
        canvas.height = powerOfTwoHeight;
        
        // Очищаем canvas
        ctx.clearRect(0, 0, powerOfTwoWidth, powerOfTwoHeight);
        
        // Рисуем изображение с масштабированием
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, 0, 0, powerOfTwoWidth, powerOfTwoHeight);
        
        // Применяем дополнительные оптимизации
        const imageData = ctx.getImageData(0, 0, powerOfTwoWidth, powerOfTwoHeight);
        this.applyWebGLOptimizations(imageData);
        ctx.putImageData(imageData, 0, 0);
        
        // Конвертируем в оптимальный формат
        const format = await this.checkFormatSupport('webp') ? 'webp' : 'jpg';
        const quality = options.quality || 85;
        
        const mimeType = this.getMimeType(format);
        const blob = await new Promise<Blob>((resolve, reject) => {
            canvas.toBlob(
                (blob) => blob ? resolve(blob) : reject(new Error('Ошибка конвертации')),
                mimeType,
                quality / 100
            );
        });
        
        const dataUrl = canvas.toDataURL(mimeType, quality / 100);
        
        const info: TextureInfo = {
            width: powerOfTwoWidth,
            height: powerOfTwoHeight,
            format,
            size: blob.size
        };
        
        return { blob, dataUrl, info };
    }

    /**
     * Находит ближайшую степень двойки
     */
    private static nearestPowerOfTwo(value: number): number {
        return Math.pow(2, Math.round(Math.log2(value)));
    }

    /**
     * Применяет оптимизации для WebGL
     */
    private static applyWebGLOptimizations(imageData: ImageData) {
        const data = imageData.data;
        
        // Предварительное умножение альфа-канала для лучшей производительности
        for (let i = 0; i < data.length; i += 4) {
            const alpha = data[i + 3] / 255;
            data[i] = Math.round(data[i] * alpha);     // R
            data[i + 1] = Math.round(data[i + 1] * alpha); // G
            data[i + 2] = Math.round(data[i + 2] * alpha); // B
        }
    }

    /**
     * Создает текстурный атлас из массива изображений
     */
    static async createTextureAtlas(
        images: HTMLImageElement[],
        atlasSize: number = 2048,
        padding: number = 2
    ): Promise<{ blob: Blob; dataUrl: string; mapping: Array<{ x: number; y: number; width: number; height: number }> }> {
        const { canvas, ctx } = this.getCanvas();
        canvas.width = atlasSize;
        canvas.height = atlasSize;
        
        ctx.clearRect(0, 0, atlasSize, atlasSize);
        
        // Простой алгоритм упаковки (можно улучшить)
        const mapping: Array<{ x: number; y: number; width: number; height: number }> = [];
        const itemsPerRow = Math.ceil(Math.sqrt(images.length));
        const itemSize = Math.floor((atlasSize - padding * (itemsPerRow + 1)) / itemsPerRow);
        
        images.forEach((img, index) => {
            const row = Math.floor(index / itemsPerRow);
            const col = index % itemsPerRow;
            
            const x = padding + col * (itemSize + padding);
            const y = padding + row * (itemSize + padding);
            
            ctx.drawImage(img, x, y, itemSize, itemSize);
            
            mapping.push({
                x: x / atlasSize,
                y: y / atlasSize,
                width: itemSize / atlasSize,
                height: itemSize / atlasSize
            });
        });
        
        const blob = await new Promise<Blob>((resolve, reject) => {
            canvas.toBlob(
                (blob) => blob ? resolve(blob) : reject(new Error('Ошибка создания атласа')),
                'image/webp',
                0.85
            );
        });
        
        const dataUrl = canvas.toDataURL('image/webp', 0.85);
        
        return { blob, dataUrl, mapping };
    }

    /**
     * Анализирует текстуру и предлагает оптимизации
     */
    static async analyzeTexture(url: string): Promise<{
        info: TextureInfo;
        recommendations: string[];
        estimatedSavings: number;
    }> {
        const info = await this.getTextureInfo(url);
        const recommendations: string[] = [];
        let estimatedSavings = 0;
        
        // Анализ размера
        if (info.width > 2048 || info.height > 2048) {
            recommendations.push('Рассмотрите уменьшение размера до 2048x2048 или меньше');
            estimatedSavings += 0.5; // 50% экономии
        }
        
        // Анализ формата
        if (info.format === 'png' && await this.checkFormatSupport('webp')) {
            recommendations.push('Конвертируйте PNG в WebP для лучшего сжатия');
            estimatedSavings += 0.3; // 30% экономии
        }
        
        // Анализ степени двойки
        if (!this.isPowerOfTwo(info.width) || !this.isPowerOfTwo(info.height)) {
            recommendations.push('Используйте размеры, кратные степени двойки, для лучшей производительности WebGL');
        }
        
        // Анализ размера файла
        if (info.size > 1024 * 1024) { // > 1MB
            recommendations.push('Файл слишком большой, рассмотрите дополнительное сжатие');
            estimatedSavings += 0.2; // 20% экономии
        }
        
        return {
            info,
            recommendations,
            estimatedSavings: Math.min(estimatedSavings, 0.8) // Максимум 80% экономии
        };
    }

    /**
     * Проверяет, является ли число степенью двойки
     */
    private static isPowerOfTwo(value: number): boolean {
        return (value & (value - 1)) === 0 && value !== 0;
    }

    /**
     * Создает превью текстуры для отладки
     */
    static createPreview(
        img: HTMLImageElement,
        size: number = 256
    ): string {
        const { canvas, ctx } = this.getCanvas();
        canvas.width = size;
        canvas.height = size;
        
        ctx.clearRect(0, 0, size, size);
        ctx.drawImage(img, 0, 0, size, size);
        
        return canvas.toDataURL('image/jpeg', 0.8);
    }

    /**
     * Очищает ресурсы
     */
    static cleanup() {
        if (this.canvas) {
            this.canvas.width = 1;
            this.canvas.height = 1;
            this.ctx?.clearRect(0, 0, 1, 1);
        }
    }
} 