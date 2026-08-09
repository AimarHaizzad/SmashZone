import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

const loader = new GLTFLoader();
const activeViewers = new Set();

function readConfig() {
    const node = document.getElementById('product-viewer-config');
    if (!node) {
        return { products: [] };
    }

    try {
        return JSON.parse(node.textContent);
    } catch {
        return { products: [] };
    }
}

function buildProceduralRacket() {
    const group = new THREE.Group();

    const frameMat = new THREE.MeshStandardMaterial({ color: 0xf0f0ec, roughness: 0.22, metalness: 0.28 });
    const goldMat = new THREE.MeshStandardMaterial({ color: 0xc9a84c, roughness: 0.18, metalness: 0.55 });
    const redMat = new THREE.MeshStandardMaterial({ color: 0xc8102e, roughness: 0.4, metalness: 0.05 });
    const blackMat = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.45 });
    const gripMat = new THREE.MeshStandardMaterial({ color: 0xe8e8e2, roughness: 0.88, metalness: 0 });
    const stringMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.12, metalness: 0 });

    const head = new THREE.Mesh(
        new THREE.TorusGeometry(0.102, 0.0082, 14, 56, Math.PI * 1.62),
        frameMat,
    );
    head.rotation.z = Math.PI * 0.5;
    head.position.y = 0.17;
    group.add(head);

    const throat = new THREE.Mesh(new THREE.BoxGeometry(0.058, 0.024, 0.011), frameMat);
    throat.position.set(0, 0.052, 0);
    group.add(throat);

    const accent = new THREE.Mesh(new THREE.BoxGeometry(0.024, 0.016, 0.006), redMat);
    accent.position.set(0, 0.055, 0.007);
    group.add(accent);

    const goldTop = new THREE.Mesh(new THREE.BoxGeometry(0.092, 0.02, 0.004), goldMat);
    goldTop.position.set(0, 0.258, 0.006);
    group.add(goldTop);

    const redSide = new THREE.Mesh(new THREE.BoxGeometry(0.024, 0.048, 0.003), redMat);
    redSide.position.set(-0.072, 0.19, 0.006);
    group.add(redSide);

    const blackSide = new THREE.Mesh(new THREE.BoxGeometry(0.024, 0.048, 0.003), blackMat);
    blackSide.position.set(0.072, 0.19, 0.006);
    group.add(blackSide);

    const shaft = new THREE.Mesh(new THREE.CylinderGeometry(0.0062, 0.0062, 0.28, 20), frameMat);
    shaft.position.y = -0.09;
    group.add(shaft);

    const grip = new THREE.Mesh(new THREE.CylinderGeometry(0.012, 0.011, 0.18, 24), gripMat);
    grip.position.y = -0.32;
    group.add(grip);

    const cap = new THREE.Mesh(new THREE.CylinderGeometry(0.011, 0.011, 0.012, 20), frameMat);
    cap.position.y = -0.42;
    group.add(cap);

    const topY = 0.21;
    const bottomY = 0.07;
    const leftX = -0.075;
    const rightX = 0.075;
    const stringRadius = 0.00028;

    for (let i = 0; i < 14; i += 1) {
        const t = i / 13;
        const x = leftX + (rightX - leftX) * t;
        const vertical = new THREE.Mesh(
            new THREE.CylinderGeometry(stringRadius, stringRadius, topY - bottomY, 4),
            stringMat,
        );
        vertical.position.set(x, (topY + bottomY) / 2, 0);
        group.add(vertical);
    }

    for (let j = 0; j < 16; j += 1) {
        const t = j / 15;
        const y = bottomY + (topY - bottomY) * t;
        const normalized = (y - 0.17) / 0.1;
        const halfW = 0.075 * Math.sqrt(Math.max(0.12, 1 - normalized * normalized * 0.8));
        const horizontal = new THREE.Mesh(
            new THREE.CylinderGeometry(stringRadius, stringRadius, halfW * 2, 4),
            stringMat,
        );
        horizontal.rotation.z = Math.PI / 2;
        horizontal.position.set(0, y, 0);
        group.add(horizontal);
    }

    group.position.y = 0.05;
    return group;
}

function resolveModel(modelUrl) {
    if (modelUrl?.startsWith('procedural:')) {
        const type = modelUrl.split(':')[1];
        if (type === 'racket') {
            return Promise.resolve(buildProceduralRacket());
        }
    }

    return new Promise((resolve, reject) => {
        loader.load(modelUrl, (gltf) => resolve(gltf.scene), undefined, reject);
    });
}

function fitCameraToObject(camera, controls, object, offset = 1.35) {
    const box = new THREE.Box3().setFromObject(object);
    const size = box.getSize(new THREE.Vector3());
    const center = box.getCenter(new THREE.Vector3());

    const maxDim = Math.max(size.x, size.y, size.z);
    const fov = (camera.fov * Math.PI) / 180;
    let distance = Math.abs(maxDim / 2 / Math.tan(fov / 2));
    distance *= offset;

    camera.position.set(
        center.x + distance * 0.35,
        center.y + distance * 0.08,
        center.z + distance * 0.85,
    );
    camera.lookAt(center);

    controls.target.copy(center);
    controls.update();
}

