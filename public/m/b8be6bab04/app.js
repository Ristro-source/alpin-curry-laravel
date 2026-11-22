import MENU from './menu.js';

const $ = (s, el = document) => el.querySelector(s);
const $$ = (s, el = document) => [...el.querySelectorAll(s)];
const LANGS = ['it', 'en', 'de'];
const LI = { it: 0, en: 1, de: 2 };
const COURSES = ['drink', 'starter', 'main', 'side', 'dessert'];
const T = {
  it: {
    journey: 'Un viaggio nei sapori autentici dell’India', explore: 'Sfoglia il menu', view3d: 'Esplora in 3D', open3d: 'Guarda in 3D',
    build: 'Componi il tuo menu', pair: 'Abbinamento', al: 'Allergeni', ask: 'chiedere al personale', legend: 'Allergeni', spicyLong: 'piccante',
    note: 'Per allergie o intolleranze rivolgersi al personale.', back: '← Torna al sito', swipe: 'scorri ← →', drinks: 'Bevande',
    photo: 'Le foto sono illustrative: il piatto servito può essere diverso nell’aspetto.', story: 'La storia', veg: 'Vegetariano',
    pick: 'Consigliato', add: 'Aggiungi', added: 'Aggiunto', glass: 'calice', bottle: 'bottiglia',
    courses: ['Bevanda', 'Antipasto', 'Piatto principale', 'Pane & riso', 'Dolce'],
    sel: 'La mia selezione', empty: 'La selezione è vuota. Tocca + su un piatto per aggiungerlo.',
    waiter: 'Mostra al cameriere', clear: 'Svuota', notOrder: 'Questa lista non è un ordine: mostrala al cameriere quando ordini.',
    addCourse: 'Aggiungi', next: 'Avanti', skip: 'Salta', prev: 'Indietro', done: 'Fatto', picks: 'Consigliati dallo chef',
    forWaiter: 'Per il cameriere', close: 'Chiudi', step: (a, b) => `Passo ${a} di ${b}`,
    sugPair: (n, w) => `${n} è perfetto con ${w}.`, sugBread: 'Un naan caldo dal tandoor per la salsa?', sugDrink: 'Qualcosa da bere per iniziare?',
    sugDessert: 'Spazio per un dolce?', items: (n) => (n === 1 ? '1 piatto' : `${n} piatti`),
    credits: 'Crediti foto',
    combos: 'Le nostre combinazioni', combosSub: 'Scegli per chi è la serata: noi pensiamo al resto.', addAll: 'Aggiungi tutto', mild: 'Senza piccante',
    comboAdded: (n) => `«${n}» è nella tua selezione.`, seeSel: 'Vedi selezione', orCombo: 'oppure scegli una combinazione pronta →', browse: 'Sfoglia il menu',
    guideIntro: ['Iniziamo da bere', 'Per cominciare', 'Il piatto forte', 'Pane e riso per la salsa', 'Il finale dolce'],
  },
  en: {
    journey: 'A journey through authentic Indian flavours', explore: 'Browse the menu', view3d: 'Explore in 3D', open3d: 'View in 3D',
    build: 'Build your meal', pair: 'Pairs with', al: 'Allergens', ask: 'please ask our staff', legend: 'Allergens', spicyLong: 'spicy',
    note: 'Please inform our staff of any allergies or intolerances.', back: '← Back to website', swipe: 'swipe ← →', drinks: 'Drinks',
    photo: 'Photos are for illustration only: the dish served may look different.', story: 'The story', veg: 'Vegetarian',
    pick: 'Recommended', add: 'Add', added: 'Added', glass: 'glass', bottle: 'bottle',
    courses: ['Drink', 'Starter', 'Main course', 'Bread & rice', 'Dessert'],
    sel: 'My selection', empty: 'Your selection is empty. Tap + on a dish to add it.',
    waiter: 'Show to the waiter', clear: 'Clear', notOrder: 'This list is not an order: show it to your waiter when you order.',
    addCourse: 'Add', next: 'Next', skip: 'Skip', prev: 'Back', done: 'Done', picks: 'Chef’s recommendations',
    forWaiter: 'For the waiter', close: 'Close', step: (a, b) => `Step ${a} of ${b}`,
    sugPair: (n, w) => `${n} is perfect with ${w}.`, sugBread: 'A warm naan from the tandoor for the sauce?', sugDrink: 'Something to drink to start?',
    sugDessert: 'Room for dessert?', items: (n) => (n === 1 ? '1 item' : `${n} items`),
    credits: 'Photo credits',
    combos: 'Our combinations', combosSub: 'Choose who the evening is for: we take care of the rest.', addAll: 'Add all', mild: 'No chilli',
    comboAdded: (n) => `“${n}” is in your selection.`, seeSel: 'See selection', orCombo: 'or pick a ready-made combination →', browse: 'Browse the menu',
    guideIntro: ['Let’s start with a drink', 'To begin', 'The main event', 'Bread and rice for the sauce', 'A sweet finish'],
  },
  de: {
    journey: 'Eine Reise durch authentische indische Aromen', explore: 'Speisekarte ansehen', view3d: 'In 3D entdecken', open3d: 'In 3D ansehen',
    build: 'Menü zusammenstellen', pair: 'Passt zu', al: 'Allergene', ask: 'bitte Personal fragen', legend: 'Allergene', spicyLong: 'scharf',
    note: 'Bitte informieren Sie unser Personal über Allergien oder Unverträglichkeiten.', back: '← Zur Website', swipe: 'wischen ← →', drinks: 'Getränke',
    photo: 'Die Fotos dienen nur zur Illustration: Das servierte Gericht kann anders aussehen.', story: 'Die Geschichte', veg: 'Vegetarisch',
    pick: 'Empfehlung', add: 'Hinzufügen', added: 'Hinzugefügt', glass: 'Glas', bottle: 'Flasche',
    courses: ['Getränk', 'Vorspeise', 'Hauptgericht', 'Brot & Reis', 'Dessert'],
    sel: 'Meine Auswahl', empty: 'Ihre Auswahl ist leer. Tippen Sie bei einem Gericht auf +.',
    waiter: 'Dem Kellner zeigen', clear: 'Leeren', notOrder: 'Diese Liste ist keine Bestellung: Zeigen Sie sie beim Bestellen dem Kellner.',
    addCourse: 'Hinzufügen', next: 'Weiter', skip: 'Überspringen', prev: 'Zurück', done: 'Fertig', picks: 'Empfehlungen des Küchenchefs',
    forWaiter: 'Für den Kellner', close: 'Schließen', step: (a, b) => `Schritt ${a} von ${b}`,
    sugPair: (n, w) => `${n} passt perfekt zu ${w}.`, sugBread: 'Ein warmes Naan aus dem Tandoor für die Sauce?', sugDrink: 'Etwas zu trinken zum Start?',
    sugDessert: 'Noch Platz für ein Dessert?', items: (n) => (n === 1 ? '1 Artikel' : `${n} Artikel`),
    credits: 'Fotonachweis',
    combos: 'Unsere Kombinationen', combosSub: 'Wählen Sie, für wen der Abend ist: Wir kümmern uns um den Rest.', addAll: 'Alles hinzufügen', mild: 'Ohne Schärfe',
    comboAdded: (n) => `„${n}“ ist in Ihrer Auswahl.`, seeSel: 'Auswahl ansehen', orCombo: 'oder eine fertige Kombination wählen →', browse: 'Speisekarte ansehen',
    guideIntro: ['Zuerst etwas zu trinken', 'Zum Start', 'Das Hauptgericht', 'Brot und Reis für die Sauce', 'Das süße Finale'],
  },
};
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
const store = {
  get: (k) => { try { return localStorage.getItem(k); } catch { return null; } },
  set: (k, v) => { try { localStorage.setItem(k, v); } catch {} },
};
const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
const svg = (name) => `<svg class="ico" viewBox="0 0 24 24">${MENU.icons[name]}</svg>`;
const MINI = '<svg><use href="#mandala"/></svg>';
const CUBE = '<svg class="ico" viewBox="0 0 24 24"><path d="M12 2.5l8.5 4.8v9.4L12 21.5l-8.5-4.8V7.3z"/><path d="M3.5 7.3L12 12l8.5-4.7M12 12v9.5"/></svg>';
const LEAF = '<svg class="leaf" viewBox="0 0 24 24"><path d="M5 19c0-8 5-13 15-14-1 10-6 15-14 15"/><path d="M5 19l8-8"/></svg>';
const WHO_ICON = {
  solo: '<circle cx="12" cy="7" r="3.2"/><path d="M5.5 20.5a6.5 6.5 0 0 1 13 0"/>',
  couple: '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
  friends: '<circle cx="8" cy="8" r="2.8"/><circle cx="16.5" cy="8" r="2.8"/><path d="M2.5 20a5.5 5.5 0 0 1 11 0M11 20a5.5 5.5 0 0 1 11 0"/>',
  family: '<circle cx="8" cy="6.5" r="2.8"/><path d="M3 20.5a5 5 0 0 1 10 0"/><circle cx="17" cy="11" r="2.1"/><path d="M13.5 20.5a3.5 3.5 0 0 1 7 0"/>',
};
let who = 'solo';
const money = (p) => `${p.toFixed(2).replace(/\.00$/, '')} €`;

