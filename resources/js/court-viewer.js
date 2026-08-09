import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { CSS2DObject, CSS2DRenderer } from 'three/examples/jsm/renderers/CSS2DRenderer.js';

const COURT_W = 6.1;
const COURT_D = 13.4;
const COURT_H = 0.08;
const GAP = 2.4;

const STATUS_COLORS = {
    available: new THREE.Color(0x6d8094),
    booked: new THREE.Color(0xb45353),
    mine: new THREE.Color(0x4b7ec7),
    past: new THREE.Color(0x4b5563),
    hover: new THREE.Color(0xfacc15),
    selected: new THREE.Color(0xfacc15),
};

function computeGridLayout(count) {
    if (count <= 1) return { cols: 1, rows: 1 };
    if (count <= 2) return { cols: 2, rows: 1 };
    if (count <= 3) return { cols: 3, rows: 1 };
    if (count <= 4) return { cols: 2, rows: 2 };
    if (count <= 6) return { cols: 3, rows: 2 };
    if (count <= 8) return { cols: 4, rows: 2 };
    if (count <= 9) return { cols: 3, rows: 3 };
    if (count <= 12) return { cols: 4, rows: 3 };

    const cols = Math.ceil(Math.sqrt(count));
    return { cols, rows: Math.ceil(count / cols) };
}

function meshKeyForCourt(court) {
    return `Court_${court.id}`;
}

function applyCourtColor(mesh, status, emissiveIntensity = 0.08) {
    if (!mesh?.material) {
        return;
    }

    const materials = Array.isArray(mesh.material) ? mesh.material : [mesh.material];
    const color = STATUS_COLORS[status] ?? STATUS_COLORS.available;

    materials.forEach((material) => {
        material.color.copy(color);
        material.emissive.copy(color);
        material.emissiveIntensity = emissiveIntensity;
        material.roughness = 0.55;
        material.metalness = 0.06;
        material.needsUpdate = true;
    });
}

function normalizeMeshKey(name) {
    const match = name?.match(/^(Court_\d+)$/);
    return match ? match[1] : null;
}

function focusBookingCourt(courtId) {
    const header = document.querySelector(`th[data-court-id="${courtId}"]`);
    const tableWrapper = document.querySelector('[data-tutorial="booking-table"] .overflow-x-auto');

    if (header) {
        header.classList.add('ring-4', 'ring-amber-400', 'ring-offset-2');
        setTimeout(() => {
            header.classList.remove('ring-4', 'ring-amber-400', 'ring-offset-2');
        }, 2200);
    }

    if (tableWrapper && header) {
        const offset = header.offsetLeft - tableWrapper.clientWidth / 2 + header.clientWidth / 2;
        tableWrapper.scrollTo({ left: Math.max(0, offset), behavior: 'smooth' });
    }

    document.getElementById('booking-table-section')?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
}

function updateCourtLabel(labelEl, court) {
    if (!labelEl || !court) {
        return;
    }

    const statusLabels = {
        available: 'Available',
        booked: 'Fully booked',
        mine: 'Your booking',
        past: 'Past slots',
    };

    labelEl.textContent = `${court.name} · ${statusLabels[court.status] ?? 'Available'}`;
}

function frameCameraToScene(camera, controls, courtsCount) {
    const { cols, rows } = computeGridLayout(courtsCount);
    const gridW = cols * COURT_W + (cols - 1) * GAP;
    const gridD = rows * COURT_D + (rows - 1) * GAP;
    const margin = 4;
    const facD = gridD + margin * 2;
    const hd = facD / 2;
    const span = Math.max(gridW, gridD);

    const eyeHeight = 1.05;
    const lookAt = new THREE.Vector3(0, 5, 0.9);

    camera.up.set(0, 0, 1);
    camera.position.set(0, -hd + 0.15, eyeHeight);
    camera.lookAt(lookAt);

    controls.target.copy(lookAt);
    controls.minDistance = 3;
    controls.maxDistance = span * 2.2;
    controls.minPolarAngle = Math.PI / 2 - 0.28;
    controls.maxPolarAngle = Math.PI / 2 + 0.18;
    controls.update();
}

