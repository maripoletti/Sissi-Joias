let page = 1;
let limit = 2000;
let loading = false;
let acabou = false;
let tipoClick = null;

async function carregarProdutos() {
  if (loading || acabou) return;

  loading = true;

  try {
    const produto = document.getElementById("filtroProduto").value.trim();
    const revendedor = document.getElementById("filtroRevendedor").value.trim();

    const response = await fetch("/api/produtosRevendedores", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        filters: {
          produto: produto || null,
          revendedor: revendedor || null
        },
        pagination: {
          page,
          limit
        }
      })
    });

    const dados = await response.json();

    if (dados.length < limit) {
      acabou = true;
    }

    renderizarTabela(dados, true);
    page++;

  } catch (e) {
    console.error(e);
  }

  loading = false;
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
    const caseID = item.CaseID ?? "sem";

    if (!grupos[caseID]) {
      grupos[caseID] = {
        nome: item.CaseName ?? "Sem maleta",
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
        <strong>${grupo.nome}</strong>
        <span style="float:right;">R$ ${grupo.total.toFixed(2)}</span>
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
      tr.dataset.caseId = caseID;

      tr.addEventListener("click", (e) => {

        if (e.target.closest(".acoes")) return;

        const checkbox = tr.querySelector('input[type="checkbox"]');

        const td = checkbox.closest(".selecao");
        if (!td || td.style.display === "none") return;

        checkbox.checked = !checkbox.checked;

        toggleCheckbox(
          checkbox,
          item.ProdId,
          item.RevId
        );
      });

      tr.innerHTML = `
        <td class="selecao" style="display:none">
          <input
            type="checkbox"
            onclick="event.stopPropagation()"
            onchange="toggleCheckbox(this, '${item.ProdId}', '${item.RevId}')"
          >
        </td>

        <td>${item.produto}</td>
        <td>${item.revendedor}</td>
        <td>${item.quantidade}</td>
        <td>R$ ${parseFloat(item.preco_revenda).toFixed(2)}</td>
        <td>${formatarData(item.data_envio)}</td>

        <td class="acoes">
          <button onclick="editarQuantidade(this)">✏️</button>
          <button onclick="excluirLinha(this)">🗑️</button>
        </td>
      `;

      tbody.appendChild(tr);
    });
  });
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

function ativarMaletaClick() {
  tipoClick = "maleta";

  mostrarCheckboxes();

  const btn = document.getElementById("prosseguirBtn");
  btn.style.display = "inline-block";

  const cancelar = document.getElementById("cancelarBtn");
  cancelar.style.display = "inline-block";

  btn.classList.remove("prosseguir-excluir");
  btn.classList.add("prosseguir-maleta");
}

function ativarExcluirClick() {
  tipoClick = "excluir";

  mostrarCheckboxes();

  const btn = document.getElementById("prosseguirBtn");
  btn.style.display = "inline-block";

  const cancelar = document.getElementById("cancelarBtn");
  cancelar.style.display = "inline-block";

  btn.classList.remove("prosseguir-maleta");
  btn.classList.add("prosseguir-excluir");
}

function cancelarSelecao() {
  selecionados.clear();

  document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
    cb.checked = false;
  });

  document.querySelectorAll(".selecao").forEach(td => {
    td.style.display = "none";
  });

  document.querySelector(".col-selecao").style.display = "none";

  document.getElementById("cancelarBtn").style.display = "none";
  document.getElementById("prosseguirBtn").style.display = "none";
  document.getElementById("prosseguirBtn").classList.remove(
    "prosseguir-maleta",
    "prosseguir-excluir"
  );

  tipoClick = null;
}

function mostrarCheckboxes() {

  document.querySelector(".col-selecao").style.display = "";

  document.querySelectorAll(".selecao").forEach(td => {
      td.style.display = "";
    });
}

const selecionados = new Set();

function toggleCheckbox(checkbox, prodId, revId) {
  const key = `${prodId}-${revId}`;

  if (checkbox.checked) {
    selecionados.add(key);
  } else {
    selecionados.delete(key);
  }
}

function fecharModalMaleta() {
    document.getElementById("modalMaleta").style.display = "none";
}

document.querySelectorAll(".modal").forEach(modal => {
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            fecharModalMaleta();
        };
    });
});

function prosseguirAcao() {

  if (selecionados.size === 0) {
    alert("Selecione pelo menos um produto.");
    return;
  }

  if (tipoClick === "maleta") {
    document.getElementById("modalMaleta").style.display = "block";
  }

  if (tipoClick === "excluir") {
    excluirEmMassa();
  }
}