let lang = store.get('ac-lang') || (navigator.language || 'it').slice(0, 2);
if (!LANGS.includes(lang)) lang = 'en';
const t = () => T[lang];
const view = { open: false, cat: 0, active: 0 };
let scene3d = null;

const ITEMS = {};
MENU.categories.forEach((c, ci) => c.items.forEach((d, i) => {
  if (c.id !== 'drinks') ITEMS[d.id] = { name: d.name, p: d.p, course: d.course, pair: d.pair, ci, i, img: d.img };
}));
MENU.drinks.forEach((s) => s.rows.forEach((r) => r.opts.forEach((o) => {
  ITEMS[o.id] = { name: r.name, label: o.l, p: o.p, course: 'drink' };
})));
const optLabel = (l) => (l === 'glass' || l === 'bottle' ? t()[l] : l);
const itemName = (id) => { const it = ITEMS[id]; return it.label ? `${it.name} · ${optLabel(it.label)}` : it.name; };

const sel = new Map();
try {
  const saved = JSON.parse(store.get('ac-sel') || 'null');
  if (saved && Date.now() - saved.at < 6 * 3600e3) saved.items.forEach(([id, q]) => { if (ITEMS[id] && q > 0) sel.set(id, q); });
} catch {}
const saveSel = () => store.set('ac-sel', JSON.stringify({ at: Date.now(), items: [...sel] }));

