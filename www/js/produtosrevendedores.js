const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const esc = value => String(value ?? "").replace(/[&<>"']/g, c => ({"&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#39;"}[c]));
let page = 1;
let limit = 10; // Quantidade de maletas por página; cada uma traz todos os seus produtos.
let loading = false;
let acabou = false;
let tipoClick = null;
let produtosCache = [];

let versaoLista = 0;
async function carregarProdutos() {
  if (loading || acabou) return;
  loading = true;
  const version = versaoLista;
  try {
    const resultado = await api("/api/produtosRevendedores", {
      method: "POST", headers: {"Content-Type": "application/json"},
      body: JSON.stringify({filters: {
        produto: document.getElementById("filtroProduto").value.trim() || null,
        revendedor: document.getElementById("filtroRevendedor").value.trim() || null
      }, pagination: {page, limit}})
    });
    if (version !== versaoLista) return;
    const dados = resultado.produtos;
    if (!Array.isArray(dados) || typeof resultado.pagination?.has_more !== "boolean") {
      throw new Error("Não foi possível carregar os produtos.");
    }
    produtosCache = page === 1 ? dados : produtosCache.concat(dados);
    acabou = !resultado.pagination.has_more;
    renderizarTabela(dados, true);
    page++;
    atualizarSelecao();
  } catch (error) {
    console.error(error);
    document.getElementById("resumoSelecao").hidden = false;
    document.getElementById("resumoSelecao").textContent = error.message;
  } finally {
    loading = false;
    if (version !== versaoLista) carregarProdutos();
  }
}

function formatarData(data) {
  const d = new Date(data);
  return d.toLocaleDateString("pt-BR");
}

function renderizarTabela(dados, append = false) {
  const tbody = document.querySelector("#tabelaRevendedores tbody");

  if (!append) {
    tbody.innerHTML = "";
  }

  const grupos = {};

  dados.forEach(item => {
    const caseID = `${item.RevId}:${item.CaseID ?? 0}`;

    if (!grupos[caseID]) {
      grupos[caseID] = {
        nome: item.CaseName ?? "Sem maleta",
        revId: Number(item.RevId),
        caseId: Number(item.CaseID ?? 0),
        revendedor: item.revendedor,
        produtos: [],
        total: 0
      };
    }

    grupos[caseID].produtos.push(item);

    grupos[caseID].total +=
      Number(item.quantidade) * Number(item.preco_revenda);
  });

  Object.entries(grupos).forEach(([caseID, grupo]) => {
    const trHeader = document.createElement("tr");

    trHeader.innerHTML = `
      <td colspan="7" class="maleta-header" data-id="${caseID}">
        <strong>${esc(grupo.nome)} — ${esc(grupo.revendedor)}</strong>

        <span style="float:right; display:flex; align-items:center; gap:10px; justify-content:flex-end;">
          <button type="button" class="btn-imprimir-maleta"
            onclick="event.stopPropagation(); imprimirMaleta(${grupo.caseId}, ${grupo.revId}, '58mm')"
            title="Imprimir comprovante de 58 mm">🖨️ 58 mm</button>
          <button type="button" class="btn-imprimir-maleta"
            onclick="event.stopPropagation(); imprimirMaleta(${grupo.caseId}, ${grupo.revId}, 'a4')"
            title="Imprimir folha A4">🖨️ A4</button>

          <span>R$ ${grupo.total.toFixed(2)}</span>
        </span>
      </td>
    `;

    tbody.appendChild(trHeader);

    trHeader.style.cursor = "pointer";

    trHeader.addEventListener("click", () => {
      toggleMaleta(trHeader);
    });

    grupo.produtos.forEach(item => {
      const tr = document.createElement("tr");   
      
      tr.dataset.quantidade = item.quantidade;
      tr.dataset.prodId = item.ProdId;
      tr.dataset.revId = item.RevId;
      tr.dataset.caseId = grupo.caseId;

      tr.addEventListener("click", (e) => {

        if (e.target.closest(".acoes") || processandoAcao) return;

        const checkbox = tr.querySelector('input[type="checkbox"]');

        if (!checkbox || checkbox.disabled) return;
        const td = checkbox.closest(".selecao");
        if (!td || td.style.display === "none") return;

        checkbox.checked = !checkbox.checked;

        toggleCheckbox(
          checkbox,
          item.ProdId,
          item.RevId,
          grupo.caseId
        );
      });

      tr.innerHTML = `
        <td class="selecao" style="display:none">
          <input
            type="checkbox"
            onclick="event.stopPropagation()"
            onchange="toggleCheckbox(this, '${item.ProdId}', '${item.RevId}', '${grupo.caseId}')"
          >
        </td>

        <td>${esc(item.produto)}</td>
        <td>${esc(item.revendedor)}</td>
        <td>${item.quantidade}</td>
        <td>${Number(item.desconto) > 0 ? `<small>R$ ${Number(item.preco_original).toFixed(2)} · cupom ${Number(item.desconto)}%</small><br>` : ""}R$ ${Number(item.preco_revenda).toFixed(2)}</td>
        <td>${formatarData(item.data_envio)}</td>

        <td class="acoes">
          <button onclick="editarQuantidade(this)">✏️</button>
          <button onclick="excluirLinha(this)">🗑️</button>
        </td>
      `;

      tr.style.display = "none";
      tbody.appendChild(tr);
    });
  });
}
function imprimirMaleta(caseId, revId, formato) {
    const query = new URLSearchParams({case_id: caseId, revendedora_id: revId, formato});
    const win = window.open(`/api/maleta/imprimir?${query}`, "_blank");
    if (!win) alert("Permita pop-ups para abrir a impressão.");
}

function toggleMaleta(headerRow) {

  let next = headerRow.nextElementSibling;

  const header = headerRow.querySelector(".maleta-header");

  const aberto = header.dataset.aberto !== "false";

  header.dataset.aberto = aberto ? "false" : "true";

  while (next && !next.querySelector(".maleta-header")) {
    next.style.display = aberto ? "none" : "";
    next = next.nextElementSibling;
  }
}

let processandoAcao = false;
const selecionados = new Set();
const modalMaleta = document.getElementById("modalMaleta");
const destinoSelect = document.getElementById("revendedoraDestino");
let revendedorasDestino = [];
let versaoMaletas = 0;

function ativarMaletaClick() {
  if (processandoAcao) return;
  if (tipoClick === "maleta") { abrirModalMaleta(); return; }
  tipoClick = "maleta";
  atualizarSelecao();
}

function ativarExcluirClick() {
  if (processandoAcao) return;
  if (tipoClick === "excluir") { excluirEmMassa(); return; }
  tipoClick = "excluir";
  atualizarSelecao();
}

function atualizarSelecao() {
  const ativo = tipoClick !== null;
  const quantidade = selecionados.size;
  document.querySelectorAll(".col-selecao, .selecao").forEach(td => { td.style.display = ativo ? "" : "none"; });
  const mover = document.getElementById("maletaClick");
  const remover = document.getElementById("excluirEmMassa");
  mover.hidden = tipoClick === "excluir";
  remover.hidden = tipoClick === "maleta";
  mover.textContent = tipoClick === "maleta" ? `Mover selecionados (${quantidade})` : "Mover para maleta";
  remover.textContent = tipoClick === "excluir" ? `Remover selecionados (${quantidade})` : "Remover em massa";
  mover.disabled = processandoAcao || (tipoClick === "maleta" && quantidade === 0);
  remover.disabled = processandoAcao || (tipoClick === "excluir" && quantidade === 0);
  const cancelar = document.getElementById("cancelarBtn");
  cancelar.hidden = !ativo;
  cancelar.disabled = processandoAcao;
  const resumo = document.getElementById("resumoSelecao");
  resumo.hidden = !ativo;
  resumo.textContent = quantidade ? `${quantidade} produto(s) selecionado(s).` : "Marque os produtos na tabela para continuar.";
  document.querySelectorAll("tr[data-prod-id]").forEach(row => {
    const key = `${row.dataset.prodId}-${row.dataset.revId}-${row.dataset.caseId}`;
    const checked = selecionados.has(key);
    row.classList.toggle("selecionado", checked);
    const checkbox = row.querySelector('input[type="checkbox"]');
    checkbox.checked = checked;
    checkbox.disabled = processandoAcao || Number(row.dataset.quantidade) <= 0;
  });
}

function cancelarSelecao() {
  if (processandoAcao) return;
  selecionados.clear();
  tipoClick = null;
  atualizarSelecao();
}

function toggleCheckbox(checkbox, prodId, revId, caseId) {
  if (processandoAcao) return;
  const key = `${prodId}-${revId}-${caseId}`;
  if (checkbox.checked) selecionados.add(key);
  else selecionados.delete(key);
  atualizarSelecao();
}

async function api(url, options = {}) {
  const headers = new Headers(options.headers);
  headers.set("X-CSRF-Token", csrfToken);
  const response = await fetch(url, {...options, headers});
  const data = await response.json();
  if (!response.ok || data.success === false) throw new Error(data.message || data.error || "Não foi possível concluir a operação.");
  return data;
}

async function abrirModalMaleta() {
  if (!selecionados.size || processandoAcao) return;
  modalMaleta.hidden = false;
  document.getElementById("feedbackMaleta").textContent = "";
  document.getElementById("resumoMovimentacao").textContent = `${selecionados.size} produto(s) selecionado(s). Escolha a destinatária e a maleta.`;
  const users = new Set([...selecionados].map(key => key.split("-")[1]));
  document.getElementById("confirmarMovimentacao").disabled = true;
  try {
    if (!revendedorasDestino.length) revendedorasDestino = await api("/api/produtos/revendedoras");
    destinoSelect.replaceChildren(new Option("Selecione a revendedora", ""));
    revendedorasDestino.forEach(user => destinoSelect.add(new Option(user.nome, user.id)));
    destinoSelect.value = users.size === 1 ? [...users][0] : "";
    await trocarModoMaleta();
  } catch (error) { document.getElementById("feedbackMaleta").textContent = error.message; }
  finally { document.getElementById("confirmarMovimentacao").disabled = false; }
  destinoSelect.focus();
}

function fecharModalMaleta() {
  if (processandoAcao) return;
  modalMaleta.hidden = true;
  document.getElementById("maletaClick").focus();
}
modalMaleta.addEventListener("click", event => { if (event.target === modalMaleta) fecharModalMaleta(); });
document.addEventListener("keydown", event => {
  if (modalMaleta.hidden) return;
  if (event.key === "Escape") fecharModalMaleta();
  if (event.key === "Tab") {
    const focusable = [...modalMaleta.querySelectorAll('button, select, input')].filter(el => !el.disabled && el.getClientRects().length);
    const first = focusable[0], last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  }
});

async function excluirEmMassa() {
  if (processandoAcao || !selecionados.size) return;
  if (!confirm(`Remover ${selecionados.size} produto(s) das revendedoras?`)) return;
  const itens = [...selecionados].map(key => {
    const [prodId, revId, caseId] = key.split("-").map(Number);
    return {prodId, revId, caseId};
  });
  processandoAcao = true;
  atualizarSelecao();
  try {
    await api("/api/produtosRevendedores/deletarEmMassa", {
      method: "POST", headers: {"Content-Type": "application/json"}, body: JSON.stringify({itens})
    });
    selecionados.clear();
    tipoClick = null;
    resetarLista();
    await carregarProdutos();
  } catch (error) { alert(error.message); }
  finally { processandoAcao = false; atualizarSelecao(); }
}

async function confirmarMaleta(event) {
  event.preventDefault();
  if (processandoAcao || !selecionados.size) return;
  const nova = document.getElementById("selectMaleta").value === "nova";
  const target = Number(destinoSelect.value);
  const caseId = nova ? null : Number(document.getElementById("selectMaletaExistente").value);
  const name = document.getElementById("nomeNovaMaleta").value.trim();
  const feedback = document.getElementById("feedbackMaleta");
  if (!target || (nova ? !name : !caseId)) { feedback.textContent = "Escolha a revendedora e a maleta de destino."; return; }
  const produtos = [];
  for (const key of selecionados) {
    const [product, user, oldCase] = key.split("-").map(Number);
    const row = document.querySelector(`tr[data-prod-id="${product}"][data-rev-id="${user}"][data-case-id="${oldCase}"]`);
    if (!row) { feedback.textContent = "Atualize a lista e selecione novamente os produtos."; return; }
    produtos.push({produto_id: product, revendedor_id: user, case_id_atual: oldCase, quantidade: Number(row.dataset.quantidade)});
  }
  processandoAcao = true;
  atualizarSelecao();
  const controls = [...document.getElementById("formMoverMaleta").querySelectorAll("input, select, button")];
  const disabled = controls.map(control => control.disabled);
  controls.forEach(control => { control.disabled = true; });
  feedback.textContent = "Movendo produtos...";
  try {
    await api("/api/maleta/adicionar", {method: "POST", headers: {"Content-Type": "application/json"},
      body: JSON.stringify({produtos, revendedora_destino_id: target, nome_maleta: nova ? name : null, case_id: caseId})});
    modalMaleta.hidden = true;
    selecionados.clear();
    tipoClick = null;
    resetarLista();
    await carregarProdutos();
    document.getElementById("maletaClick").focus();
  } catch (error) { feedback.textContent = error.message; }
  finally {
    processandoAcao = false;
    controls.forEach((control, index) => { control.disabled = disabled[index]; });
    atualizarSelecao();
  }
}
document.getElementById("formMoverMaleta").addEventListener("submit", confirmarMaleta);

window.addEventListener("scroll", () => {
  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
    carregarProdutos();
  }
});

function resetarLista() {
  versaoLista++;
  selecionados.clear();
  page = 1;
  acabou = false;
  produtosCache = [];
  document.querySelector("#tabelaRevendedores tbody").innerHTML = "";
  atualizarSelecao();
}

document.getElementById("filtroProduto").addEventListener("input", () => {
  resetarLista();
  carregarProdutos();
});

document.getElementById("filtroRevendedor").addEventListener("input", () => {
  resetarLista();
  carregarProdutos();
});

const sentinela = document.getElementById("sentinela");

const observer = new IntersectionObserver((entries) => {
  if (entries[0].isIntersecting) {
    carregarProdutos();
  }
}, {
  root: null,
  rootMargin: "200px",
  threshold: 0
});

observer.observe(sentinela);


async function excluirLinha(botao) {
  if (processandoAcao) return;
  const linha = botao.closest("tr");
  const prodId = linha.dataset.prodId;
  const revId = linha.dataset.revId;
  const caseId = linha.dataset.caseId;

  if (!confirm("Excluir esse produto?")) return;

  try {
    const response = await fetch("/api/produtosRevendedores/deletar", {
      method: "POST",
      headers: {
        "Content-Type": "application/json", "X-CSRF-Token": csrfToken
      },
      body: JSON.stringify({
        prodId,
        revId,
        caseId: caseId === "sem" ? null : Number(caseId)
      })
    });

    const result = await response.json();

    if (result.success) {
      resetarLista();
      await carregarProdutos();
    } else {
      alert("Erro ao excluir no servidor");
    }

  } catch (erro) {
    console.error(erro);
    alert("Erro na requisição");
  }
}

async function editarQuantidade(botao) {
  if (processandoAcao) return;
  const linha = botao.closest("tr");
  const tdQuantidade = linha.children[3];
  const prodId = linha.dataset.prodId;
  const revId = linha.dataset.revId;
  const caseId = linha.dataset.caseId;

  const valorAtual = tdQuantidade.innerText;
  const novoValor = prompt("Nova quantidade:", valorAtual);

  if (novoValor === null || novoValor === "" || isNaN(novoValor)) {
    alert("Digite um número válido");
    return;
  }

  try {
    const response = await fetch("/api/produtosRevendedores/editar", {
      method: "POST",
      headers: {
        "Content-Type": "application/json", "X-CSRF-Token": csrfToken
      },
      body: JSON.stringify({
        prodId,
        revId,
        caseId: caseId === "sem" ? null : Number(caseId),
        quantidade: parseInt(novoValor)
      })
    });

    const result = await response.json();

    if (result.success) {
      resetarLista();
      await carregarProdutos();
    } else {
      alert(result.message || "Erro ao atualizar no servidor");
    }

  } catch (erro) {
    console.error(erro);
    alert("Erro na requisição");
  }
}

document.getElementById("selectMaleta").addEventListener("change", trocarModoMaleta);
destinoSelect.addEventListener("change", trocarModoMaleta);

async function trocarModoMaleta() {
  const nova = document.getElementById("selectMaleta").value === "nova";
  document.getElementById("campoNovaMaleta").hidden = !nova;
  document.getElementById("campoMaletaExistente").hidden = nova;
  document.getElementById("nomeNovaMaleta").required = nova;
  document.getElementById("selectMaletaExistente").required = !nova;
  const version = ++versaoMaletas;
  if (nova) return;
  const select = document.getElementById("selectMaletaExistente");
  const user = destinoSelect.value;
  select.disabled = true;
  select.replaceChildren(new Option(user ? "Carregando maletas..." : "Escolha a revendedora primeiro", ""));
  if (!user) return;
  try {
    const maletas = await api(`/api/maletas?revendedora_id=${encodeURIComponent(user)}`);
    if (version !== versaoMaletas) return;
    select.replaceChildren(new Option(maletas.length ? "Selecione uma maleta" : "Nenhuma maleta. Escolha criar nova.", ""));
    maletas.forEach(maleta => select.add(new Option(maleta.CaseName + (maleta.UserID === null ? " — atribuir à destinatária" : ""), maleta.CaseID)));
    select.disabled = false;
  } catch (error) {
    if (version !== versaoMaletas) return;
    select.replaceChildren(new Option("Não foi possível carregar as maletas", ""));
    document.getElementById("feedbackMaleta").textContent = error.message;
  }
}

carregarProdutos();
