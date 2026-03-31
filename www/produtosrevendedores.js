let dadosGlobais = [];

async function carregarProdutos() {
  try {
    const response = await fetch("/api/produtosrevendedores_view.php");

    if (!response.ok) {
      throw new Error("Erro ao buscar dados");
    }

    const dados = await response.json();
    dadosGlobais = dados;

    renderizarTabela(dados);

  } catch (erro) {
    console.error("Erro:", erro);
  }
}

function renderizarTabela(dados) {
  const tbody = document.querySelector("#tabelaRevendedores tbody");
  tbody.innerHTML = "";

  dados.forEach(item => {

    let classeStatus = "status-ativo";
    if (item.status === "vendido") classeStatus = "status-vendido";
    if (item.status === "devolvido") classeStatus = "status-devolvido";

    const tr = document.createElement("tr");

    tr.innerHTML = `
      <td>${item.produto}</td>
      <td>${item.revendedor}</td>
      <td>${item.quantidade}</td>
      <td>R$ ${parseFloat(item.preco_revenda).toFixed(2)}</td>
      <td><span class="status ${classeStatus}">${item.status}</span></td>
      <td>${formatarData(item.data_envio)}</td>
    `;

    tbody.appendChild(tr);
  });
}

function formatarData(data) {
  const d = new Date(data);
  return d.toLocaleDateString("pt-BR");
}

document.getElementById("filtroProduto").addEventListener("input", filtrar);
document.getElementById("filtroRevendedor").addEventListener("input", filtrar);

function filtrar() {
  const produto = document.getElementById("filtroProduto").value.toLowerCase();
  const revendedor = document.getElementById("filtroRevendedor").value.toLowerCase();

  const filtrados = dadosGlobais.filter(item =>
    item.produto.toLowerCase().includes(produto) &&
    item.revendedor.toLowerCase().includes(revendedor)
  );

  renderizarTabela(filtrados);
}

carregarProdutos();

setInterval(carregarProdutos, 10000);