$('#defs').innerHTML = MENU.mandala;
$('#defs').hidden = false;

const algName = (n) => esc(MENU.allergens[n][LI[lang]]);
const algIcons = (list) => list.map((n) => `<img src="assets/allergens/${n}.png" alt="${algName(n)}" title="${algName(n)}">`).join('');
const region = (d) => (d.story ? MENU.regions[MENU.stories[d.story].r][LI[lang]] : '');
const kind = (d) => (d.story ? MENU.kinds[MENU.stories[d.story].k][LI[lang]] : '');

function stepper(id, compact = false) {
  const q = sel.get(id) || 0;
  if (!q) return `<button class="add${compact ? ' sm' : ''}" data-add="${id}" aria-label="${t().add}">+</button>`;
  return `<span class="qty${compact ? ' sm' : ''}"><button data-dec="${id}" aria-label="−">−</button><b>${q}</b><button data-add="${id}" aria-label="+">+</button></span>`;
}

function tags(d) {
  return [d.pick ? `<span class="tag pick">★ ${t().pick}</span>` : '', d.veg ? `<span class="tag veg">${LEAF}${t().veg}</span>` : '',
    d.story ? `<span class="tag reg">${esc(region(d))}</span>` : ''].join('');
}

function dishCard(d, ci, i) {
  const thumb = d.img
    ? `<span class="thumb"><img src="${d.img}" alt="" width="92" height="92" loading="lazy" decoding="async"><span class="v3d">3D</span></span>`
    : `<span class="thumb ph">${MINI}</span>`;
  const hot = d.hot ? `<span class="hot" title="${t().spicyLong}">${'<img src="assets/chilli.png" alt="">'.repeat(d.hot)}</span>` : '';
  const algs = d.al === null ? `<span class="algs"><span class="q">${t().al}: ${t().ask}</span></span>` : `<span class="algs">${algIcons(d.al)}</span>`;
  const pair = d.pair ? `<span class="pair">${svg('wine')}${esc(d.pair)}</span>` : '';
  const desc = d.desc[lang] || d.desc.en;
  return `<article class="dc" data-c="${ci}" data-i="${i}" tabindex="0" aria-label="${esc(d.name)} – ${t().open3d}">${thumb}
    <div class="info"><div class="row"><h3>${esc(d.name)}${hot}</h3><span class="dots"></span><span class="price">${esc(d.price)}</span></div>
    <div class="tags">${tags(d)}</div>
    ${desc ? `<p class="desc">${esc(desc)}</p>` : ''}${d.note ? `<p class="note">${esc(d.note)}</p>` : ''}
    <div class="meta">${pair}${algs}<span class="slot" data-slot="${d.id}">${stepper(d.id)}</span></div></div></article>`;
}

