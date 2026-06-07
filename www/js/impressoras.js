let printers = [];
let currentFilter = "all";
let selectedType = "nf";

async function loadPrinters() {
  try {
    const res = await fetch("/api/impressoras");
    const data = await res.json();
    printers = data;

    render();

  } catch (e) {
    showToast("Erro ao carregar impressoras");
    console.error(e);
  }
}

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

  if (currentFilter === "nf")
    list = printers.filter(p => p.type === "nf");

  if (currentFilter === "et")
    list = printers.filter(p => p.type === "et");

  if (currentFilter === "online")
    list = printers.filter(p => p.status === "online");

  if (currentFilter === "offline")
    list = printers.filter(p => p.status === "offline");

  if (!list.length) {
    grid.innerHTML = "";
    empty.style.display = "block";
    return;
  }

  empty.style.display = "none";

  grid.innerHTML = list.map((p, i) => `

    <div class="printer-card ${p.type}-type">

      <div class="status-row">
        <div class="dot ${p.status}"></div>
        ${statusLabel(p.status)}
      </div>

      <div class="type-badge ${p.type}">
        ${p.type === "nf" ? "🧾 Nota Fiscal" : "🏷️ Etiquetas"}
      </div>

      <div class="printer-name">${p.name}</div>
      <div class="printer-model">${p.model}</div>

      <div class="info-grid">

        <div class="info-item">
          <div class="info-key">Conexão</div>
          <div class="info-val">${p.conn}</div>
        </div>

        <div class="info-item">
          <div class="info-key">IP</div>
          <div class="info-val">${p.ip}</div>
        </div>

        <div class="info-item">
          <div class="info-key">Setor</div>
          <div class="info-val">${p.sector}</div>
        </div>

        <div class="info-item">
          <div class="info-key">ID</div>
          <div class="info-val">#${String(p.id).padStart(4,"0")}</div>
        </div>

      </div>

      <div class="capabilities">
        ${(p.caps || []).map(c => `<span class="cap-tag">${c}</span>`).join("")}
      </div>

      <div class="card-actions">
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

  document.querySelectorAll(".filter-tab")
    .forEach(t => t.classList.remove("active"));

  el.classList.add("active");

  render();
}

async function deletePrinter(id) {

  try {

    await fetch("/api/impressoras/delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });

    printers = printers.filter(p => p.id !== id);
    render();

    showToast("Impressora removida");

  } catch (e) {
    showToast("Erro ao excluir");
  }
}

async function toggleStatus(id) {

  const p = printers.find(x => x.id === id);
  if (!p) return;

  const cycle = {
    online: "offline",
    offline: "standby",
    standby: "online"
  };

  const newStatus = cycle[p.status] || "online";

  try {

    await fetch("/api/impressoras/status", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id,
        status: newStatus
      })
    });

    p.status = newStatus;
    render();

  } catch {
    showToast("Erro ao alterar status");
  }
}

function testPrint(id) {

  fetch("/api/impressoras/test", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });

  showToast("Enviando impressão de teste");
}

function showToast(msg) {

  const t = document.getElementById("toast");

  document.getElementById("toast-msg").textContent = msg;

  t.classList.add("show");

  setTimeout(() => t.classList.remove("show"), 2500);
}

loadPrinters();