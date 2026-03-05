let printers = [
  { id:1, name:"Caixa 01 - Fiscal",     model:"Elgin i9",          type:"nf", conn:"USB",          ip:"USB-001",       sector:"Caixa",      status:"online",  caps:["Cupom Fiscal","NF-e","SAT"] },
  { id:2, name:"Caixa 02 - Fiscal",     model:"Bematech MP-4200",  type:"nf", conn:"Rede (TCP/IP)", ip:"192.168.1.101", sector:"Caixa",      status:"online",  caps:["Cupom Fiscal","NF-e"] },
  { id:3, name:"Etiquetadora Estoque",  model:"Zebra ZD421",       type:"et", conn:"Rede (TCP/IP)", ip:"192.168.1.110", sector:"Estoque",    status:"online",  caps:["Nome Produto","Preço","Código de Barras"] },
  { id:4, name:"Etiquetadora Gondola",  model:"TSC TTP-225",       type:"et", conn:"USB",           ip:"USB-002",       sector:"Gondola",    status:"standby", caps:["Nome Produto","Preço"] },
  { id:5, name:"Retaguarda Fiscal",     model:"Epson TM-T20X",     type:"nf", conn:"Serial",        ip:"COM3",          sector:"Retaguarda", status:"offline", caps:["Cupom Fiscal"] },
];

let nextId = 6;
let currentFilter = "all";
let selectedType = "nf";

function statusLabel(s) {
  return { online: "Online", offline: "Offline", standby: "Standby" }[s] || s;
}

function renderStats() {
  document.getElementById("stat-total").textContent  = printers.length;
  document.getElementById("stat-nf").textContent     = printers.filter(p => p.type === "nf").length;
  document.getElementById("stat-et").textContent     = printers.filter(p => p.type === "et").length;
  document.getElementById("stat-online").textContent = printers.filter(p => p.status === "online").length;
}

function renderGrid() {
  const grid  = document.getElementById("printer-grid");
  const empty = document.getElementById("empty-state");

  let list = printers;
  if (currentFilter === "nf")      list = printers.filter(p => p.type === "nf");
  if (currentFilter === "et")      list = printers.filter(p => p.type === "et");
  if (currentFilter === "online")  list = printers.filter(p => p.status === "online");
  if (currentFilter === "offline") list = printers.filter(p => p.status === "offline");

  if (!list.length) {
    grid.innerHTML = "";
    empty.style.display = "block";
    return;
  }
  empty.style.display = "none";

  grid.innerHTML = list.map((p, i) => `
    <div class="printer-card ${p.type}-type" style="animation-delay:${i * 0.05}s">
      <div class="status-row">
        <div class="dot ${p.status}"></div>
        ${statusLabel(p.status)}
      </div>

      <div class="type-badge ${p.type}">
        ${p.type === "nf"
          ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Nota Fiscal`
          : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg> Etiquetas`}
      </div>

      <div class="printer-name">${p.name}</div>
      <div class="printer-model">${p.model}</div>

      <div class="info-grid">
        <div class="info-item">
          <div class="info-key">Conexão</div>
          <div class="info-val">${p.conn}</div>
        </div>
        <div class="info-item">
          <div class="info-key">IP / Porta</div>
          <div class="info-val">${p.ip}</div>
        </div>
        <div class="info-item">
          <div class="info-key">Setor</div>
          <div class="info-val">${p.sector}</div>
        </div>
        <div class="info-item">
          <div class="info-key">ID</div>
          <div class="info-val">#${String(p.id).padStart(4, "0")}</div>
        </div>
      </div>

      <div class="capabilities">
        ${p.caps.map(c => `<span class="cap-tag">${c}</span>`).join("")}
      </div>

      <div class="card-actions">
        <button class="btn-action ${p.type === "nf" ? "primary" : "et-primary"}" onclick="testPrint(${p.id})">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6,9 6,2 18,2 18,9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Testar
        </button>
        <button class="btn-action" onclick="toggleStatus(${p.id})">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
          Status
        </button>
        <button class="btn-action danger" onclick="deletePrinter(${p.id})">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
        </button>
      </div>
    </div>
  `).join("");
}

function render() {
  renderStats();
  renderGrid();
}

function setFilter(f, el) {
  currentFilter = f;
  document.querySelectorAll(".filter-tab").forEach(t => t.classList.remove("active"));
  el.classList.add("active");
  render();
}

function openModal() {
  const overlay = document.getElementById("modal-overlay");

  overlay.classList.add("open");
  document.body.classList.add("modal-open");
  overlay.setAttribute("aria-hidden", "false");

  document.getElementById("inp-name").value   = "";
  document.getElementById("inp-model").value  = "";
  document.getElementById("inp-ip").value     = "";
  document.getElementById("inp-sector").value = "";

  selectType("nf");

  setTimeout(() => document.getElementById("inp-name")?.focus(), 0);
}

function closeModal() {
  const overlay = document.getElementById("modal-overlay");

  overlay.classList.remove("open");
  document.body.classList.remove("modal-open");
  overlay.setAttribute("aria-hidden", "true");
}

const overlayEl = document.getElementById("modal-overlay");

document.getElementById("modal-overlay").addEventListener("click", (e) => {
  if (e.target === e.currentTarget) closeModal();
});

document.addEventListener("keydown", (e) => {
  const overlay = document.getElementById("modal-overlay");
  if (e.key === "Escape" && overlay?.classList.contains("open")) closeModal();
});

function selectType(t) {
  selectedType = t;
  document.getElementById("opt-nf").classList.toggle("selected", t === "nf");
  document.getElementById("opt-et").classList.toggle("selected", t === "et");
}

function savePrinter() {
  const name  = document.getElementById("inp-name").value.trim();
  const model = document.getElementById("inp-model").value.trim();
  if (!name || !model) { showToast("Preencha nome e modelo!"); return; }

  const caps = selectedType === "nf"
    ? ["Cupom Fiscal", "NF-e"]
    : ["Nome Produto", "Preço", "Código de Barras"];

  printers.push({
    id: nextId++,
    name,
    model,
    type:   selectedType,
    conn:   document.getElementById("inp-conn").value,
    ip:     document.getElementById("inp-ip").value     || "—",
    sector: document.getElementById("inp-sector").value || "—",
    status: document.getElementById("inp-status").value,
    caps,
  });

  closeModal();
  render();
  showToast("Impressora adicionada com sucesso!");
}

function deletePrinter(id) {
  printers = printers.filter(p => p.id !== id);
  render();
  showToast("Impressora removida.");
}

function toggleStatus(id) {
  const p = printers.find(x => x.id === id);
  if (!p) return;
  const cycle = { online: "offline", offline: "standby", standby: "online" };
  p.status = cycle[p.status] || "online";
  render();
  showToast(`Status: ${statusLabel(p.status)}`);
}

function testPrint(id) {
  const p = printers.find(x => x.id === id);
  if (p && p.status !== "online") { showToast("Impressora offline — teste indisponível."); return; }
  showToast(`Imprimindo teste em "${p.name}"...`);
}

function showToast(msg) {
  const t = document.getElementById("toast");
  document.getElementById("toast-msg").textContent = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 2800);
}

render();