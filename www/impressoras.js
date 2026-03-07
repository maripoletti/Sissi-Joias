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
          ? `🧾 Nota Fiscal`
          : `🏷️ Etiquetas`}
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
        <button class="btn-action" onclick="testPrint(${p.id})">Testar</button>
        <button class="btn-action" onclick="toggleStatus(${p.id})">Status</button>
        <button class="btn-action danger" onclick="deletePrinter(${p.id})">Excluir</button>
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

document.getElementById("modal-overlay").addEventListener("click", (e) => {
  if (e.target === e.currentTarget) closeModal();
});

document.addEventListener("keydown", (e) => {
  const overlay = document.getElementById("modal-overlay");
  if (!overlay?.classList.contains("open")) return;

  if (e.key === "Escape") closeModal();

  if (e.key === "Enter") {
    const tag = document.activeElement?.tagName?.toLowerCase();
    if (tag === "select") return;
    e.preventDefault();
    savePrinter();
  }
});

function selectType(t) {
  selectedType = t;
  document.getElementById("opt-nf").classList.toggle("selected", t === "nf");
  document.getElementById("opt-et").classList.toggle("selected", t === "et");
}

function savePrinter() {
  const nameEl   = document.getElementById("inp-name");
  const modelEl  = document.getElementById("inp-model");
  const connEl   = document.getElementById("inp-conn");
  const ipEl     = document.getElementById("inp-ip");
  const sectorEl = document.getElementById("inp-sector");
  const statusEl = document.getElementById("inp-status");

  if (!nameEl || !modelEl || !connEl || !ipEl || !sectorEl || !statusEl) {
    showToast("Campos do modal não encontrados (IDs errados).");
    return;
  }

  const name  = nameEl.value.trim();
  const model = modelEl.value.trim();
  if (!name || !model) { showToast("Preencha nome e modelo!"); return; }

  const caps = selectedType === "nf"
    ? ["Cupom Fiscal", "NF-e"]
    : ["Nome Produto", "Preço", "Código de Barras"];

  printers.push({
    id: nextId++,
    name,
    model,
    type: selectedType,
    conn: connEl.value,
    ip: ipEl.value.trim() || "—",
    sector: sectorEl.value.trim() || "—",
    status: statusEl.value,
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