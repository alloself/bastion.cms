import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';

const textureLoader = new THREE.TextureLoader();
const cubeTextureLoader = new THREE.CubeTextureLoader();
const glbLoader = new GLTFLoader();
const dracoLoader = new DRACOLoader();

dracoLoader.setDecoderPath('/public/models/lamp/draco/');
glbLoader.setDRACOLoader(dracoLoader);
dracoLoader.preload();

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
        this.init();
    }

    animateModelMove() {
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
        this.renderer.render(this.scene, this.camera);
        this.animateModelMove();
        requestAnimationFrame(this.animateScene.bind(this));
    }

    setLights() {
        let directionalLightColor = '#ffffff';
        let ambientLightColor = '#ffffff';
        let hemiLightColors = ['#ffffff', '#ffffff'];

        let light1 = new THREE.AmbientLight(ambientLightColor, 0.2);
        let light2 = new THREE.HemisphereLight(hemiLightColors[0], hemiLightColors[1], 0.2);
        let light3 = new THREE.DirectionalLight(directionalLightColor, 0.5);
        let light4 = new THREE.DirectionalLight(directionalLightColor, 0.5);

        light1.position.set(0, 1, 0);
        light2.position.set(0, 1, 0);
        light3.position.set(0, 1, 0);
        light4.position.set(0, -4, 0);

        this.scene.add(light1);
        this.scene.add(light2);
        this.scene.add(light3);
        this.scene.add(light4);
    }

    setModelTexture() {
        const texture = textureLoader.load(this.texturePath);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(4, 1);

        this.model.traverse((child) => {
            if (child.isMesh) {
                child.material.map = texture; 
                child.material.needsUpdate = true;
            }
        });
    }

    /**
     * @param {Array<string>} imagePaths required array of 6 string items
     */
    setCubeTextureEnv(imagePaths) {
        let cubeTexture = cubeTextureLoader.load(imagePaths);
        this.scene.environment = cubeTexture;
    }

    setRendered() {
        this.renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
        });
        this.renderer.setSize(
            this.renderElem.offsetWidth,
            this.renderElem.offsetHeight
        );
        this.renderElem.appendChild(this.renderer.domElement);
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    }

    setCamera() {
        this.camera = new THREE.PerspectiveCamera(
            40,
            this.renderElem.offsetWidth / this.renderElem.offsetHeight,
            1,
            1000
        );

        this.camera.position.z = 10;
    }

    setScene() {
        this.scene = new THREE.Scene();
    }

    loadModel() {
        glbLoader.load(this.filePath, (gltf) => {
            this.model = gltf.scene;
            this.model.rotation.x += this.modelInitialRotation.x;
            this.model.rotation.y += this.modelInitialRotation.y;
            this.model.rotation.z += this.modelInitialRotation.z;
            this.setModelTexture();
            this.scene.add(this.model);
            this.animateScene();
            this.renderElem.classList.add("is-loaded");
        });
    }

    init() {
        this.renderElem.innerHTML = "";

        this.setScene();

        this.setCamera();

        this.setRendered();

        this.setCubeTextureEnv(this.envImagePaths);

        this.setLights();

        this.loadModel();
    }
}