function optButtons(r) {
  return r.opts.map((o) => {
    const q = sel.get(o.id) || 0;
    const lbl = o.l ? `<small>${esc(optLabel(o.l))}</small>` : '';
    return `<button class="opt${q ? ' on' : ''}" data-add="${o.id}" aria-label="${esc(r.name)} ${esc(optLabel(o.l))} ${money(o.p)} – ${t().add}">${lbl}${money(o.p)}${q ? `<i>${q}</i>` : '<i>+</i>'}</button>`;
  }).join('');
}

function drinksBlock(ci) {
  const pics = MENU.categories[ci].items.map((d, i) =>
    `<button class="dc-pic" data-c="${ci}" data-i="${i}" aria-label="${esc(d.name)} – ${t().open3d}"><img src="${d.img}" alt="" loading="lazy"></button>`).join('');
  const secs = MENU.drinks.map((s) => {
    const head = `<h3>${svg(s.icon)}${esc(s.title)} <small>${esc(s.sub[lang])}</small>${s.al.length ? `<span class="algs">${algIcons(s.al)}</span>` : ''}</h3>`;
    const rows = s.rows.map((r) => `<div class="dr"><span class="n">${esc(r.name)}${r.pick ? ' <b class="star">★</b>' : ''}${r.note ? `<em>${esc(r.note)}</em>` : ''}${algIcons(r.al.filter((n) => !s.al.includes(n)))}</span><span class="dots"></span><span class="opts">${optButtons(r)}</span></div>`).join('');
    return `<div class="dsec">${head}${rows}</div>`;
  }).join('');
  return `<div class="drinks-pics">${pics}</div><div class="drinks-wrap">${secs}</div>`;
}

function renderPage() {
  document.documentElement.lang = lang;
  $$('.langs button').forEach((b) => b.setAttribute('aria-pressed', b.dataset.lang === lang));
  $$('[data-t]').forEach((el) => { el.textContent = t()[el.dataset.t]; });
  $('.cats').innerHTML = `<a href="#combos">${t().combos}</a>` + MENU.categories.map((c) => `<a href="#${c.id}">${esc(c.id === 'drinks' ? t().drinks : c.title)}</a>`).join('');
  $('.v-cats').innerHTML = MENU.categories.map((c, ci) => `<button data-vc="${ci}">${esc(c.id === 'drinks' ? t().drinks : c.title)}</button>`).join('');
  $('#menu').innerHTML = `<section class="cat combos" id="combos"><header class="cat-head"><h2>${t().combos}</h2><p>${t().combosSub}</p>
    <div class="orn"><span></span><i></i><span></span></div></header><div class="who"></div><div class="combo-list"></div></section>
    <p class="photo-note">${t().photo}</p>` + MENU.categories.map((c, ci) => `<section class="cat" id="${c.id}">
    <header class="cat-head"><h2>${esc(c.id === 'drinks' ? t().drinks : c.title)}</h2><p>${esc(c.sub[lang])}</p><div class="orn"><span></span><i></i><span></span></div>
    <button class="open3d" data-c="${ci}" data-i="0">${CUBE}${t().open3d}</button></header>
    ${c.id === 'drinks' ? drinksBlock(ci) : `<div class="dishes">${c.items.map((d, i) => dishCard(d, ci, i)).join('')}</div>`}
  </section>`).join('');
  $('.lgrid').innerHTML = Object.entries(MENU.allergens).map(([n, names]) =>
    `<div><img src="assets/allergens/${n}.png" alt="">${esc(names[LI[lang]])}</div>`).join('');
  $('.credits ul').innerHTML = MENU.credits.map((c) => `<li>${esc(c.dishes.join(', '))}: “${esc(c.title || '')}” · ${esc(c.creator || '')} · <a href="${esc(c.source)}" rel="noopener" target="_blank">${esc(c.license.toUpperCase())} ${esc(c.version || '')}</a></li>`).join('');
  renderCombos();
  spy();
  renderBar();
  if (view.open) renderCard(false);
  if (!$('.sheet').hidden) renderSheet();
  if (!$('.waiter').hidden) renderWaiter();
}

