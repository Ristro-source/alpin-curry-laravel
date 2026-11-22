import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import { mergeGeometries } from 'three/addons/utils/BufferGeometryUtils.js';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';

const $ = (s) => document.querySelector(s);
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

function makeCanvasTexture(w, h, draw) {
  const c = document.createElement('canvas');
  c.width = w; c.height = h;
  draw(c.getContext('2d'), w, h);
  const t = new THREE.CanvasTexture(c);
  t.colorSpace = THREE.SRGBColorSpace;
  return t;
}

const radial = (stops) => makeCanvasTexture(256, 256, (g, w) => {
  const r = g.createRadialGradient(w / 2, w / 2, 0, w / 2, w / 2, w / 2);
  stops.forEach(([o, c]) => r.addColorStop(o, c));
  g.fillStyle = r; g.fillRect(0, 0, w, w);
});

function wrapText(g, text, maxW) {
  const lines = [];
  let cur = '';
  for (const w of text.split(' ')) {
    const t = cur ? `${cur} ${w}` : w;
    if (g.measureText(t).width > maxW && cur) { lines.push(cur); cur = w; } else cur = t;
  }
  lines.push(cur);
  return lines;
}

function medallion(name) {
  return makeCanvasTexture(512, 512, (g, s) => {
    const c = s / 2;
    const gold = g.createLinearGradient(0, 0, s, s);
    gold.addColorStop(0, '#f6d58a'); gold.addColorStop(0.5, '#d49a3c'); gold.addColorStop(1, '#a8702a');
    const bowl = g.createRadialGradient(c, c * 0.8, 10, c, c, 240);
    bowl.addColorStop(0, '#4a3220'); bowl.addColorStop(0.7, '#24170f'); bowl.addColorStop(1, '#140d08');
    g.beginPath(); g.arc(c, c, 236, 0, Math.PI * 2); g.fillStyle = bowl; g.fill();
    g.lineWidth = 14; g.strokeStyle = gold; g.stroke();
    g.beginPath(); g.arc(c, c, 212, 0, Math.PI * 2); g.lineWidth = 2; g.stroke();
    g.save(); g.translate(c, c); g.globalAlpha = 0.5;
    for (let i = 0; i < 24; i++) {
      g.rotate(Math.PI / 12);
      g.beginPath(); g.moveTo(180, 0); g.quadraticCurveTo(193, 7, 206, 0); g.quadraticCurveTo(193, -7, 180, 0);
      g.fillStyle = gold; g.fill();
    }
    g.restore();
    g.fillStyle = gold; g.textAlign = 'center'; g.textBaseline = 'middle';
    let size = 64;
    let lines;
    do {
      g.font = `800 ${size}px Eczar, serif`;
      lines = wrapText(g, name.toUpperCase(), 300);
      size -= 4;
    } while ((lines.length > 3 || lines.some((l) => g.measureText(l).width > 320)) && size > 28);
    const lh = size * 1.18;
    lines.forEach((l, i) => g.fillText(l, c, c + (i - (lines.length - 1) / 2) * lh + 4));
  });
}

function petal(r1, r2, w) {
  const m = (r1 + r2) / 2;
  const s = new THREE.Shape();
  s.moveTo(r1, 0); s.quadraticCurveTo(m, w, r2, 0); s.quadraticCurveTo(m, -w, r1, 0);
  return s;
}

function petalOutline(r1, r2, w, t) {
  const s = petal(r1, r2, w);
  const a = r1 + t * 1.6, b = r2 - t * 1.6, m = (a + b) / 2, ww = Math.max(w - t * 1.4, 0.3);
  const h = new THREE.Path();
  h.moveTo(a, 0); h.quadraticCurveTo(m, ww, b, 0); h.quadraticCurveTo(m, -ww, a, 0);
  s.holes.push(h);
  return s;
}

function ring(r, t) {
  const s = new THREE.Shape(); s.absarc(0, 0, r + t / 2, 0, Math.PI * 2, false);
  const h = new THREE.Path(); h.absarc(0, 0, r - t / 2, 0, Math.PI * 2, true);
  s.holes.push(h);
  return s;
}

function disc(x, y, r) {
  const s = new THREE.Shape(); s.absarc(x, y, r, 0, Math.PI * 2, false);
  return s;
}

