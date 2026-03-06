<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Produtos - Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/produtos.css" />
</head>
<body>

  <div class="container">
    <div class="card app">

      <aside class="sidebar">
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
          <a href="/paineldecontrole">Painel de Controle</a>
          <a href="/produtos" class="active">Produtos</a>
          <a href="/vendas">Vendas</a>
          <a href="relatorios.php">Relatórios</a>
          <a href="estoque.php">Estoque</a>
          <a href="/controledeusuarios">Controle de Usuários</a>
          <a href="impressoras.php">Impressoras</a>
          <a href="fornecedores.php">Fornecedores</a>
          <a href="revendedores.php">Revendedores</a>
          <a href="cadastroimpressora.php">Cadastrar Impressora</a>
        </nav>
      </aside>

      <main class="main">

        <header class="top">
          <div>
            <h1>Produtos</h1>
            <span class="subtitle">Gerencie seus itens e encontre rapidinho</span>
          </div>

          <div class="top-actions">
            <a href="/novavenda" class="btn-primary">+ Nova venda</a>
            <button type="button" class="btn-primary" onclick="abrirModalAdicionar()">+ Adicionar produto</button>
          </div>
        </header>

        <section class="hero">
          <div class="hero-text">
            <h2>Catálogo</h2>
            <p>Confira todos os produtos disponíveis</p>
          </div>
        </section>

        <section class="filters">
          <div class="filter">
            <label>Buscar</label>
            <input id="q" type="text" placeholder="Ex: brinco, colar, anel..." />
          </div>

          <div class="filter">
            <label>Categoria</label>
            <input type="text" id="cat">
          </div>

          <div class="filter">
            <label>Preço</label>
            <select id="price">
              <option value="all">Qualquer</option>
              <option value="0-50">Até R$ 50</option>
              <option value="50-100">R$ 50 – R$ 100</option>
              <option value="100-200">R$ 100 – R$ 200</option>
              <option value="200+">R$ 200+</option>
            </select>
          </div>

          <div class="filter">
            <label>Ordenar</label>
            <select id="sort">
              <option value="relevancia">Relevância</option>
              <option value="menor">Menor preço</option>
              <option value="maior">Maior preço</option>
              <option value="az">Nome (A-Z)</option>
              <option value="za">Nome (Z-A)</option>
            </select>
          </div>
        </section>

        <section class="grid-wrap">
          <div class="grid-header">
            <p id="count">Mostrando 0 produtos</p>
          </div>

          <div id="grid" class="grid"></div>
        </section>

      </main>

    </div>
  </div>

  <!-- MODAL EDIT -->
  <div id="modalEdit" class="modal hidden">
    <div class="modal-card">
      <div class="modal-header">
        <h2>Editar produto</h2>
        <button type="button" class="modal-close" onclick="fecharModal()">✕</button>
      </div>

      <form id="formEdit" class="modal-form">
        <input type="hidden" id="editId" />

        <label>Nome</label>
        <input type="text" id="editNome" required />

        <label>Preço</label>
        <input type="number" id="editPreco" step="0.01" min="0" required />

        <label>Estoque</label>
        <input type="number" id="editEstoque" min="0" required />

        <label>Dar baixa no estoque</label>
        <div class="baixa-row">
          <input type="number" id="editBaixa" min="1" placeholder="Qtd" />
          <button type="button" class="btn btn-outline" onclick="darBaixaEstoque()">Dar baixa</button>
        </div>

        <div class="form-group">
          <label>Reservar peça</label>

          <div class="row-actions">
            <input type="number" id="qtdReserva" min="1" placeholder="Qtd" class="input"
            />

            <button type="button" id="btnReservar" class="btn btn-outline">Reservar peça</button>
          </div>

        <small class="hint">Dica: use a reserva para separar peças pra clientes ou revendedoras</small>
        </div>

        <label>Foto</label>
        <input type="file" id="editFoto" accept="image/*" />

        <div class="modal-preview">
          <p>Preview:</p>
          <img id="editPreview" alt="Preview da foto" />
        </div>

        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="fecharModal()">Cancelar</button>
          <button type="submit" class="btn">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL ADD -->
  <div id="modalAdd" class="modal hidden">
    <div class="modal-card">
      <div class="modal-header">
        <h2>Adicionar produto</h2>
        <button type="button" class="modal-close" onclick="fecharModalAdicionar()">✕</button>
      </div>

      <form id="formAdd" class="modal-form">
        <label>Nome</label>
        <input type="text" id="addNome" required />

        <label>Categoria</label>
        <input type="text" id="addCategoria" required>

        <label>Preço</label>
        <input type="number" id="addPreco" step="0.01" min="0" required />

        <label>Estoque</label>
        <input type="number" id="addEstoque" min="0" required />

        <label>Foto</label>
        <input type="file" id="addFoto" accept="image/*"/>

        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="fecharModalAdicionar()">Cancelar</button>
          <button type="submit" class="btn">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