function createMaterials() {
    return {
        floor: new THREE.MeshStandardMaterial({ color: 0x374151, roughness: 0.92 }),
        courtBase: new THREE.MeshStandardMaterial({ color: 0x4b5563, roughness: 0.65 }),
        court: new THREE.MeshStandardMaterial({
            color: 0x6d8094,
            roughness: 0.55,
            metalness: 0.06,
            side: THREE.DoubleSide,
        }),
        line: new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.2 }),
        net: new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.5, transparent: true, opacity: 0.35 }),
        post: new THREE.MeshStandardMaterial({ color: 0xcbd5e1, roughness: 0.35, metalness: 0.4 }),
        curb: new THREE.MeshStandardMaterial({ color: 0x475569, roughness: 0.88 }),
        bench: new THREE.MeshStandardMaterial({ color: 0x8b5e34, roughness: 0.72 }),
    };
}

function addBox(group, name, loc, size, material, { castShadow = false, receiveShadow = true } = {}) {
    const geometry = new THREE.BoxGeometry(size[0], size[1], size[2]);
    const mesh = new THREE.Mesh(geometry, material);
    mesh.name = name;
    mesh.position.set(loc[0], loc[1], loc[2]);
    mesh.castShadow = castShadow;
    mesh.receiveShadow = receiveShadow;
    group.add(mesh);
    return mesh;
}

function addCourtSurface(group, name, cx, cy, material, clickableMeshes) {
    const base = addBox(
        group,
        `${name}_Base`,
        [cx, cy, COURT_H / 2],
        [COURT_W + 0.12, COURT_D + 0.12, COURT_H],
        material.base,
        { castShadow: true }
    );
    base.material = material.base;

    const surfaceGeometry = new THREE.PlaneGeometry(COURT_W, COURT_D);
    const surface = new THREE.Mesh(surfaceGeometry, material.surface.clone());
    surface.name = name;
    surface.position.set(cx, cy, COURT_H + 0.012);
    surface.receiveShadow = true;
    group.add(surface);
    clickableMeshes.push(surface);

    return surface;
}

function addLine(group, prefix, cx, cy, z, sx, sy, material) {
    addBox(group, `${prefix}_Line`, [cx, cy, z], [sx, sy, 0.016], material);
}

function createCourtNameLabel(name) {
    const element = document.createElement('div');
    element.textContent = name;
    element.style.cssText = [
        'padding: 5px 12px',
        'background: rgba(15, 23, 42, 0.88)',
        'color: #f8fafc',
        'font-size: 12px',
        'font-weight: 600',
        'letter-spacing: 0.01em',
        'border-radius: 9999px',
        'border: 1px solid rgba(255, 255, 255, 0.18)',
        'white-space: nowrap',
        'pointer-events: none',
        'box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35)',
        'transform: translate(-50%, -50%)',
    ].join(';');

    const label = new CSS2DObject(element);
    return { label, element };
}

function styleCourtNameLabel(element, state) {
    if (state === 'selected') {
        element.style.background = 'rgba(250, 204, 21, 0.95)';
        element.style.color = '#1e293b';
        element.style.borderColor = 'rgba(250, 204, 21, 1)';
        return;
    }

    if (state === 'hover') {
        element.style.background = 'rgba(250, 204, 21, 0.82)';
        element.style.color = '#1e293b';
        element.style.borderColor = 'rgba(250, 204, 21, 0.9)';
        return;
    }

    element.style.background = 'rgba(15, 23, 42, 0.88)';
    element.style.color = '#f8fafc';
    element.style.borderColor = 'rgba(255, 255, 255, 0.18)';
}