document.querySelector("#prosseguirBtn").addEventListener("click", prosseguirAcao);

async function excluirEmMassa() {

  const quantidade = selecionados.size;

  const confirmar = confirm(
    `Deseja excluir ${quantidade} produto(s)?`
  );

  if (!confirmar) return;

  const itens = [];

  selecionados.forEach(key => {
    const [prodId, revId] = key.split("-");

    itens.push({
      prodId: Number(prodId),
      revId: Number(revId)
    });
  });

  try {

    const response = await fetch(
      "/api/produtosRevendedores/deletarEmMassa",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ itens })
      }
    );

    const result = await response.json();

    if (result.success) {

      selecionados.forEach(key => {

        const [prodId, revId] = key.split("-");

        const linha = document.querySelector(
          `tr[data-prod-id="${prodId}"][data-rev-id="${revId}"]`
        );

        linha?.remove();
      });

      selecionados.clear();

    } else {
      alert("Erro ao excluir.");
    }

  } catch (erro) {
    console.error(erro);
  }
}
  
async function confirmarMaleta() {
  const modo =
    document.getElementById("selectMaleta").value;

  let nomeNova = null;
  let caseId = null;

  if (modo === "nova") {

    nomeNova =
      document.getElementById("nomeNovaMaleta")
      .value
      .trim();

    if (!nomeNova) {
      alert("Digite o nome da maleta.");
      return;
    }

  } else {

    caseId = Number(
      document.getElementById(
        "selectMaletaExistente"
      ).value
    );

  }

  const produtos = [];

  selecionados.forEach(key => {

    const [prodId, revId] = key.split("-");

    const linha = document.querySelector(
      `tr[data-prod-id="${prodId}"][data-rev-id="${revId}"]`
    );

    produtos.push({
      produto_id: Number(prodId),
      revendedor_id: Number(revId),
      quantidade: Number(linha.dataset.quantidade)
    });

  });

  const payload = {
    produtos,
    nome_maleta: nomeNova,
    case_id: caseId
  };

  const res = await fetch("/api/maleta/adicionar", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  });

  const data = await res.json();

  if (data.success) {
    selecionados.clear();
    location.reload();
  } else {
    alert("Erro ao processar");
  }
}

window.addEventListener("scroll", () => {
  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
    carregarProdutos();
  }
});

function resetarLista() {
  page = 1;
  acabou = false;
  document.querySelector("#tabelaRevendedores tbody").innerHTML = "";
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
  const linha = botao.closest("tr");
  const prodId = linha.dataset.prodId;
  const revId = linha.dataset.revId;

  if (!confirm("Excluir esse produto?")) return;

  try {
    const response = await fetch("/api/produtosRevendedores/deletar", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ prodId, revId })
    });

    const result = await response.json();

    if (result.success) {
      linha.remove();
    } else {
      alert("Erro ao excluir no servidor");
    }

  } catch (erro) {
    console.error(erro);
    alert("Erro na requisição");
  }
}

async function editarQuantidade(botao) {
  const linha = botao.closest("tr");
  const tdQuantidade = linha.children[3];
  const prodId = linha.dataset.prodId;
  const revId = linha.dataset.revId;

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
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        prodId,
        revId,
        quantidade: parseInt(novoValor)
      })
    });

    const result = await response.json();

    if (result.success) {
      tdQuantidade.innerText = novoValor;
      linha.dataset.quantidade = novoValor;
    } else {
      alert("Erro ao atualizar no servidor");
    }

  } catch (erro) {
    console.error(erro);
    alert("Erro na requisição");
  }
}

document.getElementById("selectMaleta").addEventListener("change", trocarModoMaleta);


async function trocarModoMaleta() {

  const modo = document.getElementById("selectMaleta").value;

  const inputNova = document.getElementById("nomeNovaMaleta");
  const selectExistente = document.getElementById("selectMaletaExistente");

  if (modo === "nova") {

    inputNova.style.display = "";
    selectExistente.style.display = "none";

    return;
  }

  inputNova.style.display = "none";
  selectExistente.style.display = "";

  await carregarMaletas();
}



async function carregarMaletas() {
  const select = document.getElementById(
    "selectMaletaExistente"
  );

  if (select.dataset.loaded === "true") {
    return;
  }

  const response = await fetch("/api/maletas");

  const maletas = await response.json();

  select.innerHTML = "";

  maletas.forEach(maleta => {

    select.innerHTML += `
      <option value="${maleta.CaseID}">
        ${maleta.CaseName}
      </option>
    `;

  });

  select.dataset.loaded = "true";
}

carregarProdutos();