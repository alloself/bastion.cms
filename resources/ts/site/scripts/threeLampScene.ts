import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls.js";
import {
    cubeTextureLoader,
    glbLoader,
    textureLoader,
} from "./treeObjectsСache";

interface ModelRotation {
    x: number;
    y: number;
    z: number;
}

interface ModelAnimationSettings {
    direction: "left" | "right";
    axis: "x" | "y" | "z";
    value: number;
    moreValue: [number, number];
}

interface ThreeLampSceneConfig {
    texturePath: string;
    envImagePaths: string[];
    filePath: string;
    renderElem: HTMLElement;
    orbitControlEnabled?: boolean;
    modelInitialRotation?: ModelRotation;
    modelMoveAnimationSettings?: ModelAnimationSettings;
}

export class ThreeLampScene {
    scene: THREE.Scene | null = null;
    camera: THREE.PerspectiveCamera | null = null;
    renderer: THREE.WebGLRenderer | null = null;
    model: THREE.Object3D | null = null;
    renderElem: HTMLElement | null = null;
    texturePath: string | null = null;
    envImagePaths: string[] | null = null;
    filePath: string | null = null;
    controls: OrbitControls | null = null;
    orbitControlEnabled: boolean = false;
    modelInitialRotation: ModelRotation = { x: 0, y: 0, z: 0 };
    modelMoveAnimationSettings: ModelAnimationSettings = {
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
    }: ThreeLampSceneConfig) {
        if (!renderElem) return;

        this.renderElem = renderElem;
        this.texturePath = texturePath;
        this.envImagePaths = envImagePaths;
        this.filePath = filePath;

        if (orbitControlEnabled) {
            this.orbitControlEnabled = orbitControlEnabled;
        }
        if (modelInitialRotation) {
            this.modelInitialRotation = modelInitialRotation;
        }
        if (modelMoveAnimationSettings) {
            this.modelMoveAnimationSettings = modelMoveAnimationSettings;
        }

        this.init();
    }

    animateModelMove() {
        if (!this.model) return;

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
        if (this.scene && this.camera && this.renderer) {
            this.renderer.render(this.scene, this.camera);
            this.animateModelMove();
            this.controls?.update();
            requestAnimationFrame(this.animateScene.bind(this));
        }
    }

    setLights() {
        const ambient = new THREE.AmbientLight("#ffffff", 0.2);
        const hemi = new THREE.HemisphereLight("#ffffff", "#ffffff", 0.2);
        const dir1 = new THREE.DirectionalLight("#ffffff", 0.5);
        const dir2 = new THREE.DirectionalLight("#ffffff", 0.5);

        ambient.position.set(0, 1, 0);
        hemi.position.set(0, 1, 0);
        dir1.position.set(0, 1, 0);
        dir2.position.set(0, -4, 0);

        this.scene?.add(ambient, hemi, dir1, dir2);
    }

    setModelTexture() {
        if (!this.model || !this.texturePath || !this.scene) return;

        const texture = textureLoader.load(this.texturePath);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(4, 1);

        this.model.traverse((child: THREE.Object3D) => {
            if ((child as THREE.Mesh).isMesh) {
                const mesh = child as THREE.Mesh;
                if (mesh.material && "map" in mesh.material) {
                    mesh.material.map = texture;
                    mesh.material.needsUpdate = true;
                }
            }
        });
    }

    setCubeTextureEnv(imagePaths: string[]) {
        if (!this.scene) return;
        const cubeTexture = cubeTextureLoader.load(imagePaths);
        this.scene.environment = cubeTexture;
    }

    setRendered() {
        if (!this.renderElem) return;

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
        if (!this.renderElem) return;

        this.camera = new THREE.PerspectiveCamera(
            40,
            this.renderElem.offsetWidth / this.renderElem.offsetHeight,
            1,
            1000
        );
        this.camera.position.z = 10;

        if (
            this.orbitControlEnabled &&
            this.renderer?.domElement &&
            this.camera
        ) {
            this.controls = new OrbitControls(
                this.camera,
                this.renderer.domElement
            );
            this.controls.enableDamping = true;
            this.controls.dampingFactor = 0.05;
        }
    }

    setScene() {
        this.scene = new THREE.Scene();
    }

    loadModel() {
        if (!this.filePath || !this.scene) return;

        glbLoader.load(this.filePath, (gltf: any) => {
            this.model = gltf.scene;
            if (!this.model) return;

            this.model.rotation.x += this.modelInitialRotation.x;
            this.model.rotation.y += this.modelInitialRotation.y;
            this.model.rotation.z += this.modelInitialRotation.z;

            this.setModelTexture();
            this.scene?.add(this.model);
            this.animateScene();
            this.renderElem?.classList.add("is-loaded");
        });
    }

    init() {
        if (!this.renderElem || !this.envImagePaths) return;

        this.renderElem.innerHTML = "";

        this.setScene();
        this.setCamera();
        this.setRendered();
        this.setCubeTextureEnv(this.envImagePaths);
        this.setLights();
        this.loadModel();
    }
}