class ProductViewer {
    constructor(container, modelUrl, options = {}) {
        this.container = container;
        this.modelUrl = modelUrl;
        this.autoRotate = options.autoRotate ?? false;
        this.interactive = options.interactive ?? true;
        this.onReady = options.onReady ?? null;

        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(options.background ?? 0x1e293b);

        const width = container.clientWidth || 320;
        const height = container.clientHeight || 256;

        this.camera = new THREE.PerspectiveCamera(42, width / height, 0.01, 100);
        this.camera.position.set(0.8, 0.25, 0.9);

        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.setSize(width, height);
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.1;
        container.appendChild(this.renderer.domElement);

        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.enablePan = false;
        this.controls.autoRotate = this.autoRotate;
        this.controls.autoRotateSpeed = 1.6;
        this.controls.enabled = this.interactive;

        const ambient = new THREE.AmbientLight(0xffffff, 0.85);
        const key = new THREE.DirectionalLight(0xffffff, 1.15);
        key.position.set(2, 4, 3);
        const fill = new THREE.DirectionalLight(0x93c5fd, 0.45);
        fill.position.set(-2, 1, -2);
        const rim = new THREE.DirectionalLight(0xfde68a, 0.35);
        rim.position.set(0, -1, -3);
        this.scene.add(ambient, key, fill, rim);

        this.model = null;
        this.raf = null;
        this.resizeObserver = new ResizeObserver(() => this.handleResize());
        this.resizeObserver.observe(container);

        activeViewers.add(this);
        this.loadModel();
        this.animate();
    }

    loadModel() {
        resolveModel(this.modelUrl)
            .then((model) => {
                this.model = model;
                this.scene.add(this.model);
                fitCameraToObject(this.camera, this.controls, this.model, 1.45);
                this.onReady?.();
            })
            .catch(() => {
                this.container.dispatchEvent(new CustomEvent('product-viewer-error'));
            });
    }

    handleResize() {
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        if (!width || !height) {
            return;
        }

        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
    }

    animate() {
        this.raf = requestAnimationFrame(() => this.animate());
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    dispose() {
        if (this.raf) {
            cancelAnimationFrame(this.raf);
        }

        this.resizeObserver.disconnect();
        this.controls.dispose();
        this.renderer.dispose();

        if (this.renderer.domElement.parentNode === this.container) {
            this.container.removeChild(this.renderer.domElement);
        }

        activeViewers.delete(this);
    }
}

function buildModal() {
    if (document.getElementById('product-viewer-modal')) {
        return;
    }

    const modal = document.createElement('div');
    modal.id = 'product-viewer-modal';
    modal.className = 'hidden fixed inset-0 z-[80] items-center justify-center p-4';
    modal.innerHTML = `
        <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-sm" data-product-modal-close></div>
        <div class="relative w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">3D Product View</p>
                    <h3 id="product-viewer-modal-title" class="text-lg font-bold text-gray-900"></h3>
                </div>
                <button type="button" data-product-modal-close class="rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="relative bg-slate-800">
                <div id="product-viewer-modal-canvas" class="w-full h-[420px] sm:h-[520px]"></div>
                <div id="product-viewer-modal-loading" class="absolute inset-0 flex items-center justify-center bg-slate-800/90 text-sm font-medium text-slate-200">
                    Loading 3D model...
                </div>
            </div>
            <div class="px-5 py-4 text-sm text-gray-600 bg-gray-50">
                Drag to rotate · Scroll to zoom
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    modal.querySelectorAll('[data-product-modal-close]').forEach((el) => {
        el.addEventListener('click', closeModal);
    });
}

let modalViewer = null;

function openModal(product) {
    buildModal();

    const modal = document.getElementById('product-viewer-modal');
    const title = document.getElementById('product-viewer-modal-title');
    const canvas = document.getElementById('product-viewer-modal-canvas');
    const loading = document.getElementById('product-viewer-modal-loading');

    title.textContent = product.name;
    loading.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    if (modalViewer) {
        modalViewer.dispose();
        modalViewer = null;
    }

    canvas.innerHTML = '';

    modalViewer = new ProductViewer(canvas, product.modelUrl, {
        autoRotate: false,
        interactive: true,
        background: 0x0f172a,
        onReady: () => loading.classList.add('hidden'),
    });
}

function closeModal() {
    const modal = document.getElementById('product-viewer-modal');
    if (!modal) {
        return;
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');

    if (modalViewer) {
        modalViewer.dispose();
        modalViewer = null;
    }
}

function initCardPreview(card, product) {
    const canvas = card.querySelector('[data-product-3d-canvas]');
    const loading = card.querySelector('[data-product-3d-loading]');
    if (!canvas || canvas.dataset.initialized === 'true') {
        return;
    }

    canvas.dataset.initialized = 'true';

    const viewer = new ProductViewer(canvas, product.modelUrl, {
        autoRotate: true,
        interactive: false,
        background: 0x1e293b,
        onReady: () => loading?.classList.add('hidden'),
    });

    canvas.addEventListener('product-viewer-error', () => {
        loading?.classList.remove('hidden');
        if (loading) {
            loading.textContent = '3D preview unavailable';
        }
    });

    card._productViewer = viewer;
}

function init() {
    const config = readConfig();
    const productsById = Object.fromEntries(config.products.map((product) => [String(product.id), product]));

    buildModal();

    document.querySelectorAll('[data-product-3d-card]').forEach((card) => {
        const product = productsById[card.dataset.productId];
        if (!product) {
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        initCardPreview(card, product);
                        observer.disconnect();
                    }
                });
            },
            { rootMargin: '120px' },
        );

        observer.observe(card);

        card.querySelector('[data-product-3d-open]')?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            openModal(product);
        });

        card.querySelector('[data-product-3d-canvas]')?.addEventListener('click', () => openModal(product));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