function scallopBand(r, bump, n, t) {
  const pts = (off) => Array.from({ length: n * 10 }, (_, i) => {
    const a = (i / (n * 10)) * Math.PI * 2;
    const rr = r + bump * Math.sin(Math.PI * ((i % 10) / 10)) + off;
    return new THREE.Vector2(Math.cos(a) * rr, Math.sin(a) * rr);
  });
  const s = new THREE.Shape(pts(t / 2));
  s.holes.push(new THREE.Path(pts(-t / 2).reverse()));
  return s;
}

function extrude(shapes, depth) {
  return mergeGeometries(shapes.map((sh) => new THREE.ExtrudeGeometry(sh,
    { depth, bevelEnabled: true, bevelThickness: depth * 0.4, bevelSize: 0.22, bevelSegments: 2, curveSegments: 14 })));
}

function rot(shapeFn, count, offset = 0) {
  return Array.from({ length: count }, (_, i) => {
    const a = ((i * 360) / count + offset) * (Math.PI / 180);
    const ca = Math.cos(a), sa = Math.sin(a);
    const xf = (p) => new THREE.Vector2(p.x * ca - p.y * sa, p.x * sa + p.y * ca);
    const pts = shapeFn().extractPoints(14);
    const out = new THREE.Shape(pts.shape.map(xf));
    pts.holes.forEach((h) => out.holes.push(new THREE.Path(h.map(xf))));
    return out;
  });
}

function buildMandala(goldMat, lineMat) {
  const T0 = 0.9;
  const dots = (r, rad, list) => list.map((a) => disc(r * Math.cos(a * Math.PI / 180), r * Math.sin(a * Math.PI / 180), rad));
  const layers = [
    [[disc(0, 0, 7)], [ring(11, T0)]],
    [rot(() => petal(15, 26, 2.2), 12), rot(() => petalOutline(12, 30, 6, T0), 12)],
    [dots(37, 1.5, Array.from({ length: 24 }, (_, i) => i * 15)), [ring(33, T0)]],
    [rot(() => petal(44, 58, 3.2), 16, 11.25), rot(() => petalOutline(40, 64, 9, T0), 16, 11.25)],
    [[], [scallopBand(68, 4.2, 32, T0)]],
    [rot(() => petal(76, 87, 1.4), 24), rot(() => petalOutline(72, 92, 4.6, T0), 24)],
    [dots(97, 1.2, Array.from({ length: 48 }, (_, i) => i * 7.5 + 3.75)), []],
  ];
  const group = new THREE.Group();
  const parts = layers.map(([fill, line], i) => {
    const g = new THREE.Group();
    if (fill.length) g.add(new THREE.Mesh(extrude(fill, 1.6), goldMat));
    if (line.length) g.add(new THREE.Mesh(extrude(line, 0.9), lineMat));
    g.userData.delay = i * 0.13;
    group.add(g);
    return g;
  });
  return { group, parts };
}