function buildCourt(group, court, cx, cy, materials, clickableMeshes, courtLabels) {
    const key = meshKeyForCourt(court);
    const courtGroup = new THREE.Group();
    courtGroup.name = `${key}_Group`;

    const courtMesh = addCourtSurface(
        courtGroup,
        key,
        cx,
        cy,
        { base: materials.courtBase, surface: materials.court },
        clickableMeshes
    );

    const hw = COURT_W / 2;
    const hd = COURT_D / 2;
    const z = COURT_H + 0.02;

    addLine(courtGroup, key, cx, cy + hd - 0.04, z, COURT_W - 0.08, 0.05, materials.line);
    addLine(courtGroup, key, cx, cy - hd + 0.04, z, COURT_W - 0.08, 0.05, materials.line);
    addLine(courtGroup, key, cx - hw + 0.04, cy, z, 0.05, COURT_D - 0.08, materials.line);
    addLine(courtGroup, key, cx + hw - 0.04, cy, z, 0.05, COURT_D - 0.08, materials.line);
    addLine(courtGroup, key, cx, cy, z, COURT_W - 0.08, 0.05, materials.line);

    const svc = 1.98;
    addLine(courtGroup, `${key}_SvcT`, cx, cy + svc, z, COURT_W - 0.08, 0.04, materials.line);
    addLine(courtGroup, `${key}_SvcB`, cx, cy - svc, z, COURT_W - 0.08, 0.04, materials.line);

    const inset = 0.46;
    addLine(courtGroup, `${key}_SingleL`, cx - hw + inset, cy, z, 0.04, COURT_D - 0.08, materials.line);
    addLine(courtGroup, `${key}_SingleR`, cx + hw + inset, cy, z, 0.04, COURT_D - 0.08, materials.line);

    const postX = hw - 0.15;
    addBox(courtGroup, `${key}_Post_L`, [cx - postX, cy, 0.78], [0.08, 0.08, 1.56], materials.post, { castShadow: true });
    addBox(courtGroup, `${key}_Post_R`, [cx + postX, cy, 0.78], [0.08, 0.08, 1.56], materials.post, { castShadow: true });
    addBox(courtGroup, `${key}_Net`, [cx, cy, 0.78], [COURT_W - 0.4, 0.04, 1.5], materials.net);
    addBox(courtGroup, `${key}_NetTape`, [cx, cy, 1.55], [COURT_W - 0.3, 0.06, 0.04], materials.line);

    const { label, element } = createCourtNameLabel(court.name);
    label.position.set(cx, cy, 2.75);
    courtGroup.add(label);
    courtLabels.set(key, element);

    group.add(courtGroup);
    return courtMesh;
}

function buildProceduralFacility(courts) {
    const facility = new THREE.Group();
    facility.name = 'SmashZone_Facility';

    if (!courts.length) {
        return { facility, courtMeshes: new Map(), clickableMeshes: [], courtLabels: new Map() };
    }

    const materials = createMaterials();
    const { cols, rows } = computeGridLayout(courts.length);
    const courtMeshes = new Map();
    const clickableMeshes = [];
    const courtLabels = new Map();

    const gridW = cols * COURT_W + (cols - 1) * GAP;
    const gridD = rows * COURT_D + (rows - 1) * GAP;
    const margin = 4;
    const facW = gridW + margin * 2;
    const facD = gridD + margin * 2;
    const hw = facW / 2;
    const hd = facD / 2;

    addBox(facility, 'Facility_Floor', [0, 0, -0.06], [facW, facD, 0.12], materials.floor);

    const startX = -((cols - 1) * (COURT_W + GAP)) / 2;
    const startY = ((rows - 1) * (COURT_D + GAP)) / 2;

    courts.forEach((court, index) => {
        const row = Math.floor(index / cols);
        const col = index % cols;
        const cx = startX + col * (COURT_W + GAP);
        const cy = startY - row * (COURT_D + GAP);
        const mesh = buildCourt(facility, court, cx, cy, materials, clickableMeshes, courtLabels);
        courtMeshes.set(meshKeyForCourt(court), mesh);
    });

    const curbH = 0.2;
    addBox(facility, 'Curb_North', [0, hd + 0.1, curbH / 2], [hw, 0.08, curbH], materials.curb);
    addBox(facility, 'Curb_South', [0, -hd - 0.05, curbH / 2], [hw, 0.08, curbH], materials.curb);

    const benchCount = Math.min(4, courts.length);
    const benchSpacing = facW / (benchCount + 1);
    const benchY = -hd + 0.35;
    for (let i = 0; i < benchCount; i += 1) {
        const x = -hw + benchSpacing * (i + 1);
        addBox(facility, `Bench_${i + 1}`, [x, benchY, 0.2], [3.8, 0.55, 0.4], materials.bench, { castShadow: true });
        addBox(facility, `Bench_Slat_${i + 1}_A`, [x, benchY, 0.38], [3.5, 0.45, 0.04], materials.bench);
        addBox(facility, `Bench_Slat_${i + 1}_B`, [x, benchY, 0.44], [3.5, 0.45, 0.04], materials.bench);
        addBox(facility, `Bench_Back_${i + 1}`, [x, benchY, 0.58], [3.8, 0.06, 0.48], materials.bench, { castShadow: true });
    }

    return { facility, courtMeshes, clickableMeshes, courtLabels };
}

