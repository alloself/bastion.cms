import * as THREE from "three";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";
import { DRACOLoader } from "three/addons/loaders/DRACOLoader.js";

const textureLoader = new THREE.TextureLoader();
const cubeTextureLoader = new THREE.CubeTextureLoader();
const glbLoader = new GLTFLoader();
const dracoLoader = new DRACOLoader();

dracoLoader.setDecoderPath("/public/models/lamp/draco/");
glbLoader.setDRACOLoader(dracoLoader);
dracoLoader.preload();

export { textureLoader, cubeTextureLoader, glbLoader, dracoLoader };