let spyObs;
function spy() {
  spyObs?.disconnect();
  const nav = $('.cats');
  const links = $$('a', nav);
  spyObs = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (!e.isIntersecting) return;
      links.forEach((a) => {
        const on = a.hash === `#${e.target.id}`;
        if (a.getAttribute('aria-current') === String(on)) return;
        a.setAttribute('aria-current', on);
        if (on) nav.scrollTo({ left: a.offsetLeft - nav.clientWidth / 2 + a.offsetWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
      });
    });
  }, { rootMargin: '-45% 0px -50% 0px' });
  $$('.cat').forEach((s) => spyObs.observe(s));
}

function renderCombos() {
  $('.who').innerHTML = Object.entries(MENU.audiences).map(([k, names]) =>
    `<button data-who="${k}" aria-pressed="${k === who}"><svg viewBox="0 0 24 24">${WHO_ICON[k]}</svg>${esc(names[LI[lang]])}</button>`).join('');
  $('.combo-list').innerHTML = MENU.combos.map((c, k) => [c, k]).filter(([c]) => c.who === who).map(([c, k]) => {
    const pics = c.items.map(([id]) => ITEMS[id].img).filter(Boolean).slice(0, 4).map((src) => `<img src="${src}" alt="" loading="lazy">`).join('');
    const tg = [c.veg ? `<span class="tag veg">${LEAF}${t().veg}</span>` : '', c.mild ? `<span class="tag">${t().mild}</span>` : ''].join('');
    return `<article class="combo"><div class="c-pics">${pics}</div><h3>${esc(c.name)}</h3><div class="tags">${tg}</div><p>${esc(c.tag[lang])}</p>
      <ul>${c.items.map(([id, q]) => `<li><b>${q}×</b>${esc(itemName(id))}</li>`).join('')}</ul>
      <button class="btn gold sm" data-combo="${k}">+ ${t().addAll}</button></article>`;
  }).join('');
}

function addCombo(k) {
  const c = MENU.combos[k];
  c.items.forEach(([id, q]) => { sel.set(id, (sel.get(id) || 0) + q); refreshSlots(id); });
  saveSel();
  renderBar();
  const el = $('.toast');
  el.innerHTML = `<span>${esc(t().comboAdded(c.name))}</span><button class="toast-add bar-open">${t().seeSel}</button><button class="toast-x" aria-label="${t().close}">×</button>`;
  el.hidden = false;
  clearTimeout(toastT);
  toastT = setTimeout(() => { el.hidden = true; }, 8000);
}

function alg(list) {
  if (list === null) return `<span>${t().al}:</span><span class="ask">${t().ask}</span>`;
  return list.map((n) => `<span><img src="assets/allergens/${n}.png" alt="">${algName(n)}</span>`).join('');
}

function renderCard(swap = true) {
  const c = MENU.categories[view.cat];
  const d = c.items[view.active];
  $('.v-cat').textContent = c.id === 'drinks' ? t().drinks : c.title;
  $('.v-sub').textContent = c.sub[lang];
  $$('.v-cats button').forEach((b) => b.setAttribute('aria-current', +b.dataset.vc === view.cat));
  const fill = () => {
    const st = d.story && MENU.stories[d.story];
    $('.card .name').textContent = d.name;
    $('.card .price').textContent = d.price;
    $('.card .tags').innerHTML = tags(d) + (st ? `<span class="tag kind">${esc(kind(d))}</span>` : '') + '<img src="assets/chilli.png" alt="">'.repeat(d.hot);
    $('.card .desc').textContent = d.desc[lang] || d.desc.en || '';
    $('.card .note').textContent = d.note;
    $('.card .story').hidden = !st;
    $('.card .story p').textContent = st ? st[lang] : '';
    $('.card .story summary').textContent = t().story;
    $('.card .pair').innerHTML = d.pair ? `${svg('wine')}<span>${t().pair} <b>${esc(d.pair)}</b></span>` : '';
    $('.card .alg').innerHTML = alg(d.al);
    $('.card .slot').dataset.slot = d.id;
    $('.card .slot').innerHTML = stepper(d.id);
    $('.count').textContent = `${view.active + 1} / ${c.items.length}`;
    $('.card .dish').classList.remove('swap');
  };
  if (!swap || reduce) return fill();
  $('.card .dish').classList.add('swap');
  clearTimeout(renderCard.t);
  renderCard.t = setTimeout(fill, 150);
}