export function createScene({ isOpen, onActive }) {
  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({ canvas: $('#scene'), antialias: true, powerPreference: 'high-performance' });
  } catch {
    document.documentElement.classList.add('nogl');
    return null;
  }
  const small = matchMedia('(max-width: 999px)');
  renderer.setPixelRatio(Math.min(devicePixelRatio, small.matches ? 1.75 : 2));
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.05;

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0x120d0a);
  scene.fog = new THREE.Fog(0x120d0a, 18, 60);
  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
  scene.environmentIntensity = 0.28;

  const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 120);
  const TAN = 2 * Math.tan(THREE.MathUtils.degToRad(19));
  const MC = new THREE.Vector3(0, 0.7, -5.5);

  scene.add(new THREE.HemisphereLight(0x8a5a30, 0x0a0604, 0.9));
  const key = new THREE.SpotLight(0xffd7a0, 80, 30, 0.5, 0.7, 1.6);
  key.position.set(0, 9, 6);
  scene.add(key, key.target);
  const lantern = new THREE.PointLight(0xff9d45, 26, 0, 1.6);
  scene.add(lantern);

  const glowTex = radial([[0, 'rgba(255,190,110,1)'], [0.25, 'rgba(255,150,60,.45)'], [1, 'rgba(255,120,40,0)']]);
  const lanternGlow = new THREE.Sprite(new THREE.SpriteMaterial({ map: glowTex, blending: THREE.AdditiveBlending, depthWrite: false, transparent: true, opacity: 0 }));
  lanternGlow.scale.setScalar(0.9);
  scene.add(lanternGlow);

  const goldMat = new THREE.MeshStandardMaterial({ color: 0xe8b153, metalness: 1, roughness: 0.27 });
  const lineMat = new THREE.MeshStandardMaterial({ color: 0xc8923a, metalness: 1, roughness: 0.38 });
  const mandala = buildMandala(goldMat, lineMat);
  const pivot = new THREE.Group();
  pivot.position.copy(MC);
  mandala.group.scale.setScalar(0.056);
  pivot.add(mandala.group);
  scene.add(pivot);

  const halo = new THREE.Sprite(new THREE.SpriteMaterial({ map: glowTex, blending: THREE.AdditiveBlending, depthWrite: false, transparent: true, opacity: 0, color: 0xff8a3a }));
  halo.scale.setScalar(15);
  halo.position.set(MC.x, MC.y, MC.z - 0.7);
  scene.add(halo);

  const shadowTex = radial([[0, 'rgba(0,0,0,.85)'], [0.55, 'rgba(0,0,0,.35)'], [1, 'rgba(0,0,0,0)']]);
  const spotRing = new THREE.Mesh(new THREE.RingGeometry(1.45, 1.52, 96), new THREE.MeshBasicMaterial({ color: 0xf0b24f, transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false }));
  spotRing.rotation.x = -Math.PI / 2;
  spotRing.position.set(0, -1.53, 1.2);
  const spotPool = new THREE.Mesh(new THREE.PlaneGeometry(5, 5), new THREE.MeshBasicMaterial({ map: radial([[0, 'rgba(255,170,80,.5)'], [1, 'rgba(255,140,50,0)']]), transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false }));
  spotPool.rotation.x = -Math.PI / 2;
  spotPool.position.set(0, -1.54, 1.2);
  scene.add(spotRing, spotPool);

  const EMBERS = small.matches ? 160 : 320;
  const ePos = new Float32Array(EMBERS * 3);
  const eCol = new Float32Array(EMBERS * 3);
  const eVel = new Float32Array(EMBERS);
  const palette = [new THREE.Color(0xffc26a), new THREE.Color(0xff8a3a), new THREE.Color(0xf6d58a)];
  for (let i = 0; i < EMBERS; i++) {
    ePos.set([(Math.random() - 0.5) * 22, -2 + Math.random() * 11, -9 + Math.random() * 12], i * 3);
    palette[i % 3].toArray(eCol, i * 3);
    eVel[i] = 0.12 + Math.random() * 0.35;
  }
  const eGeo = new THREE.BufferGeometry();
  eGeo.setAttribute('position', new THREE.BufferAttribute(ePos, 3));
  eGeo.setAttribute('color', new THREE.BufferAttribute(eCol, 3));
  scene.add(new THREE.Points(eGeo, new THREE.PointsMaterial({
    size: 0.08, map: radial([[0, 'rgba(255,255,255,1)'], [0.3, 'rgba(255,255,255,.6)'], [1, 'rgba(255,255,255,0)']]),
    vertexColors: true, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending, opacity: 0.85,
  })));

  const composer = new EffectComposer(renderer);
  composer.addPass(new RenderPass(scene, camera));
  const bloom = new UnrealBloomPass(new THREE.Vector2(1, 1), 0.34, 0.5, 1.0);
  composer.addPass(bloom);
  composer.addPass(new OutputPass());

  const loader = new THREE.TextureLoader();
  const texCache = new Map();
  const loadTex = (d) => {
    const k = d.img || `m:${d.name}`;
    if (!texCache.has(k)) {
      texCache.set(k, d.img
        ? loader.loadAsync(d.img).then((t) => { t.colorSpace = THREE.SRGBColorSpace; t.anisotropy = 4; return t; })
        : Promise.resolve(medallion(d.name)));
    }
    return texCache.get(k);
  };

  const plane = new THREE.PlaneGeometry(1, 1);
  const STEP = 0.52, R = small.matches ? 5.6 : 7.2;
  let dishes = [];
  let current = null;
  let pos = 0;
  let target = 0;
  let clock = 0;
  let v = 0;
  const introStart = reduce ? -10 : 0.1;

  async function open(c, i) {
    current = c;
    dishes.forEach((d) => { if (d.dying === null) d.dying = clock; });
    pos = target = i;
    const born = clock + 0.35;
    const texes = await Promise.all(c.items.map(loadTex));
    if (current !== c) return;
    const n = c.items.length;
    dishes = dishes.concat(c.items.map((item, k) => {
      const tex = texes[k];
      const aspect = tex.image.width / tex.image.height;
      const w = item.img ? Math.min(3.5, 2.55 * aspect) : 2.3;
      const h = item.img ? w / aspect : 2.3;
      const mesh = new THREE.Mesh(plane, new THREE.MeshBasicMaterial({ map: tex, transparent: true, alphaTest: 0.02 }));
      mesh.userData.index = k;
      const shadow = new THREE.Mesh(plane, new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, depthWrite: false }));
      shadow.rotation.x = -Math.PI / 2;
      scene.add(mesh, shadow);
      const off = Math.abs(((((k - i) % n) + n + n / 2) % n) - n / 2);
      return { mesh, shadow, w, h, n, born: born + off * 0.08, dying: null, seed: Math.random() * 10, lastRel: 0 };
    }));
    layout();
  }

  function close() {
    current = null;
    dishes.forEach((d) => { if (d.dying === null) d.dying = clock; });
  }

  function goTo(idx) {
    const n = current?.items.length || 1;
    target = idx + Math.round((target - idx) / n) * n;
  }

  const pointer = new THREE.Vector2(0.35, 0.2);
  const pointerSmooth = new THREE.Vector2(0.35, 0.2);
  let mouse = false;
  addEventListener('pointermove', (e) => {
    if (e.pointerType !== 'mouse') return;
    mouse = true;
    pointer.set((e.clientX / innerWidth) * 2 - 1, -(e.clientY / innerHeight) * 2 + 1);
  }, { passive: true });

  const el = renderer.domElement;
  const ray = new THREE.Raycaster();
  let drag = null;
  el.addEventListener('pointerdown', (e) => {
    if (!isOpen()) return;
    el.setPointerCapture(e.pointerId);
    el.classList.add('drag');
    drag = { x: e.clientX, start: target, moved: false, v: 0, lx: e.clientX, lt: e.timeStamp };
  });
  el.addEventListener('pointermove', (e) => {
    if (!drag) return;
    const dx = e.clientX - drag.x;
    drag.moved = drag.moved || Math.abs(dx) > 6;
    target = drag.start - dx / Math.min(260, innerWidth * 0.32);
    pos = target;
    drag.v = (e.clientX - drag.lx) / Math.max(1, e.timeStamp - drag.lt);
    drag.lx = e.clientX; drag.lt = e.timeStamp;
  });
  const end = (e) => {
    if (!drag || !current) { drag = null; return; }
    el.classList.remove('drag');
    const n = current.items.length;
    if (drag.moved) {
      target = Math.round(target + Math.max(-3, Math.min(3, -drag.v * 2.2)));
    } else {
      ray.setFromCamera(new THREE.Vector2((e.clientX / el.clientWidth) * 2 - 1, -(e.clientY / el.clientHeight) * 2 + 1), camera);
      const hit = ray.intersectObjects(dishes.filter((d) => d.dying === null && d.mesh.visible).map((d) => d.mesh))[0];
      if (hit) {
        const r = Math.round(target);
        target = r + ((((hit.object.userData.index - r) % n) + n + Math.floor(n / 2)) % n) - Math.floor(n / 2);
      }
    }
    onActive(((target % n) + n) % n);
    drag = null;
  };
  el.addEventListener('pointerup', end);
  el.addEventListener('pointercancel', end);
  let wheelAcc = 0;
  el.addEventListener('wheel', (e) => {
    if (!isOpen() || !current) return;
    e.preventDefault();
    wheelAcc += Math.abs(e.deltaX) > Math.abs(e.deltaY) ? e.deltaX : e.deltaY;
    if (Math.abs(wheelAcc) < 60) return;
    const n = current.items.length;
    target = Math.round(target) + Math.sign(wheelAcc);
    wheelAcc = 0;
    onActive(((target % n) + n) % n);
  }, { passive: false });

  const cam = { heroD: 20, viewD: 10, sx: 0, sy: 0 };
  function layout() {
    const w = el.clientWidth, h = el.clientHeight, aspect = w / h;
    camera.aspect = aspect;
    cam.heroD = 10.8 / (TAN * Math.min(1, aspect) * (aspect < 1 ? 1.35 : 0.95));
    if (isOpen()) {
      const top = $('.v-top').getBoundingClientRect().bottom;
      const card = $('.card').getBoundingClientRect();
      const wide = !small.matches;
      const left = wide ? card.right : 0;
      const bottom = wide ? h - 16 : card.top - 4;
      const sw = Math.max(120, w - left), sh = Math.max(120, bottom - top);
      cam.viewD = Math.max(7.5, (5.4 * h) / (TAN * sw), (3.9 * h) / (TAN * sh)) + 1.2;
      cam.sx = left + sw / 2 - w / 2;
      cam.sy = top + sh / 2 - h / 2;
      document.documentElement.style.setProperty('--stage-mid', `${top + sh / 2}px`);
      document.documentElement.style.setProperty('--stage-left', `${left}px`);
    }
  }
  let size = '';
  function resize() {
    const s = `${el.clientWidth}x${el.clientHeight}`;
    if (s !== size) {
      size = s;
      renderer.setSize(el.clientWidth, el.clientHeight, false);
      composer.setSize(el.clientWidth, el.clientHeight);
    }
    layout();
  }
  addEventListener('resize', resize);
  new ResizeObserver(layout).observe($('.card'));
  resize();

  let spin = 0;
  let lastY = scrollY;
  addEventListener('scroll', () => { spin += (scrollY - lastY) * 0.0015; lastY = scrollY; }, { passive: true });

  const ease = (x) => 1 - Math.pow(1 - Math.min(Math.max(x, 0), 1), 3);
  const easeBack = (x) => { const t = Math.min(Math.max(x, 0), 1) - 1; return 1 + 2.2 * t * t * t + 1.2 * t * t; };
  const tmp = new THREE.Vector3();
  const heroPos = new THREE.Vector3(), viewPos = new THREE.Vector3(), heroLook = new THREE.Vector3(), viewLook = new THREE.Vector3();
  const lookAt = new THREE.Vector3();
  let last = performance.now();

  function frame(now) {
    requestAnimationFrame(frame);
    const dt = Math.min(0.05, (now - last) / 1000);
    last = now;
    clock += dt;
    const open = isOpen();
    v += ((open ? 1 : 0) - v) * (1 - Math.exp(-dt * 3.5));
    const sp = Math.min(1.5, scrollY / innerHeight);
    if (!open && v < 0.01 && sp > 1.02) return;

    const intro = ease((clock - introStart) / 2.6);
    const ev = ease(v);
    if (!mouse) pointer.set(Math.sin(clock * 0.35) * 0.55, 0.15 + Math.sin(clock * 0.5) * 0.3);
    pointerSmooth.lerp(pointer, 1 - Math.exp(-dt * 4));

    heroPos.set(pointerSmooth.x * 0.7, MC.y + pointerSmooth.y * 0.5 - sp * 2.2, MC.z + cam.heroD + (1 - intro) * 10 + sp * 3);
    heroLook.set(0, MC.y - sp * 1.2, MC.z);
    viewPos.set(pointerSmooth.x * 0.45, 1.0 + pointerSmooth.y * 0.25, cam.viewD);
    viewLook.set(0, 0.15, 0);
    camera.position.lerpVectors(heroPos, viewPos, ev);
    lookAt.lerpVectors(heroLook, viewLook, ev);
    camera.lookAt(lookAt);
    camera.setViewOffset(el.clientWidth, el.clientHeight, -cam.sx * ev, -cam.sy * ev, el.clientWidth, el.clientHeight);
    camera.updateProjectionMatrix();

    lantern.position.set(pointerSmooth.x * 5.5, THREE.MathUtils.lerp(MC.y, 0.4, ev) + pointerSmooth.y * 3, THREE.MathUtils.lerp(-2.2, 2.2, ev));
    lantern.intensity = 24 + Math.sin(clock * 7.3) * 1.6 + Math.sin(clock * 13.1) * 1.1;
    lanternGlow.position.copy(lantern.position);
    lanternGlow.material.opacity = mouse ? 0.45 + Math.sin(clock * 9) * 0.04 : 0;

    mandala.group.rotation.z += dt * (reduce ? 0 : 0.035) + spin;
    spin *= 0.9;
    pivot.rotation.x = THREE.MathUtils.lerp(pivot.rotation.x, -pointerSmooth.y * 0.16, 0.08);
    pivot.rotation.y = THREE.MathUtils.lerp(pivot.rotation.y, pointerSmooth.x * 0.22, 0.08);
    mandala.parts.forEach((p) => {
      const k = easeBack((clock - introStart - p.userData.delay) / 1.1);
      p.scale.setScalar(Math.max(0.001, k));
      p.rotation.z = (1 - Math.min(k, 1)) * 0.9;
    });
    halo.material.opacity = 0.16 * intro + Math.sin(clock * 1.3) * 0.02;

    if (!drag) pos += (target - pos) * (1 - Math.exp(-dt * 7));
    dishes = dishes.filter((d) => {
      const { n } = d;
      const rel = d.dying === null ? ((((d.mesh.userData.index - pos) % n) + n + n / 2) % n) - n / 2 : d.lastRel;
      d.lastRel = rel;
      const appear = ease((clock - d.born) / 0.8);
      const gone = d.dying === null ? 0 : ease((clock - d.dying) / 0.45);
      if (gone >= 1) {
        scene.remove(d.mesh, d.shadow);
        d.mesh.material.dispose(); d.shadow.material.dispose();
        return false;
      }
      const ar = Math.abs(rel);
      const edge = d.dying === null ? Math.min(1, Math.max(0, (n / 2 - ar) * 2)) : 1;
      const vis = Math.max(0, Math.min(1, 3.4 - ar)) * edge * appear * (1 - gone);
      const focus = Math.max(0, 1 - ar);
      const scale = (0.62 + 0.38 * focus) * (0.85 + 0.15 * appear);
      const a = rel * STEP;
      const x = Math.sin(a) * R;
      const z = (Math.cos(a) - 1) * R * 0.9 + 1.2;
      const y = -1.5 + (d.h * scale) / 2 + 0.12 + Math.sin(clock * 1.2 + d.seed) * 0.05 - (1 - appear) * 1.2 + gone * 1.4;
      d.mesh.position.set(x, y, z);
      d.mesh.scale.set(d.w * scale, d.h * scale, 1);
      tmp.copy(camera.position); tmp.y = y;
      d.mesh.lookAt(tmp);
      d.mesh.rotation.z += Math.sin(clock * 0.8 + d.seed) * 0.012;
      d.mesh.material.opacity = vis;
      d.mesh.material.color.setScalar(0.5 + 0.42 * focus);
      d.mesh.visible = vis > 0.01;
      d.mesh.renderOrder = Math.round(10 - ar);
      d.shadow.position.set(x, -1.545, z);
      d.shadow.scale.set(d.w * scale * 0.95, d.w * scale * 0.4, 1);
      d.shadow.material.opacity = vis * 0.9;
      d.shadow.visible = d.mesh.visible;
      return true;
    });

    const settled = Math.max(0, 1 - Math.abs(target - pos) * 2);
    spotRing.scale.setScalar((1 + Math.sin(clock * 2) * 0.025) * (0.8 + 0.2 * settled));
    spotRing.material.opacity = 0.55 * settled * ev;
    spotPool.material.opacity = 0.6 * ev;

    const p = eGeo.attributes.position.array;
    for (let i = 0; i < EMBERS; i++) {
      p[i * 3 + 1] += eVel[i] * dt;
      p[i * 3] += Math.sin(clock * 0.6 + i) * dt * 0.08;
      if (p[i * 3 + 1] > 9) p[i * 3 + 1] = -2;
    }
    eGeo.attributes.position.needsUpdate = true;
    composer.render(dt);
  }
  requestAnimationFrame((t) => { last = t; frame(t); });

  return { open, close, goTo, layout };
}

