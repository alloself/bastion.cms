import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';

// Ленивая инициализация загрузчиков
let textureLoader = null;
let cubeTextureLoader = null;
let glbLoader = null;
let dracoLoader = null;

function initLoaders() {
    if (!textureLoader) {
        textureLoader = new THREE.TextureLoader();
        cubeTextureLoader = new THREE.CubeTextureLoader();
        glbLoader = new GLTFLoader();
        dracoLoader = new DRACOLoader();
        
        dracoLoader.setDecoderPath('/public/models/lamp/draco/');
        glbLoader.setDRACOLoader(dracoLoader);
        dracoLoader.preload();
    }
}

export class ThreeLampScene {
    scene = null;
    camera = null;
    renderer = null;
    model = null;
    renderElem = null;
    texturePath = null;
    envImagePaths = null;
    filePath = null;
    controls = null;
    orbitControlEnabled = false;
    isInitialized = false;
    isVisible = false;
    animationId = null;
    modelInitialRotation = {
        y: 0,
        x: 0,
        z: 0,
    };
    modelMoveAnimationSettings = {
        direction: "left",
        axis: "y",
        value: 0.0003,
        moreValue: [0.5, -0.5],
    };

    constructor({
        texturePath,
        envImagePaths,
        filePath,
        renderElem,
        orbitControlEnabled,
        modelInitialRotation,
        modelMoveAnimationSettings,
    }) {
        if (!renderElem) {
            return;
        }

        if (orbitControlEnabled) {
            this.orbitControlEnabled = orbitControlEnabled;
        }
        if (modelInitialRotation) {
            this.modelInitialRotation = modelInitialRotation;
        }
        if (modelMoveAnimationSettings) {
            this.modelMoveAnimationSettings = modelMoveAnimationSettings;
        }
        this.envImagePaths = envImagePaths;
        this.texturePath = texturePath;
        this.filePath = filePath;
        this.renderElem = renderElem;
        
        // Ленивая инициализация при появлении в viewport
        this.setupIntersectionObserver();
    }

    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.isInitialized) {
                    this.isVisible = true;
                    // Задержка для предотвращения блокировки рендеринга
                    requestIdleCallback(() => {
                        this.init();
                    }, { timeout: 2000 });
                    observer.unobserve(this.renderElem);
                } else if (!entry.isIntersecting && this.isInitialized) {
                    this.isVisible = false;
                    this.pauseAnimation();
                } else if (entry.isIntersecting && this.isInitialized) {
                    this.isVisible = true;
                    this.resumeAnimation();
                }
            });
        }, {
            rootMargin: '50px',
            threshold: 0.1
        });

        observer.observe(this.renderElem);
    }

    pauseAnimation() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
    }

    resumeAnimation() {
        if (!this.animationId && this.model) {
            this.animateScene();
        }
    }

    animateModelMove() {
        if (!this.model || !this.isVisible) return;

        if (this.modelMoveAnimationSettings.direction === "right") {
            this.model.rotation.y += this.modelMoveAnimationSettings.value;

            if (
                this.model.rotation.y >
                this.modelMoveAnimationSettings.moreValue[0]
            ) {
                this.modelMoveAnimationSettings.direction = "left";
            }
        }

        if (this.modelMoveAnimationSettings.direction === "left") {
            this.model.rotation.y -= this.modelMoveAnimationSettings.value;

            if (
                this.model.rotation.y <
                this.modelMoveAnimationSettings.moreValue[1]
            ) {
                this.modelMoveAnimationSettings.direction = "right";
            }
        }
    }

    animateScene() {
        if (!this.isVisible || !this.renderer || !this.scene || !this.camera) {
            return;
        }

        this.renderer.render(this.scene, this.camera);
        this.animateModelMove();
        this.animationId = requestAnimationFrame(this.animateScene.bind(this));
    }

    setLights() {
        // Упрощенное освещение для лучшей производительности
        let ambientLight = new THREE.AmbientLight('#ffffff', 0.4);
        let directionalLight = new THREE.DirectionalLight('#ffffff', 0.6);
        
        directionalLight.position.set(0, 1, 0);
        
        this.scene.add(ambientLight);
        this.scene.add(directionalLight);
    }

    async setModelTexture() {
        try {
            const texture = await new Promise((resolve, reject) => {
                textureLoader.load(
                    this.texturePath,
                    resolve,
                    undefined,
                    reject
                );
            });
            
            texture.wrapS = THREE.RepeatWrapping;
            texture.wrapT = THREE.RepeatWrapping;
            texture.repeat.set(4, 1);
            // Уменьшаем качество для производительности
            texture.minFilter = THREE.LinearFilter;
            texture.magFilter = THREE.LinearFilter;

            this.model.traverse((child) => {
                if (child.isMesh) {
                    child.material.map = texture; 
                    child.material.needsUpdate = true;
                    // Отключаем тени для производительности
                    child.castShadow = false;
                    child.receiveShadow = false;
                }
            });
        } catch (error) {
            console.warn('Не удалось загрузить текстуру:', error);
        }
    }

    /**
     * @param {Array<string>} imagePaths required array of 6 string items
     */
    async setCubeTextureEnv(imagePaths) {
        try {
            const cubeTexture = await new Promise((resolve, reject) => {
                cubeTextureLoader.load(imagePaths, resolve, undefined, reject);
            });
            this.scene.environment = cubeTexture;
            this.envImages = [cubeTexture]; // Сохраняем для смены
        } catch (error) {
            console.warn('Не удалось загрузить environment текстуры:', error);
        }
    }

    setRendered() {
        this.renderer = new THREE.WebGLRenderer({
            antialias: false, // Отключаем для производительности
            alpha: true,
            powerPreference: "high-performance",
            stencil: false,
            depth: true
        });
        
        // Уменьшаем разрешение для производительности
        const pixelRatio = Math.min(window.devicePixelRatio, 1.5);
        this.renderer.setPixelRatio(pixelRatio);
        
        this.renderer.setSize(
            this.renderElem.offsetWidth,
            this.renderElem.offsetHeight
        );
        this.renderElem.appendChild(this.renderer.domElement);
        
        // Отключаем тени для производительности
        this.renderer.shadowMap.enabled = false;
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;
    }

    setCamera() {
        this.camera = new THREE.PerspectiveCamera(
            40,
            this.renderElem.offsetWidth / this.renderElem.offsetHeight,
            1,
            100 // Уменьшаем дальность для производительности
        );

        this.camera.position.z = 10;
    }

    setScene() {
        this.scene = new THREE.Scene();
    }

    async loadModel() {
        try {
            const gltf = await new Promise((resolve, reject) => {
                glbLoader.load(this.filePath, resolve, undefined, reject);
            });
            
            this.model = gltf.scene;
            this.model.rotation.x += this.modelInitialRotation.x;
            this.model.rotation.y += this.modelInitialRotation.y;
            this.model.rotation.z += this.modelInitialRotation.z;
            
            // Оптимизируем модель
            this.model.traverse((child) => {
                if (child.isMesh) {
                    child.frustumCulled = true;
                    child.matrixAutoUpdate = false;
                }
            });
            
            await this.setModelTexture();
            this.scene.add(this.model);
            
            // Запускаем анимацию только если элемент видим
            if (this.isVisible) {
                this.animateScene();
            }
            
            this.renderElem.classList.add("is-loaded");
        } catch (error) {
            console.warn('Не удалось загрузить 3D модель:', error);
        }
    }

    async init() {
        if (this.isInitialized) return;
        
        try {
            // Инициализируем загрузчики
            initLoaders();
            
            this.renderElem.innerHTML = "";
            this.setScene();
            this.setCamera();
            this.setRendered();
            this.setLights();
            
            // Асинхронная загрузка ресурсов
            await Promise.all([
                this.setCubeTextureEnv(this.envImagePaths),
                this.loadModel()
            ]);
            
            this.isInitialized = true;
        } catch (error) {
            console.warn('Ошибка инициализации Three.js сцены:', error);
        }
    }

    dispose() {
        this.pauseAnimation();
        
        if (this.renderer) {
            this.renderer.dispose();
            this.renderElem.removeChild(this.renderer.domElement);
        }
        
        if (this.scene) {
            this.scene.clear();
        }
        
        this.isInitialized = false;
    }
}