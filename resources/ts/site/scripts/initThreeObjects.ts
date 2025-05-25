// Ленивый импорт Three.js
let ThreeLampScene: any = null;

async function loadThreeJS() {
    if (!ThreeLampScene) {
        const module = await import("@site/scripts/threeLampScene.js");
        ThreeLampScene = module.ThreeLampScene;
    }
    return ThreeLampScene;
}

export async function initThreeObjects() {
    // Проверяем, есть ли элементы для Three.js на странице
    const threeElements = document.querySelectorAll("[class*='three-lamp']");
    if (threeElements.length === 0) {
        return;
    }

    // Загружаем Three.js только когда он действительно нужен
    const ThreeLampSceneClass = await loadThreeJS();

    function getElementOffsetTop(selector: string) {
        let offset = 0;
        let element = document.querySelector(selector);

        if (element) {
            offset =
                element.getBoundingClientRect().top +
                window.scrollY -
                element.getBoundingClientRect().height;
        }

        return offset;
    }

    // Создаем объекты только для существующих элементов
    const lampInstances: Record<string, any> = {};

    // Проверяем каждый элемент перед созданием сцены
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
            }
        }
    ];

    // Создаем экземпляры только для существующих элементов
    for (const lampConfig of lampConfigs) {
        const element = document.querySelector(lampConfig.selector);
        if (element) {
            lampInstances[lampConfig.key] = new ThreeLampSceneClass({
                ...lampConfig.config,
                renderElem: element
            });
        }
    }

    // Оптимизированный обработчик скролла с throttling
    let lastKnownScrollPosition = 0;
    let deltaY = 0;
    let footerOffseTop = getElementOffsetTop(".js-app-footer");
    let subscribeBlockOffsetTop = getElementOffsetTop(".js-subscribe-block");
    let ticking = false;

    function moveThreeObjectsOnScroll() {
        if (!ticking) {
            requestAnimationFrame(() => {
                deltaY = window.scrollY - lastKnownScrollPosition;
                lastKnownScrollPosition = window.scrollY;

                if (window.scrollY > 0) {
                    if (lampInstances.mainLeftLamp?.model) {
                        lampInstances.mainLeftLamp.model.rotation.x += deltaY * 0.0016;
                        lampInstances.mainLeftLamp.model.rotation.z -= deltaY * 0.0018;
                    }

                    if (lampInstances.mainRightLamp?.model) {
                        lampInstances.mainRightLamp.model.rotation.x -= deltaY * 0.0013;
                    }

                    if (lampInstances.mainBannerLamp?.model) {
                        lampInstances.mainBannerLamp.model.rotation.x -= deltaY * 0.0013;
                    }

                    if (lampInstances.subscribeLeftLamp?.model) {
                        lampInstances.subscribeLeftLamp.model.rotation.x -= deltaY * 0.0013;
                    }

                    if (
                        lampInstances.subscribeRightLamp?.model &&
                        window.scrollY > subscribeBlockOffsetTop
                    ) {
                        lampInstances.subscribeRightLamp.model.rotation.x -= deltaY * 0.002;
                    }

                    if (lampInstances.footerLamp?.model && window.scrollY > footerOffseTop) {
                        lampInstances.footerLamp.model.rotation.x += deltaY * 0.0013;
                    }
                }
                ticking = false;
            });
            ticking = true;
        }
    }

    // Добавляем обработчик скролла только если есть активные лампы
    if (Object.keys(lampInstances).length > 0) {
        window.addEventListener("scroll", moveThreeObjectsOnScroll, { passive: true });
    }

    // Оптимизированная функция смены изображений окружения
    function changeEnvImagesInterval(lampInstances: Record<string, any>, interval: number = 4000) {
        const activeLamps = Object.values(lampInstances).filter(lamp => lamp && lamp.model);
        
        if (activeLamps.length === 0) return;

        const randomBetween = (min: number, max: number) => {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        };

        setInterval(() => {
            activeLamps.forEach((lamp) => {
                if (lamp.envImages && lamp.envImages.length > 0) {
                    const randomIndex = randomBetween(0, lamp.envImages.length - 1);
                    if (lamp.scene && lamp.scene.environment) {
                        lamp.scene.environment = lamp.envImages[randomIndex];
                    }
                }
            });
        }, interval);
    }

    // Запускаем смену изображений только если есть активные лампы
    if (Object.keys(lampInstances).length > 0) {
        changeEnvImagesInterval(lampInstances);
    }
}