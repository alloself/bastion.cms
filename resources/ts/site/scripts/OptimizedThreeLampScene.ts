// @ts-ignore
import * as THREE from 'three';
// @ts-ignore
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
// @ts-ignore
import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';
import { TextureOptimizer } from './TextureOptimizer';

interface LampSceneConfig {
    texturePath: string;
    envImagePaths: string[];
    filePath: string;
    renderElem: HTMLElement;
    orbitControlEnabled?: boolean;
    modelInitialRotation?: {
        x: number;
        y: number;
        z: number;
    };
    modelMoveAnimationSettings?: {
        direction: string;
        axis: string;
        value: number;
        moreValue: number[];
    };
    quality?: 'low' | 'medium' | 'high';
}

// Глобальные загрузчики для переиспользования
let glbLoader: GLTFLoader | null = null;
let dracoLoader: DRACOLoader | null = null;

function initLoaders() {
    if (!glbLoader) {
        glbLoader = new GLTFLoader();
        dracoLoader = new DRACOLoader();
        
        dracoLoader.setDecoderPath('/public/models/lamp/draco/');
        glbLoader.setDRACOLoader(dracoLoader);
        dracoLoader.preload();
    }
}

export class OptimizedThreeLampScene {
    private scene: THREE.Scene | null = null;
    private camera: THREE.PerspectiveCamera | null = null;
    private renderer: THREE.WebGLRenderer | null = null;
    private model: THREE.Group | null = null;
    private renderElem: HTMLElement;
    private textureOptimizer: TextureOptimizer;
    private config: LampSceneConfig;
    private isInitialized = false;
    private isVisible = false;
    private animationId: number | null = null;
    private intersectionObserver: IntersectionObserver | null = null;
    private resizeObserver: ResizeObserver | null = null;
    private lastFrameTime = 0;
    private targetFPS = 60;
    private frameInterval = 1000 / this.targetFPS;

    constructor(config: LampSceneConfig) {
        this.config = {
            quality: 'medium',
            modelInitialRotation: { x: 0, y: 0, z: 0 },
            modelMoveAnimationSettings: {
                direction: "left",
                axis: "y",
                value: 0.0003,
                moreValue: [0.5, -0.5],
            },
            ...config
        };
        
        this.renderElem = config.renderElem;
        this.textureOptimizer = TextureOptimizer.getInstance();
        
        // Адаптивное качество на основе устройства
        this.adaptQualityToDevice();
        
        this.setupIntersectionObserver();
        this.setupResizeObserver();
    }

    /**
     * Адаптирует качество на основе характеристик устройства
     */
    private adaptQualityToDevice() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const pixelRatio = window.devicePixelRatio || 1;
        const screenSize = window.innerWidth * window.innerHeight;
        
        // Автоматическое определение качества
        if (!this.config.quality || this.config.quality === 'medium') {
            if (isMobile || pixelRatio > 2 || screenSize < 1000000) {
                this.config.quality = 'low';
                this.targetFPS = 30;
            } else if (screenSize > 2000000 && pixelRatio <= 1.5) {
                this.config.quality = 'high';
                this.targetFPS = 60;
            } else {
                this.config.quality = 'medium';
                this.targetFPS = 45;
            }
        }
        
