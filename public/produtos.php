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
          <a href="produtos.php" class="active">Produtos</a>
          <a href="vendas.php">Vendas</a>
          <a href="relatorios.php">Relatórios</a>
          <a href="estoque.php">Estoque</a>
          <a href="usuarios.php">Controle de Usuários</a>
          <a href="impressoras.php">Impressoras</a>
        </nav>
      </aside>

      <main class="main">

        <header class="top">
          <div>
            <h1>Produtos</h1>
            <span class="subtitle">Gerencie seus itens e encontre rapidinho</span>
          </div>

          <div class="top-actions">
            <a href="novavenda.php" class="btn-primary">+ Nova venda</a>
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
            <select id="cat">
              <option value="all">Todas</option>
              <option value="aneis">Anéis</option>
              <option value="brincos">Brincos</option>
              <option value="colares">Colares</option>
              <option value="pulseiras">Pulseiras</option>
              <option value="conjuntos">Conjuntos</option>
            </select>
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
              <option value="nome">Nome (A-Z)</option>
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
        <select id="addCategoria" required>
          <option value="aneis">Anéis</option>
          <option value="brincos">Brincos</option>
          <option value="colares">Colares</option>
          <option value="pulseiras">Pulseiras</option>
          <option value="conjuntos">Conjuntos</option>
        </select>

        <label>Preço</label>
        <input type="number" id="addPreco" step="0.01" min="0" required />

        <label>Estoque</label>
        <input type="number" id="addEstoque" min="0" required />

        <label>Foto</label>
        <input type="file" id="addFoto" accept="image/*" required />

        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="fecharModalAdicionar()">Cancelar</button>
          <button type="submit" class="btn">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

<script>
  // Produtos fake (troca depois por banco/API)
  const produtos = [
    { id: 1, nome: "Brinco Zircônia Lux", categoria: "brincos", preco: 79.90, estoque: 12, img: "https://picsum.photos/seed/brinco1/600/600" },
    { id: 2, nome: "Colar Ponto de Luz", categoria: "colares", preco: 129.90, estoque: 6, img: "https://picsum.photos/seed/colar1/600/600" },
    { id: 3, nome: "Anel Solitário", categoria: "aneis", preco: 99.90, estoque: 9, img: "https://picsum.photos/seed/anel1/600/600" },
    { id: 4, nome: "Pulseira Elegance", categoria: "pulseiras", preco: 59.90, estoque: 15, img: "https://picsum.photos/seed/pulseira1/600/600" },
    { id: 5, nome: "Conjunto Dourado", categoria: "conjuntos", preco: 219.90, estoque: 4, img: "https://picsum.photos/seed/conjunto1/600/600" },
    { id: 6, nome: "Brinco Argola Premium", categoria: "brincos", preco: 49.90, estoque: 22, img: "https://picsum.photos/seed/brinco2/600/600" },
    { id: 7, nome: "Colar Choker Fashion", categoria: "colares", preco: 89.90, estoque: 7, img: "https://picsum.photos/seed/colar2/600/600" },
    { id: 8, nome: "Anel Ajustável", categoria: "aneis", preco: 39.90, estoque: 30, img: "https://picsum.photos/seed/anel2/600/600" }
  ];

  const q = document.getElementById("q");
  const cat = document.getElementById("cat");
  const price = document.getElementById("price");
  const sort = document.getElementById("sort");
  const grid = document.getElementById("grid");
  const count = document.getElementById("count");

  // edit
  const modalEdit = document.getElementById("modalEdit");
  const formEdit = document.getElementById("formEdit");
  const editId = document.getElementById("editId");
  const editNome = document.getElementById("editNome");
  const editPreco = document.getElementById("editPreco");
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

  function render() {
    const term = q.value.trim().toLowerCase();
    const catVal = cat.value;
    const priceVal = price.value;
    const sortVal = sort.value;

    let list = produtos.filter(p => {
      const matchTerm = p.nome.toLowerCase().includes(term);
      const matchCat = (catVal === "all") ? true : p.categoria === catVal;
      const matchPrice = inPriceRange(p.preco, priceVal);
      return matchTerm && matchCat && matchPrice;
    });

    if (sortVal === "menor") list.sort((a,b) => a.preco - b.preco);
    if (sortVal === "maior") list.sort((a,b) => b.preco - a.preco);
    if (sortVal === "nome") list.sort((a,b) => a.nome.localeCompare(b.nome));

    count.textContent = `Mostrando ${list.length} produto(s)`;

    grid.innerHTML = list.map(p => `
      <article class="product">
        <div class="thumb">
          <img src="${p.img}" alt="${p.nome}">
          ${p.estoque <= 5 ? `<span class="badge">Baixo estoque</span>` : ``}
        </div>

        <div class="info">
          <h3 title="${p.nome}">${p.nome}</h3>
          <p class="meta">${p.categoria.toUpperCase()} • Estoque: ${p.estoque}</p>
          <div class="price">R$ ${p.preco.toFixed(2).replace(".", ",")}</div>

          <div class="actions">
            <button class="btn btn-editar" type="button" onclick="abrirModal(${p.id})">Editar</button>
            <button class="btn btn-outline" type="button" onclick="alert('Aqui liga no backend pra excluir')">Excluir</button>
          </div>
        </div>
      </article>
    `).join("");
  }

  // ===== MODAL EDIT =====
  function abrirModal(id) {
    const prod = produtos.find(p => p.id === id);
    if (!prod) return;

    editId.value = prod.id;
    editNome.value = prod.nome;
    editPreco.value = prod.preco;

    editFoto.value = "";
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

  formEdit.addEventListener("submit", (e) => {
    e.preventDefault();

    const id = Number(editId.value);
    const idx = produtos.findIndex(p => p.id === id);
    if (idx === -1) return;

    produtos[idx].nome = editNome.value.trim();
    produtos[idx].preco = Number(editPreco.value);

    const file = editFoto.files && editFoto.files[0];
    if (file) produtos[idx].img = URL.createObjectURL(file);

    fecharModal();
    render();
  });

  // ===== MODAL ADD =====
  function abrirModalAdicionar() {
    modalAdd.classList.remove("hidden");
  }

  function fecharModalAdicionar() {
    modalAdd.classList.add("hidden");
  }

  modalAdd.addEventListener("click", (e) => {
    if (e.target === modalAdd) fecharModalAdicionar();
  });

  formAdd.addEventListener("submit", (e) => {
    e.preventDefault();

    const nome = addNome.value.trim();
    const categoria = addCategoria.value;
    const preco = Number(addPreco.value);
    const estoque = Number(addEstoque.value);
    const file = addFoto.files && addFoto.files[0];

    if (!nome || !categoria || !file || !(preco >= 0) || !(estoque >= 0)) {
      alert("Preencha tudo certinho.");
      return;
    }

    produtos.push({
      id: Date.now(),
      nome,
      categoria,
      preco,
      estoque,
      img: URL.createObjectURL(file)
    });

    formAdd.reset();
    fecharModalAdicionar();
    render();
  });

  // ===== FECHAR COM ESC (para os dois) =====
  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    if (!modalEdit.classList.contains("hidden")) fecharModal();
    if (!modalAdd.classList.contains("hidden")) fecharModalAdicionar();
  });

  window.abrirModal = abrirModal;
  window.fecharModal = fecharModal;
  window.abrirModalAdicionar = abrirModalAdicionar;
  window.fecharModalAdicionar = fecharModalAdicionar;

  [q, cat, price, sort].forEach(el => el.addEventListener("input", render));
  render();
</script>

</body>
</html>