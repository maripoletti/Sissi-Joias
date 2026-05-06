let page = 1;
let limit = 50;
let loading = false;
let acabou = false;

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

  dados.forEach(item => {
    const tr = document.createElement("tr");

    // 🔥 ID para backend
    tr.dataset.id = item.id;

    tr.innerHTML = `
      <td>${item.produto}</td>
      <td>${item.revendedor}</td>
      <td>${item.quantidade}</td>
      <td>R$ ${parseFloat(item.preco_revenda).toFixed(2)}</td>
      <td>${formatarData(item.data_envio)}</td>
      <td class="acoes">
        <button class="btn-editar" onclick="editarQuantidade(this)">✏️</button>
        <button class="btn-excluir" onclick="excluirLinha(this)">🗑️</button>
      </td>
    `;

    tbody.appendChild(tr);
  });
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


// 🗑️ EXCLUIR COM BACKEND
async function excluirLinha(botao) {
  const linha = botao.closest("tr");
  const id = linha.dataset.id;

  if (!confirm("Excluir esse produto?")) return;

  try {
    const response = await fetch("/api/excluirProdutoRevendedor", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
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
  const tdQuantidade = linha.children[2];
  const id = linha.dataset.id;

  const valorAtual = tdQuantidade.innerText;
  const novoValor = prompt("Nova quantidade:", valorAtual);

  if (novoValor === null || novoValor === "" || isNaN(novoValor)) {
    alert("Digite um número válido");
    return;
  }

  try {
    const response = await fetch("/api/editarQuantidadeProdutoRevendedor", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        id,
        quantidade: parseInt(novoValor)
      })
    });

    const result = await response.json();

    if (result.success) {
      tdQuantidade.innerText = novoValor;
    } else {
      alert("Erro ao atualizar no servidor");
    }

  } catch (erro) {
    console.error(erro);
    alert("Erro na requisição");
  }
}

carregarProdutos();