<script>
  const q = document.getElementById("q");
  const cat = document.getElementById("cat");
  const price = document.getElementById("price");
  const sort = document.getElementById("sort");
  const grid = document.getElementById("grid");
  const count = document.getElementById("count");
  const main = document.querySelector(".main");

  // edit
  const modalEdit = document.getElementById("modalEdit");
  const formEdit = document.getElementById("formEdit");
  const editId = document.getElementById("editId");
  const editNome = document.getElementById("editNome");
  const editPreco = document.getElementById("editPreco");
  const editEstoque = document.getElementById("editEstoque");
  const editBaixa = document.getElementById("editBaixa");
  const editFoto = document.getElementById("editFoto");
  const editPreview = document.getElementById("editPreview");

  // add
  const modalAdd = document.getElementById("modalAdd");
  const formAdd = document.getElementById("formAdd");
  const addNome = document.getElementById("addNome");
  const addCategoria = document.getElementById("addCategoria");
  const addPreco = document.getElementById("addPreco");
  const addEstoque = document.getElementById("addEstoque");
  const addFoto = document.getElementById("addFoto");

  function inPriceRange(p, range) {
    if (range === "all") return true;
    if (range === "200+") return p >= 200;
    const [min, max] = range.split("-").map(Number);
    return p >= min && p <= max;
  }

  let produtos = [];
  let page = 0;
  let limit = 12;
  let isLoading = false;
  let acabou = false;

  async function render(reset = false) {
    if (isLoading) return;

    if (reset) {
      page = 0;
      acabou = false;
      produtos = [];
      grid.innerHTML = "";
    }

    if (acabou) return;

    isLoading = true;

    const term = q.value.trim().toLowerCase();
    const catVal = cat.value;
    const priceVal = price.value;
    const sortVal = sort.value;

    try {
      const tags = catVal
        .split(",")
        .map(t => t.trim())
        .filter(t => t.length > 0);
      
      const res = await fetch("/api/produtos", {
        method: "POST",
        headers: { "Content-Type": "application/json"},
        body: JSON.stringify({
          text: term,
          tags: tags,
          price: priceVal,
          sort: sortVal,
          page: page,
          limit: limit
        })
      });

      if (!res.ok) throw new Error("Erro na requisição");

      const data = await res.json();
      const novos = data.produtos;
      const total = data.total;

      if (novos.length < limit) acabou = true;

      produtos = [...produtos, ...novos];

      count.textContent = `Mostrando ${total} produto(s)`;

      grid.innerHTML += novos.map(p => `
        <article class="product">
          <div class="thumb">
            <img src="${p.img}" alt="${p.nome}">
            ${p.estoque <= 5 ? `<span class="badge">Baixo estoque</span>` : ``}
          </div>

          <div class="info">
            <h3 title="${p.nome}">${p.nome}</h3>
            <p class="meta">• Estoque: ${p.estoque}</p>
            <div class="price">R$ ${parseFloat(p.preco).toFixed(2).replace(".", ",")}</div>

            <div class="actions">
              <button class="btn btn-editar" type="button" onclick="abrirModal(${p.id})">Editar</button>
              <button class="btn btn-outline" type="button" onclick="formDel(${p.id})">Excluir</button>
            </div>
          </div>
        </article>
      `).join("");

      page++;

    } catch (err) {
      console.error(err);
      if (reset) grid.innerHTML = "<p>Erro ao carregar produtos.</p>";
    }

    isLoading = false;
  }

  main.addEventListener("scroll", () => {
    if (main.scrollTop + main.clientHeight >= main.scrollHeight - 200) {
      render();
    }
  });

  // ===== MODAL EDIT =====
  function abrirModal(id) {
    const prod = produtos.find(p => p.id === id);
    if (!prod) return;

    editId.value = prod.id;
    editNome.value = prod.nome;
    editPreco.value = prod.preco;
    editEstoque.value = prod.estoque;

    editFoto.value = "";
    editBaixa.value = "";
    editPreview.src = prod.img;

    modalEdit.classList.remove("hidden");
  }

  function fecharModal() {
    modalEdit.classList.add("hidden");
  }

  modalEdit.addEventListener("click", (e) => {
    if (e.target === modalEdit) fecharModal();
  });

  // preview da foto (edit)
  editFoto.addEventListener("change", () => {
    const file = editFoto.files && editFoto.files[0];
    if (!file) return;
    editPreview.src = URL.createObjectURL(file);
  });

  function darBaixaEstoque() {
    const id = Number(editId.value);
    const idx = produtos.findIndex(p => p.id === id);
    if (idx === -1) return;

    const atual = Number(editEstoque.value);
    const baixa = Number(editBaixa.value);

    if (!baixa || baixa <= 0) {
      alert("Digite uma quantidade válida pra dar baixa.");
      return;
    }

    if (baixa > atual) {
      alert("Não dá: baixa maior que o estoque.");
      return;
    }

    const novo = atual - baixa;

    editEstoque.value = novo;
    produtos[idx].estoque = novo;

    editBaixa.value = "";
    render();
  }

  function darBaixaEstoque() {
    const id = Number(editId.value);
    const idx = produtos.findIndex(p => p.id === id);
    if (idx === -1) return;

    const atual = Number(editEstoque.value);
    const baixa = Number(editBaixa.value);

    if (!baixa || baixa <= 0) {
      alert("Digite uma quantidade válida pra dar baixa.");
      return;
    }

    if (baixa > atual) {
      alert("Não dá: baixa maior que o estoque.");
      return;
    }

    const novo = atual - baixa;
    
    editEstoque.value = novo;
    produtos[idx].estoque = novo;

    editBaixa.value = "";
    render();
  }

  formEdit.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = Number(editId.value);
    const idx = produtos.findIndex(p => p.id === id);
    if (idx === -1) return;

    produtos[idx].nome = editNome.value.trim();
    produtos[idx].preco = Number(editPreco.value);
    produtos[idx].estoque = Number(editEstoque.value);

    const file = editFoto.files && editFoto.files[0];
    if (file) produtos[idx].img = URL.createObjectURL(file);

    const data = new FormData();
    data.append("id", id);
    data.append("nome", editNome.value.trim());
    data.append("preco", editPreco.value);
    data.append("estoque", editEstoque.value);

    if (editFoto.files[0]) data.append("foto", editFoto.files[0]);

    try {
      const res = await fetch("/api/produtos/update", {
        method: "POST",
        body: data
      });

      if (!res.ok) throw new Error("Erro ao atualizar produto");

      fecharModal();
      render(true);

    } catch (err) {
      console.error(err);
      alert("Não foi possível atualizar o produto.");
    }
  });

  function abrirModalAdicionar() {
    modalAdd.classList.remove("hidden");
  }

  function fecharModalAdicionar() {
    modalAdd.classList.add("hidden");
  }

  modalAdd.addEventListener("click", (e) => {
    if (e.target === modalAdd) fecharModalAdicionar();
  });

  formAdd.addEventListener("submit", async (e) => {
    e.preventDefault();
    const addCategoriaClean = addCategoria.value
        .split(",")
        .map(t => t.trim())
        .filter(t => t.length > 0);

    const data = new FormData();

    addCategoriaClean.forEach(cat => {
      data.append("categoria[]", cat);
    });
    data.append("nome", addNome.value.trim());
    data.append("preco", addPreco.value);
    data.append("estoque", addEstoque.value);
    data.append("foto", addFoto.files[0]);

    const res = await fetch("/api/produtos/add", {
      method: "POST",
      body: data
    });

    if (!res.ok) return console.error("Erro ao enviar pro back");

    formAdd.reset();
    fecharModalAdicionar();
    render(true);
  });

  async function formDel(id) {
  if (!confirm("Deseja realmente excluir este produto?")) return;

  try {
    const res = await fetch(`/api/produtos/delete`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });

    if (!res.ok) throw new Error("Erro ao excluir produto");

    produtos = produtos.filter(p => p.id !== id);
    render(true);

  } catch (err) {
    console.error(err);
    alert("Não foi possível excluir o produto.");
  }
}

window.formDel = formDel;

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    if (!modalEdit.classList.contains("hidden")) fecharModal();
    if (!modalAdd.classList.contains("hidden")) fecharModalAdicionar();
  });

  window.abrirModal = abrirModal;
  window.fecharModal = fecharModal;
  window.abrirModalAdicionar = abrirModalAdicionar;
  window.fecharModalAdicionar = fecharModalAdicionar;
  window.darBaixaEstoque = darBaixaEstoque;

  q.addEventListener("input", () => render(true));
  cat.addEventListener("input", () => render(true));
  price.addEventListener("change", () => render(true));
  sort.addEventListener("change", () => render(true));

  render(true);
</script>

</body>
</html>