const layers = [];
function pushLayer(name, close) {
  layers.push({ name, close });
  history.pushState({ layer: name }, '');
  document.body.classList.add('locked');
}
function popLayer() {
  const l = layers.pop();
  l?.close();
  if (!layers.length) document.body.classList.remove('locked');
}
const closeTop = () => history.back();
addEventListener('popstate', popLayer);

function openViewer(ci, i) {
  if (!scene3d) return;
  view.cat = ci;
  view.active = i;
  if (!view.open) {
    view.open = true;
    pushLayer('viewer', () => {
      view.open = false;
      $('.viewer').hidden = true;
      document.body.classList.remove('viewing');
      scene3d.close();
    });
  }
  $('.viewer').hidden = false;
  document.body.classList.add('viewing');
  renderCard(false);
  scene3d.open(MENU.categories[ci], i);
  const chip = $(`.v-cats [data-vc="${ci}"]`);
  const strip = $('.v-cats');
  strip.scrollTo({ left: chip.offsetLeft - strip.clientWidth / 2 + chip.offsetWidth / 2 });
}

function go(i) {
  const n = MENU.categories[view.cat].items.length;
  const next = ((i % n) + n) % n;
  scene3d?.goTo(next);
  setActive(next);
}

function setActive(i) {
  if (i === view.active) return;
  view.active = i;
  renderCard();
}

function changeQty(id, delta) {
  const q = Math.max(0, (sel.get(id) || 0) + delta);
  if (q) sel.set(id, q); else sel.delete(id);
  saveSel();
  refreshSlots(id);
  renderBar();
  if (!$('.sheet').hidden) renderSheet();
  if (delta > 0 && q === 1) suggest(id);
}

function refreshSlots(id) {
  $$(`[data-slot="${id}"]`).forEach((el) => { el.innerHTML = stepper(id, el.classList.contains('sm')); });
  const row = ITEMS[id].course === 'drink' && MENU.drinks.flatMap((s) => s.rows).find((r) => r.opts.some((o) => o.id === id));
  if (row) $$(`.opt[data-add="${id}"]`).forEach((b) => { b.parentElement.innerHTML = optButtons(row); });
}

function courseState() {
  return COURSES.map((c) => [...sel.keys()].some((id) => ITEMS[id].course === c));
}

function renderBar() {
  const n = [...sel.values()].reduce((a, b) => a + b, 0);
  $('.bar').hidden = !n;
  document.body.classList.toggle('has-bar', !!n);
  $('.bar-steps').innerHTML = courseState().map((on, k) => `<i class="${on ? 'on' : ''}" title="${t().courses[k]}"></i>`).join('');
  $('.bar-count').textContent = t().items(n);
  $('.bar-cta').textContent = t().sel;
}

function findDrink(pair) {
  const rows = MENU.drinks.flatMap((s) => s.rows);
  const p = pair.toLowerCase();
  const r = rows.find((x) => x.name.toLowerCase() === p)
    || rows.find((x) => x.name.toLowerCase().includes(p))
    || rows.find((x) => p.includes(x.name.toLowerCase()));
  return r && r.opts[0].id;
}

const byName = (n) => Object.keys(ITEMS).find((id) => ITEMS[id].name === n);
let toastT;
function suggest(id) {
  const it = ITEMS[id];
  const [hasDrink, , hasMain, hasSide, hasDessert] = courseState();
  let s = null;
  if (it.course === 'main') {
    const w = it.pair && findDrink(it.pair);
    if (w && !sel.has(w)) s = { text: t().sugPair(it.name, itemName(w)), id: w };
    else if (!hasSide) s = { text: t().sugBread, id: byName('Garlic Naan') };
  } else if (it.course === 'starter' && !hasDrink) {
    const w = (it.pair && findDrink(it.pair)) || byName('Mango Lassi');
    s = { text: it.pair ? t().sugPair(it.name, itemName(w)) : t().sugDrink, id: w };
  } else if (it.course === 'side' && hasMain && !hasDessert) {
    s = { text: t().sugDessert, id: byName('Gajar Halwa') };
  }
  if (!s?.id || sel.has(s.id)) return;
  const el = $('.toast');
  el.innerHTML = `<span>${esc(s.text)}</span><button class="toast-add" data-add="${s.id}">+ ${esc(itemName(s.id))} · ${money(ITEMS[s.id].p)}</button><button class="toast-x" aria-label="${t().close}">×</button>`;
  el.hidden = false;
  clearTimeout(toastT);
  toastT = setTimeout(() => { el.hidden = true; }, 8000);
}