export function initCourtViewer(root, config) {
    const canvasHost = root.querySelector('[data-court-viewer-canvas]');
    const loadingEl = root.querySelector('[data-court-viewer-loading]');
    const errorEl = root.querySelector('[data-court-viewer-error]');
    const labelEl = root.querySelector('[data-court-viewer-label]');
    const courts = config?.courts ?? [];

    if (!canvasHost || courts.length === 0) {
        return null;
    }

    const courtsByMesh = new Map(courts.map((court) => [meshKeyForCourt(court), court]));
    let courtMeshes = new Map();
    let clickableMeshes = [];
    let courtLabels = new Map();
    let selectedMeshKey = null;
    let hoveredMeshKey = null;

    canvasHost.style.position = 'relative';

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0f172a);
    scene.fog = new THREE.Fog(0x0f172a, 55, 120);

    const camera = new THREE.PerspectiveCamera(
        48,
        canvasHost.clientWidth / Math.max(canvasHost.clientHeight, 1),
        0.1,
        300
    );
    camera.up.set(0, 0, 1);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(canvasHost.clientWidth, Math.max(canvasHost.clientHeight, 320));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    canvasHost.appendChild(renderer.domElement);

    const labelRenderer = new CSS2DRenderer();
    labelRenderer.setSize(canvasHost.clientWidth, Math.max(canvasHost.clientHeight, 320));
    labelRenderer.domElement.style.position = 'absolute';
    labelRenderer.domElement.style.inset = '0';
    labelRenderer.domElement.style.pointerEvents = 'none';
    canvasHost.appendChild(labelRenderer.domElement);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.06;
    controls.minPolarAngle = Math.PI / 2 - 0.28;
    controls.maxPolarAngle = Math.PI / 2 + 0.18;
    controls.enablePan = true;

    scene.add(new THREE.HemisphereLight(0xbfdbfe, 0x0f172a, 0.45));

    const keyLight = new THREE.DirectionalLight(0xffffff, 1.2);
    keyLight.position.set(8, -6, 18);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.set(2048, 2048);
    scene.add(keyLight);

    const fillLight = new THREE.DirectionalLight(0x93c5fd, 0.35);
    fillLight.position.set(-16, 12, 8);
    scene.add(fillLight);

    const rimLight = new THREE.DirectionalLight(0x86efac, 0.15);
    rimLight.position.set(0, 8, 14);
    scene.add(rimLight);

    const raycaster = new THREE.Raycaster();
    const pointer = new THREE.Vector2();

    function paintCourts() {
        courtMeshes.forEach((mesh, meshKey) => {
            const court = courtsByMesh.get(meshKey);
            if (!court) {
                return;
            }

            if (meshKey === selectedMeshKey) {
                applyCourtColor(mesh, 'selected', 0.22);
                return;
            }

            if (meshKey === hoveredMeshKey) {
                applyCourtColor(mesh, 'hover', 0.18);
                return;
            }

            applyCourtColor(mesh, court.status, court.status === 'mine' ? 0.14 : 0.06);
        });

        courtLabels.forEach((element, meshKey) => {
            if (meshKey === selectedMeshKey) {
                styleCourtNameLabel(element, 'selected');
            } else if (meshKey === hoveredMeshKey) {
                styleCourtNameLabel(element, 'hover');
            } else {
                styleCourtNameLabel(element, 'default');
            }
        });
    }

    function setSelectedCourt(meshKey) {
        selectedMeshKey = meshKey;
        const court = courtsByMesh.get(meshKey);
        updateCourtLabel(labelEl, court);
        paintCourts();

        if (court?.id) {
            focusBookingCourt(court.id);
        }
    }

    function onPointerMove(event) {
        const rect = renderer.domElement.getBoundingClientRect();
        pointer.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        pointer.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(pointer, camera);
        const hits = raycaster.intersectObjects(clickableMeshes, false);
        const nextHover = hits.length ? normalizeMeshKey(hits[0].object.name) : null;

        if (nextHover !== hoveredMeshKey) {
            hoveredMeshKey = nextHover;
            renderer.domElement.style.cursor = hoveredMeshKey ? 'pointer' : 'grab';
            if (!selectedMeshKey && hoveredMeshKey) {
                updateCourtLabel(labelEl, courtsByMesh.get(hoveredMeshKey));
            } else if (!selectedMeshKey) {
                labelEl.textContent = `Seated view · ${courts.length} court${courts.length === 1 ? '' : 's'} · Drag to look around · Click a court`;
            }
            paintCourts();
        }
    }

    function onClick() {
        if (!hoveredMeshKey) {
            return;
        }
        setSelectedCourt(hoveredMeshKey);
    }

    function onResize() {
        const width = canvasHost.clientWidth;
        const height = Math.max(canvasHost.clientHeight, 320);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
        labelRenderer.setSize(width, height);
    }

    renderer.domElement.addEventListener('pointermove', onPointerMove);
    renderer.domElement.addEventListener('click', onClick);
    window.addEventListener('resize', onResize);

    try {
        const built = buildProceduralFacility(courts);
        scene.add(built.facility);
        courtMeshes = built.courtMeshes;
        clickableMeshes = built.clickableMeshes;
        courtLabels = built.courtLabels;

        frameCameraToScene(camera, controls, courts.length);
        paintCourts();
        labelEl.textContent = `Seated view · ${courts.length} court${courts.length === 1 ? '' : 's'} · Drag to look around · Click a court`;

        if (loadingEl) {
            loadingEl.classList.add('hidden');
        }
    } catch (error) {
        console.error('Failed to build procedural facility', error);
        if (loadingEl) {
            loadingEl.classList.add('hidden');
        }
        if (errorEl) {
            errorEl.textContent = 'Unable to build the 3D facility view. Use the booking table below.';
            errorEl.classList.remove('hidden');
        }
    }

    let animationId = 0;
    const animate = () => {
        animationId = requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
        labelRenderer.render(scene, camera);
    };
    animate();

    const resetView = () => {
        selectedMeshKey = null;
        hoveredMeshKey = null;
        frameCameraToScene(camera, controls, courts.length);
        paintCourts();
        labelEl.textContent = `Seated view · ${courts.length} court${courts.length === 1 ? '' : 's'} · Drag to look around · Click a court`;
    };

    return {
        destroy() {
            cancelAnimationFrame(animationId);
            window.removeEventListener('resize', onResize);
            renderer.domElement.removeEventListener('pointermove', onPointerMove);
            renderer.domElement.removeEventListener('click', onClick);
            labelRenderer.domElement.remove();
            renderer.dispose();
            canvasHost.innerHTML = '';
        },
        focusCourt(courtId) {
            const entry = [...courtsByMesh.entries()].find(([, court]) => court.id === courtId);
            if (entry) {
                setSelectedCourt(entry[0]);
            }
        },
        resetView,
    };
}

window.focusBookingCourt = focusBookingCourt;

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('court-viewer-root');
    const configEl = document.getElementById('court-viewer-config');

    if (!root || !configEl) {
        return;
    }

    let config = {};
    try {
        config = JSON.parse(configEl.textContent);
    } catch (error) {
        console.error('Invalid court viewer config', error);
        return;
    }

    window.smashzoneCourtViewer = initCourtViewer(root, config);

    document.getElementById('court-viewer-reset')?.addEventListener('click', () => {
        window.smashzoneCourtViewer?.resetView?.();
    });

    const toggleBtn = document.getElementById('court-viewer-toggle');
    const viewerPanel = document.getElementById('court-viewer-panel');
    const tableSection = document.getElementById('booking-table-section');

    if (toggleBtn && viewerPanel && tableSection) {
        toggleBtn.addEventListener('click', () => {
            const showing3d = !viewerPanel.classList.contains('hidden');
            viewerPanel.classList.toggle('hidden', showing3d);
            tableSection.classList.toggle('hidden', !showing3d);
            toggleBtn.textContent = showing3d ? 'Show 3D View' : 'Show Table View';

            if (!showing3d) {
                window.dispatchEvent(new Event('resize'));
            }
        });
    }
});