        this.frameInterval = 1000 / this.targetFPS;
        console.log(`Качество 3D сцены: ${this.config.quality}, целевой FPS: ${this.targetFPS}`);
    }

    /**
     * Настраивает Intersection Observer для ленивой инициализации
     */
    private setupIntersectionObserver() {
        this.intersectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.isInitialized) {
                    this.isVisible = true;
                    // Используем requestIdleCallback для неблокирующей инициализации
                    if ('requestIdleCallback' in window) {
                        requestIdleCallback(() => this.init(), { timeout: 3000 });
                    } else {
                        setTimeout(() => this.init(), 100);
                    }
                    this.intersectionObserver?.unobserve(this.renderElem);
                } else if (!entry.isIntersecting && this.isInitialized) {
                    this.isVisible = false;
                    this.pauseAnimation();
                } else if (entry.isIntersecting && this.isInitialized) {
                    this.isVisible = true;
                    this.resumeAnimation();
                }
            });
        }, {
            rootMargin: '100px', // Увеличиваем область для предзагрузки
            threshold: 0.1
        });

        this.intersectionObserver.observe(this.renderElem);
    }

    /**
     * Настраивает ResizeObserver для адаптивного изменения размера
     */
    private setupResizeObserver() {
        if ('ResizeObserver' in window) {
            this.resizeObserver = new ResizeObserver((entries) => {
                if (this.renderer && this.camera && this.isInitialized) {
                    const entry = entries[0];
                    const { width, height } = entry.contentRect;
                    
                    this.camera.aspect = width / height;
                    this.camera.updateProjectionMatrix();
                    this.renderer.setSize(width, height);
                }
            });
            
            this.resizeObserver.observe(this.renderElem);
        }
    }

    /**
     * Приостанавливает анимацию для экономии ресурсов
     */
    private pauseAnimation() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
    }

    /**
     * Возобновляет анимацию
     */
    private resumeAnimation() {
        if (!this.animationId && this.model && this.isVisible) {
            this.animateScene();
        }
    }

    /**
     * Анимирует движение модели с оптимизацией FPS
     */
    private animateModelMove() {
        if (!this.model || !this.isVisible || !this.config.modelMoveAnimationSettings) return;

        const settings = this.config.modelMoveAnimationSettings;
        
        if (settings.direction === "right") {
            this.model.rotation.y += settings.value;
            if (this.model.rotation.y > settings.moreValue[0]) {
                settings.direction = "left";
            }
        } else if (settings.direction === "left") {
            this.model.rotation.y -= settings.value;
            if (this.model.rotation.y < settings.moreValue[1]) {
                settings.direction = "right";
            }
        }
    }

    /**
     * Основной цикл анимации с контролем FPS
     */
    private animateScene() {
        if (!this.isVisible || !this.renderer || !this.scene || !this.camera) {
            return;
        }

        const currentTime = performance.now();
        const deltaTime = currentTime - this.lastFrameTime;

        // Ограничиваем FPS для экономии ресурсов
        if (deltaTime >= this.frameInterval) {
            this.renderer.render(this.scene, this.camera);
            this.animateModelMove();
            this.lastFrameTime = currentTime - (deltaTime % this.frameInterval);
        }

        this.animationId = requestAnimationFrame(() => this.animateScene());
    }

    /**
     * Настраивает оптимизированное освещение
     */
    private setupLights() {
        if (!this.scene) return;

        // Упрощенное освещение для лучшей производительности
        const ambientLight = new THREE.AmbientLight('#ffffff', 0.4);
        const directionalLight = new THREE.DirectionalLight('#ffffff', 0.6);
        
        directionalLight.position.set(0, 1, 0);
        
        // Отключаем тени для производительности
        directionalLight.castShadow = false;
        
        this.scene.add(ambientLight);
        this.scene.add(directionalLight);
    }

    /**
     * Применяет оптимизированную текстуру к модели
     */
    private async applyModelTexture() {
        if (!this.model) return;

        try {
            const texture = await this.textureOptimizer.loadTexture({
                path: this.config.texturePath,
                quality: this.config.quality,
                wrapS: THREE.RepeatWrapping,
                wrapT: THREE.RepeatWrapping,
                repeat: [4, 1],
                generateMipmaps: this.config.quality !== 'low'
            });

            this.model.traverse((child: any) => {
                if (child.isMesh) {
                    // Клонируем материал для избежания конфликтов
                    child.material = child.material.clone();
                    child.material.map = texture;
                    child.material.needsUpdate = true;
                    
                    // Оптимизации для производительности
                    child.castShadow = false;
                    child.receiveShadow = false;
                    child.frustumCulled = true;
                    child.matrixAutoUpdate = false;
                    child.updateMatrix();
                }
            });
        } catch (error) {
            console.warn('Не удалось загрузить текстуру модели:', error);
        }
    }

    /**
     * Устанавливает оптимизированную environment текстуру
     */
    private async setupEnvironment() {
        if (!this.scene) return;

        try {
            const cubeTexture = await this.textureOptimizer.loadCubeTexture(
                this.config.envImagePaths,
                this.config.quality
            );
            
            this.scene.environment = cubeTexture;
            
            // Для низкого качества отключаем environment mapping
            if (this.config.quality === 'low') {
                this.scene.environment = null;
            }
        } catch (error) {
            console.warn('Не удалось загрузить environment текстуры:', error);
        }
    }

    /**
     * Настраивает оптимизированный рендерер
     */
    private setupRenderer() {
        const rendererConfig: any = {
            alpha: true,
            powerPreference: "high-performance",
            stencil: false,
            depth: true
        };

        // Адаптивные настройки качества
        switch (this.config.quality) {
            case 'low':
                rendererConfig.antialias = false;
                rendererConfig.logarithmicDepthBuffer = false;
                break;
            case 'medium':
                rendererConfig.antialias = false;
                rendererConfig.logarithmicDepthBuffer = false;
                break;
            case 'high':
                rendererConfig.antialias = true;
                rendererConfig.logarithmicDepthBuffer = false;
                break;
        }

        this.renderer = new THREE.WebGLRenderer(rendererConfig);
        
        // Адаптивное разрешение
        const maxPixelRatio = this.config.quality === 'low' ? 1 : 
                             this.config.quality === 'medium' ? 1.5 : 2;
        const pixelRatio = Math.min(window.devicePixelRatio, maxPixelRatio);
        this.renderer.setPixelRatio(pixelRatio);
        
        this.renderer.setSize(
            this.renderElem.offsetWidth,
            this.renderElem.offsetHeight
        );
        
        // Отключаем тени для производительности
        this.renderer.shadowMap.enabled = false;
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;
        
        // Оптимизации рендерера
        this.renderer.info.autoReset = false;
        
        this.renderElem.appendChild(this.renderer.domElement);
    }

    /**
     * Настраивает камеру с оптимизированными параметрами
     */
    private setupCamera() {
        const aspect = this.renderElem.offsetWidth / this.renderElem.offsetHeight;
        
        // Адаптивные параметры камеры
        const fov = this.config.quality === 'low' ? 35 : 40;
        const near = 1;
        const far = this.config.quality === 'low' ? 50 : 100;
        
        this.camera = new THREE.PerspectiveCamera(fov, aspect, near, far);
        this.camera.position.z = 10;
    }

    /**
     * Загружает и оптимизирует 3D модель
     */
    private async loadModel() {
        if (!this.scene) return;

        try {
            initLoaders();
            
            const gltf = await new Promise<any>((resolve, reject) => {
                glbLoader!.load(this.config.filePath, resolve, undefined, reject);
            });
            
            this.model = gltf.scene;
            
            // Применяем начальную ротацию
            if (this.config.modelInitialRotation) {
                this.model.rotation.x += this.config.modelInitialRotation.x;
                this.model.rotation.y += this.config.modelInitialRotation.y;
                this.model.rotation.z += this.config.modelInitialRotation.z;
            }
            
            // Оптимизируем модель
            this.model.traverse((child: any) => {
                if (child.isMesh) {
                    child.frustumCulled = true;
                    child.matrixAutoUpdate = false;
                    child.updateMatrix();
                    
                    // Упрощаем материалы для низкого качества
                    if (this.config.quality === 'low' && child.material) {
                        child.material.roughness = 0.8;
                        child.material.metalness = 0.2;
                    }
                }
            });
            
            // Применяем текстуру
            await this.applyModelTexture();
            
            this.scene.add(this.model);
            
            // Запускаем анимацию только если элемент видим
            if (this.isVisible) {
                this.resumeAnimation();
            }
            
            this.renderElem.classList.add("is-loaded");
            
        } catch (error) {
            console.warn('Не удалось загрузить 3D модель:', error);
        }
    }

    /**
     * Инициализирует сцену
     */
    private async init() {
        if (this.isInitialized) return;
        
        try {
            // Очищаем контейнер
            this.renderElem.innerHTML = "";
            
            // Создаем сцену
            this.scene = new THREE.Scene();
            
            // Настраиваем компоненты
            this.setupCamera();
            this.setupRenderer();
            this.setupLights();
            
            // Асинхронно загружаем ресурсы
            await Promise.all([
                this.setupEnvironment(),
                this.loadModel()
            ]);
            
            this.isInitialized = true;
            
            // Выводим статистику памяти
            const stats = this.textureOptimizer.getMemoryStats();
            console.log(`Память текстур: ${(stats.currentUsage / 1024 / 1024).toFixed(1)}MB / ${(stats.budget / 1024 / 1024).toFixed(1)}MB (${stats.usagePercent.toFixed(1)}%)`);
            
        } catch (error) {
            console.warn('Ошибка инициализации оптимизированной Three.js сцены:', error);
        }
    }

    /**
     * Освобождает ресурсы
     */
    public dispose() {
        this.pauseAnimation();
        
        if (this.intersectionObserver) {
            this.intersectionObserver.disconnect();
        }
        
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
        }
        
        if (this.renderer) {
            this.renderer.dispose();
            if (this.renderer.domElement.parentNode) {
                this.renderer.domElement.parentNode.removeChild(this.renderer.domElement);
            }
        }
        
        if (this.scene) {
            this.scene.clear();
        }
        
        this.isInitialized = false;
    }

    /**
     * Получает статистику производительности
     */
    public getPerformanceStats() {
        return {
            isInitialized: this.isInitialized,
            isVisible: this.isVisible,
            quality: this.config.quality,
            targetFPS: this.targetFPS,
            rendererInfo: this.renderer?.info,
            memoryStats: this.textureOptimizer.getMemoryStats()
        };
    }

    /**
     * Изменяет качество рендеринга на лету
     */
    public setQuality(quality: 'low' | 'medium' | 'high') {
        if (this.config.quality === quality) return;
        
        this.config.quality = quality;
        this.adaptQualityToDevice();
        
        // Пересоздаем сцену с новым качеством
        if (this.isInitialized) {
            this.dispose();
            setTimeout(() => this.init(), 100);
        }
    }
} 