let sheetMode = 'sel';
let guideStep = 0;
function openSheet(mode, step = 0) {
  sheetMode = mode;
  guideStep = step;
  if ($('.sheet').hidden) pushLayer('sheet', () => { $('.sheet').hidden = true; });
  $('.sheet').hidden = false;
  renderSheet();
  $('.sheet-body').scrollTop = 0;
}

function guideRow(d) {
  const thumb = d.img ? `<img src="${d.img}" alt="" width="52" height="52" loading="lazy">` : `<span class="ph">${MINI}</span>`;
  return `<div class="gr">${thumb}<div class="gi"><b>${esc(d.name)}${'<img class="chili" src="assets/chilli.png" alt="">'.repeat(d.hot)}</b>
    <small>${[d.veg ? t().veg : '', region(d)].filter(Boolean).map(esc).join(' · ')}</small>${d.pick ? `<em>★ ${t().pick}</em>` : ''}</div>
    <span class="gp">${esc(d.price)}</span><span class="slot sm" data-slot="${d.id}">${stepper(d.id, true)}</span></div>`;
}

function guideBody(course) {
  const drow = (r) => `<div class="dr"><span class="n">${esc(r.name)}${r.note ? `<em>${esc(r.note)}</em>` : ''}</span><span class="dots"></span><span class="opts">${optButtons(r)}</span></div>`;
  if (course === 'drink') {
    const picks = MENU.drinks.flatMap((s) => s.rows).filter((r) => r.pick);
    return `<h4>★ ${t().picks}</h4>${picks.map(drow).join('')}`
      + MENU.drinks.map((s) => `<h4>${svg(s.icon)}${esc(s.title)}</h4>${s.rows.map(drow).join('')}`).join('');
  }
  const dishes = MENU.categories.filter((c) => c.id !== 'drinks');
  const picks = dishes.flatMap((c) => c.items).filter((d) => d.course === course && d.pick);
  const groups = dishes.map((c) => [c, c.items.filter((d) => d.course === course)]).filter(([, l]) => l.length);
  return (picks.length ? `<h4>★ ${t().picks}</h4>${picks.map(guideRow).join('')}` : '')
    + groups.map(([c, l]) => `<h4>${esc(c.title)}</h4>${l.map(guideRow).join('')}`).join('');
}

function renderSheet() {
  const head = $('.sheet-head');
  const body = $('.sheet-body');
  const foot = $('.sheet-foot');
  const state = courseState();
  if (sheetMode === 'guide') {
    head.innerHTML = `<div><small>${t().step(guideStep + 1, COURSES.length)}</small><h2>${t().guideIntro[guideStep]}</h2></div><button class="x" aria-label="${t().close}">×</button>
      ${guideStep === 0 ? `<button class="to-combos" data-tocombos>${t().orCombo}</button>` : ''}
      <div class="steps">${COURSES.map((c, k) => `<button data-step="${k}" class="${k === guideStep ? 'cur' : ''}${state[k] ? ' on' : ''}">${t().courses[k]}</button>`).join('')}</div>`;
    body.innerHTML = guideBody(COURSES[guideStep]);
    foot.innerHTML = `<button class="btn ghost sm" data-step="${guideStep - 1}" ${guideStep ? '' : 'disabled'}>${t().prev}</button>
      <span class="sum"></span>
      <button class="btn gold sm" data-step="${guideStep + 1}">${guideStep === COURSES.length - 1 ? t().done : state[guideStep] ? t().next : t().skip}</button>`;
    return;
  }
  head.innerHTML = `<div><h2>${t().sel}</h2></div><button class="x" aria-label="${t().close}">×</button>`;
  if (!sel.size) {
    body.innerHTML = `<p class="empty">${t().empty}</p><div class="empty-cta"><button class="btn gold" data-guide="0">${t().build}</button><button class="btn ghost" data-tocombos>${t().combos}</button></div>`;
    foot.innerHTML = '';
    return;
  }
  body.innerHTML = COURSES.map((c, k) => {
    const ids = [...sel.keys()].filter((id) => ITEMS[id].course === c);
    if (!ids.length) return `<button class="missing" data-guide="${k}">+ ${t().addCourse}: ${t().courses[k]}</button>`;
    return `<h4>${t().courses[k]}</h4>` + ids.map((id) => `<div class="sr"><span class="sn">${esc(itemName(id))}</span>
      <span class="slot sm" data-slot="${id}">${stepper(id, true)}</span></div>`).join('');
  }).join('') + `<p class="hint-note">${t().notOrder}</p>`;
  foot.innerHTML = `<button class="btn ghost sm" data-clear>${t().clear}</button><span class="sum"></span>
    <button class="btn gold sm" data-waiter>${t().waiter}</button>`;
}

function renderWaiter() {
  $('.waiter-body').innerHTML = `<p class="w-kicker">${t().forWaiter}</p>`
    + COURSES.map((c, k) => {
      const ids = [...sel.keys()].filter((id) => ITEMS[id].course === c);
      return ids.length ? `<h3>${t().courses[k]}</h3>` + ids.map((id) => `<div class="wr"><b>${sel.get(id)}×</b><span>${esc(itemName(id))}</span></div>`).join('') : '';
    }).join('');
  $('.waiter-x').setAttribute('aria-label', t().close);
}

function openWaiter() {
  pushLayer('waiter', () => { $('.waiter').hidden = true; });
  $('.waiter').hidden = false;
  renderWaiter();
}

$$('.langs button').forEach((b) => b.addEventListener('click', () => { lang = b.dataset.lang; store.set('ac-lang', lang); renderPage(); }));
document.addEventListener('click', (e) => {
  const el = e.target.closest('button, a, [data-c]');
  if (!el) return;
  const ds = el.dataset;
  if (ds.add) return changeQty(ds.add, 1);
  if (ds.dec) return changeQty(ds.dec, -1);
  if (el.matches('.toast-x')) { $('.toast').hidden = true; return; }
  if (el.matches('.sheet .x, .waiter-x, .v-close')) return closeTop();
  if (ds.step !== undefined) {
    const k = +ds.step;
    if (k >= COURSES.length) sheetMode = 'sel'; else guideStep = k;
    renderSheet();
    $('.sheet-body').scrollTop = 0;
    return;
  }
  if (ds.guide !== undefined) return openSheet('guide', +ds.guide);
  if (el.matches('[data-clear]')) { sel.clear(); saveSel(); renderPage(); return; }
  if (el.matches('[data-waiter]')) return openWaiter();
  if (el.matches('.bar-open')) { $('.toast').hidden = true; return openSheet('sel'); }
  if (ds.combo !== undefined) return addCombo(+ds.combo);
  if (ds.who) { who = ds.who; renderCombos(); return; }
  if (el.matches('[data-tocombos]')) { closeTop(); setTimeout(() => $('#combos').scrollIntoView({ behavior: reduce ? 'auto' : 'smooth' }), 80); return; }
  if (ds.vc !== undefined) return openViewer(+ds.vc, 0);
  if (el.matches('[data-open3d]')) return openViewer(0, 0);
  if (el.matches('.arrow.prev')) return go(view.active - 1);
  if (el.matches('.arrow.next')) return go(view.active + 1);
  if (ds.c !== undefined && el.closest('#menu')) openViewer(+ds.c, +ds.i);
});
$('#menu').addEventListener('keydown', (e) => {
  const c = e.target.closest('.dc');
  if (c && e.target === c && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); openViewer(+c.dataset.c, +c.dataset.i); }
});
addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && layers.length) return closeTop();
  if (layers.at(-1)?.name !== 'viewer') return;
  if (e.key === 'ArrowRight') go(view.active + 1);
  else if (e.key === 'ArrowLeft') go(view.active - 1);
});
const head = $('.site-head');
const onScroll = () => head.classList.toggle('solid', scrollY > innerHeight * 0.55);
addEventListener('scroll', onScroll, { passive: true });
onScroll();
if (history.state?.layer) history.replaceState(null, '');

renderPage();
if (matchMedia('(min-width: 1000px)').matches) $('.card .story').open = true;
try {
  const { createScene } = await import('./scene.js');
  await Promise.race([document.fonts.load('800 60px Eczar'), new Promise((r) => setTimeout(r, 2500))]);
  scene3d = createScene({ isOpen: () => view.open, onActive: setActive });
} catch {
  scene3d = null;
}
if (!scene3d) document.documentElement.classList.add('nogl');
