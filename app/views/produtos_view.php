<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Produtos - Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/produtos.css" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://unpkg.com/cropperjs/dist/cropper.css">
  <script src="https://unpkg.com/cropperjs"></script>
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
          <a href="/impressoras">Impressoras</a>
          
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
            <a href="/relatorios">Relatórios</a>
            <a href="/controledeusuarios">Controle de Revendedores</a>
            <a href="/fornecedores">Fornecedores</a>
            <a href="/cadastrarimpressora">Cadastrar Impressora</a>
            <a href="/produtosrevendedores">Produtos dos Revendedores</a>
          <?php endif; ?>

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

            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
              <button type="button" class="btn-primary" onclick="abrirModalAdicionar()">+ Adicionar produto</button>
              <button type="button" class="btn-primary" onclick="abrirModalEnvio()">Enviar produtos para revendedoras</button>

              <form action="/api/produtos/xml" method="post" enctype="multipart/form-data" class="xml-form">
                <label class="btn-primary file-btn">
                  Escolher XML
                  <input type="file" name="xmlfile" accept=".xml" required hidden>
                </label>
                <button type="submit" class="btn-primary">Importar XML</button>
              </form>
            <?php endif; ?>
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

          <div class="filter">
            <label>Tamanho</label>
            <input type="text" id="tamanho" placeholder="Ex: 12, P, M, G..." />
          </div>

          <div class="filter">
            <label>Cor</label>
            <input type="text" id="cor" placeholder="Ex: Dourado, Prata..." />
          </div>

          <div class="filter">
            <label>Peso do banho</label>
            <input type="text" id="pesoBanho" placeholder="Ex: 5g" />
          </div>

          <div class="filter">
            <label>Milésimos do banho</label>
            <input type="text" id="milesimosBanho" placeholder="Ex: 3" />
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

        <label>Tamanho da peça</label>
        <input type="text" id="editTamanho" placeholder="Ex: 12, 14, P, M, G, Ajustável" />

        <label>Cor</label>
        <input type="text" id="editCor" placeholder="Ex: Dourado, Prata, Rosé" />

        <label>Peso do banho</label>
        <input type="number" id="editPesoBanho" placeholder="Ex: 5g" />

        <label>Milésimos de banho</label>
        <input type="number" id="editMilesimosBanho" placeholder="Ex: 3 milésimos" />

        <label>Dar baixa no estoque</label>
        <div class="baixa-row">
          <input type="number" id="editBaixa" min="1" placeholder="Qtd" />
          <button type="button" class="btn btn-outline" onclick="darBaixaEstoque()">Dar baixa</button>
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
        <input type="text" id="addCategoria">

        <label>Preço</label>
        <input type="number" id="addPreco" step="0.01" min="0" required />

        <label>Estoque</label>
        <input type="number" id="addEstoque" min="0" required />

        <label>Tamanho da peça</label>
        <input type="text" id="addTamanho" placeholder="Ex: 12, 14, P, M, G, Ajustável" />

        <label>Cor</label>
        <input type="text" id="addCor" placeholder="Ex: Dourado, Prata, Rosé" />

        <label>Peso do banho</label>
        <input type="number" id="addPesoBanho" placeholder="Ex: 5g" />

        <label>Milésimos de banho</label>
        <input type="number" id="addMilesimosBanho" placeholder="Ex: 3 milésimos" />

        <label>Foto</label>
        <input type="file" id="addFoto" accept="image/*"/>

        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="fecharModalAdicionar()">Cancelar</button>
          <button type="submit" class="btn">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL ENVIAR PRODUTOS -->
  <div id="modalEnvio" class="modal hidden">
    <div class="modal-card modal-card-envio">
      <div class="modal-header">
        <h2>Enviar produtos para revendedoras</h2>
        <button type="button" class="modal-close" onclick="fecharModalEnvio()">✕</button>
      </div>

      <form id="formEnvio" class="modal-form">
        <label for="revendedoraSelect">Escolha a revendedora</label>
        <select id="revendedoraSelect" required>
          <option value="">Selecione uma revendedora</option>
        </select>

        <label for="buscaEnvio">Buscar produto</label>
        <input type="text" id="buscaEnvio" placeholder="Digite o nome do produto..." />

        <div id="listaProdutosEnvio" class="lista-produtos-envio"></div>

        <div class="resumo-envio">
          <div class="resumo-envio-topo">
            <h3>Produtos selecionados</h3>
            <button type="button" class="btn btn-outline btn-limpar-envio" onclick="limparSelecaoEnvio()">Limpar</button>
          </div>

          <div id="itensSelecionadosEnvio" class="itens-selecionados-envio">
            <p class="envio-vazio">Nenhum produto selecionado.</p>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="fecharModalEnvio()">Cancelar</button>
          <button type="submit" class="btn">Confirmar envio</button>
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

  const tamanho = document.getElementById("tamanho");
  const cor = document.getElementById("cor");
  const pesoBanho = document.getElementById("pesoBanho");
  const milesimosBanho = document.getElementById("milesimosBanho");

  // edit
  const modalEdit = document.getElementById("modalEdit");
  const formEdit = document.getElementById("formEdit");
  const editId = document.getElementById("editId");
  const editNome = document.getElementById("editNome");
  const editPreco = document.getElementById("editPreco");
  const editEstoque = document.getElementById("editEstoque");
  const editTamanho = document.getElementById("editTamanho");
  const editCor = document.getElementById("editCor");
  const editPesoBanho = document.getElementById("editPesoBanho");
  const editMilesimosBanho = document.getElementById("editMilesimosBanho");
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
  const addTamanho = document.getElementById("addTamanho");
  const addCor = document.getElementById("addCor");
  const addPesoBanho = document.getElementById("addPesoBanho");
  const addMilesimosBanho = document.getElementById("addMilesimosBanho");
  const addFoto = document.getElementById("addFoto");

  // envio
  const modalEnvio = document.getElementById("modalEnvio");
  const formEnvio = document.getElementById("formEnvio");
  const revendedoraSelect = document.getElementById("revendedoraSelect");
  const buscaEnvio = document.getElementById("buscaEnvio");
  const listaProdutosEnvio = document.getElementById("listaProdutosEnvio");
  const itensSelecionadosEnvio = document.getElementById("itensSelecionadosEnvio");

  let produtos = [];
  let page = 0;
  let limit = 12;
  let isLoading = false;
  let acabou = false;

  let revendedoras = [];
  let itensEnvio = [];

  const revendedorasMock = [
    { id: 1000000000, nome: "?" }
  ];

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
          tamanho: tamanho.value.trim(),
          cor: cor.value.trim(),
          peso_banho: pesoBanho.value.trim(),
          milesimos_banho: milesimosBanho.value.trim(),
          page: page,
          limit: limit
        })
      });

      if (!res.ok) throw new Error("Erro na requisição");

      const data = await res.json();
      const novos = Array.isArray(data.produtos) ? data.produtos : [];
      const total = data.total ?? 0;

      if (novos.length < limit) acabou = true;

      produtos = [...produtos, ...novos];

      count.textContent = `Mostrando ${total} produto(s)`;

      grid.innerHTML += novos.map(p => `
        <article class="product">
          <div class="thumb">
            <img src="${p.img}" alt="${p.nome}">
            ${Number(p.estoque) <= 3 ? `<span class="badge">Baixo estoque</span>` : ``}
          </div>

          <div class="info">
            <h3 title="${p.nome}">${p.nome}</h3>
            <div class="meta">
              <p>• Estoque: ${p.estoque}</p>
              ${p.tamanho ? `<p>• Tamanho: ${p.tamanho}</p>` : ``}
              ${p.cor ? `<p>• Cor: ${p.cor}</p>` : ``}
              ${p.peso_banho ? `<p>• Peso banho: ${p.peso_banho}</p>` : ``}
              ${p.milesimos_banho ? `<p>• Milésimos: ${p.milesimos_banho}</p>` : ``}
            </div>
            <div class="price">R$ ${parseFloat(p.preco).toFixed(2).replace(".", ",")}</div>

            <div class="actions">
              <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
                <button class="btn btn-editar" type="button" onclick="abrirModal(${p.id})">Editar</button>
              <?php endif; ?>

              <button class="btn btn-outline" type="button" onclick="imprimirEtiqueta(${p.id})">
                Etiqueta
              </button>

              <button class="btn btn-outline" type="button" onclick="formDel(${p.id})">
                Excluir
              </button>
            </div>
          </div>
        </article>
      `).join("");

      page++;
      atualizarListaProdutosEnvio();

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

  function abrirModal(id) {
    const prod = produtos.find(p => Number(p.id) === Number(id));
    if (!prod) return;

    editId.value = prod.id;
    editNome.value = prod.nome || "";
    editPreco.value = prod.preco || "";
    editEstoque.value = prod.estoque || 0;
    editTamanho.value = prod.tamanho || "";
    editCor.value = prod.cor || "";
    editPesoBanho.value = prod.peso_banho || "";
    editMilesimosBanho.value = prod.milesimos_banho || "";

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

  editFoto.addEventListener("change", () => {
    const file = editFoto.files && editFoto.files[0];
    if (!file) return;
    editPreview.src = URL.createObjectURL(file);
  });

  function darBaixaEstoque() {
    const id = Number(editId.value);
    const idx = produtos.findIndex(p => Number(p.id) === id);
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
    render(true);
  }

  formEdit.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = Number(editId.value);
    const idx = produtos.findIndex(p => Number(p.id) === id);
    if (idx === -1) return;

    produtos[idx].nome = editNome.value.trim();
    produtos[idx].preco = Number(editPreco.value);
    produtos[idx].estoque = Number(editEstoque.value);
    produtos[idx].tamanho = editTamanho.value.trim();
    produtos[idx].cor = editCor.value.trim();
    produtos[idx].peso_banho = editPesoBanho.value.trim();
    produtos[idx].milesimos_banho = editMilesimosBanho.value.trim();

    const file = editFoto.files && editFoto.files[0];
    if (file) produtos[idx].img = URL.createObjectURL(file);

    const data = new FormData();
    data.append("id", id);
    data.append("nome", editNome.value.trim());
    data.append("preco", editPreco.value);
    data.append("estoque", editEstoque.value);
    data.append("tamanho", editTamanho.value.trim());
    data.append("cor", editCor.value.trim());
    data.append("peso_banho", editPesoBanho.value.trim());
    data.append("milesimos_banho", editMilesimosBanho.value.trim());

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
    data.append("tamanho", addTamanho.value.trim());
    data.append("cor", addCor.value.trim());
    data.append("peso_banho", addPesoBanho.value.trim());
    data.append("milesimos_banho", addMilesimosBanho.value.trim());

    if (addFoto.files[0]) {
      data.append("foto", addFoto.files[0]);
    }

    const res = await fetch("/api/produtos/add", {
      method: "POST",
      body: data
    });

    if (!res.ok) {
      console.error("Erro ao enviar pro back");
      return;
    }

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

      produtos = produtos.filter(p => Number(p.id) !== Number(id));
      render(true);

    } catch (err) {
      console.error(err);
      alert("Não foi possível excluir o produto.");
    }
  }

  function imprimirEtiqueta(id){
    const prod = produtos.find(p => Number(p.id) === Number(id));
    if(!prod) return;

    const preco = parseFloat(prod.preco).toFixed(2).replace(".", ",");

    const html = `
    <html>
    <head>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode/dist/JsBarcode.all.min.js"><\/script>
    <style>
      @media print {
        @page {
          size: 40mm 35mm;
          margin: 0;
        }

        body {
          margin: 0;
          padding: 0;
        }
      }

      body{
        font-family: Arial;
        text-align:center;
        margin:0;
      }

      .etiqueta{
        width:40mm;
        height:35mm;
        padding:2mm;
        box-sizing:border-box;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
      }

      .nome{
        font-size:8pt;
      }

      .preco{
        font-size:12pt;
        font-weight:bold;
      }

      svg{
        width:100%;
        height:10mm;
      }
    </style>
    </head>

    <body>
      <div class="etiqueta">
        <div class="nome">${prod.nome}</div>
        <div class="preco">R$ ${preco}</div>
        <svg id="barcode"></svg>
      </div>

      <script>
        JsBarcode("#barcode", "${prod.cdb || prod.id}", {
          format:"CODE128",
          width:2,
          height:60,
          displayValue:true
        });

        window.print();
      <\/script>
    </body>
    </html>
    `;

    const win = window.open();
    win.document.write(html);
  }

  async function carregarRevendedoras() {
    try {
      const res = await fetch("/api/produtos/revendedoras");
      if (!res.ok) throw new Error("Erro ao buscar revendedoras");

      const data = await res.json();
      revendedoras = Array.isArray(data) ? data : [];

      if (!revendedoras.length) {
        revendedoras = revendedorasMock;
      }
    } catch (err) {
      console.warn("Usando revendedoras mock no front.");
      revendedoras = revendedorasMock;
    }

    preencherSelectRevendedoras();
  }

  function preencherSelectRevendedoras() {
    revendedoraSelect.innerHTML = `
      <option value="">Selecione uma revendedora</option>
      ${revendedoras.map(r => `<option value="${r.id}">${r.nome}</option>`).join("")}
    `;
  }

  async function abrirModalEnvio() {
    modalEnvio.classList.remove("hidden");

    if (!produtosEnvio.length) {
      await carregarProdutosEnvio();
    }

    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
  }

  function fecharModalEnvio() {
    modalEnvio.classList.add("hidden");
  }

  modalEnvio.addEventListener("click", (e) => {
    if (e.target === modalEnvio) fecharModalEnvio();
  });

  let produtosEnvio = [];

  async function carregarProdutosEnvio() {
    produtosEnvio = [];
    let pageEnvio = 0;
    let acabouEnvio = false;

    while (!acabouEnvio) {
      const res = await fetch("/api/produtos", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          text: "",
          tags: [],
          price: "all",
          sort: "relevancia",
          tamanho: "",
          cor: "",
          peso_banho: "",
          milesimos_banho: "",
          page: pageEnvio,
          limit: 100
        })
      });

      if (!res.ok) break;

      const data = await res.json();
      const novos = Array.isArray(data.produtos) ? data.produtos : [];

      produtosEnvio.push(...novos);

      if (novos.length < 100) {
        acabouEnvio = true;
      } else {
        pageEnvio++;
      }
    }
  }

  function atualizarListaProdutosEnvio() {
    const termo = (buscaEnvio.value || "").trim().toLowerCase();

    const filtrados = produtosEnvio.filter(p => {
      const nome = (p.nome || "").toLowerCase();
      return nome.includes(termo);
    });

    if (!filtrados.length) {
      listaProdutosEnvio.innerHTML = `<p class="envio-vazio">Nenhum produto encontrado.</p>`;
      return;
    }

    listaProdutosEnvio.innerHTML = filtrados.map(p => {
      const jaSelecionado = itensEnvio.find(item => Number(item.id) === Number(p.id));
      const qtdAtual = jaSelecionado ? jaSelecionado.quantidade : 1;

      return `
        <div class="produto-envio-item">
          <div class="produto-envio-left">
            <img src="${p.img}" alt="${p.nome}">
            <div>
              <strong>${p.nome}</strong>
              <span>Estoque disponível: ${p.estoque}</span>
            </div>
          </div>

          <div class="produto-envio-right">
            <input
              type="number"
              min="1"
              max="${p.estoque}"
              value="${qtdAtual}"
              id="qtd-envio-${p.id}"
              ${Number(p.estoque) <= 0 ? "disabled" : ""}
            />

            <button
              type="button"
              class="btn ${jaSelecionado ? "btn-outline" : ""}"
              onclick="toggleProdutoEnvio(${p.id})"
              ${Number(p.estoque) <= 0 ? "disabled" : ""}
            >
              ${jaSelecionado ? "Remover" : "Adicionar"}
            </button>
          </div>
        </div>
      `;
    }).join("");
  }

  function toggleProdutoEnvio(id) {
    const prod = produtosEnvio.find(p => Number(p.id) === Number(id));
    if (!prod) return;

    const idx = itensEnvio.findIndex(item => Number(item.id) === Number(id));

    if (idx >= 0) {
      itensEnvio.splice(idx, 1);
    } else {
      const inputQtd = document.getElementById(`qtd-envio-${id}`);
      const quantidade = Number(inputQtd?.value || 1);

      if (!quantidade || quantidade <= 0) {
        alert("Digite uma quantidade válida.");
        return;
      }

      if (quantidade > Number(prod.estoque)) {
        alert("A quantidade não pode ser maior que o estoque.");
        return;
      }

      itensEnvio.push({
        id: prod.id,
        nome: prod.nome,
        quantidade,
        estoque: prod.estoque
      });
    }

    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
  }

  function atualizarResumoEnvio() {
    if (!itensEnvio.length) {
      itensSelecionadosEnvio.innerHTML = `<p class="envio-vazio">Nenhum produto selecionado.</p>`;
      return;
    }

    itensSelecionadosEnvio.innerHTML = itensEnvio.map(item => `
      <div class="item-selecionado-envio">
        <div>
          <strong>${item.nome}</strong>
          <span>Quantidade: ${item.quantidade}</span>
        </div>
        <button type="button" class="btn btn-outline" onclick="removerItemEnvio(${item.id})">Remover</button>
      </div>
    `).join("");
  }

  function removerItemEnvio(id) {
    itensEnvio = itensEnvio.filter(item => Number(item.id) !== Number(id));
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
  }

  function limparSelecaoEnvio() {
    itensEnvio = [];
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
  }

  formEnvio.addEventListener("submit", async (e) => {
    e.preventDefault();

    const revendedoraId = revendedoraSelect.value;

    if (!revendedoraId) {
      alert("Selecione uma revendedora.");
      return;
    }

    if (!itensEnvio.length) {
      alert("Selecione pelo menos um produto.");
      return;
    }

    const payload = {
      revendedora_id: revendedoraId,
      produtos: itensEnvio.map(item => ({
        produto_id: item.id,
        quantidade: item.quantidade
      }))
    };

    try {
      const res = await fetch("/api/produtos/envios_revendedoras", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      if (!res.ok) throw new Error("Erro ao enviar");
      formEnvio.reset();
      itensEnvio = [];
      atualizarListaProdutosEnvio();
      atualizarResumoEnvio();
      fecharModalEnvio();

    } catch (err) {
      formEnvio.reset();
      itensEnvio = [];
      atualizarListaProdutosEnvio();
      atualizarResumoEnvio();
      fecharModalEnvio();
    }
  });

  buscaEnvio.addEventListener("input", atualizarListaProdutosEnvio);

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    if (!modalEdit.classList.contains("hidden")) fecharModal();
    if (!modalAdd.classList.contains("hidden")) fecharModalAdicionar();
    if (!modalEnvio.classList.contains("hidden")) fecharModalEnvio();
  });

  window.formDel = formDel;
  window.abrirModal = abrirModal;
  window.fecharModal = fecharModal;
  window.abrirModalAdicionar = abrirModalAdicionar;
  window.fecharModalAdicionar = fecharModalAdicionar;
  window.darBaixaEstoque = darBaixaEstoque;
  window.abrirModalEnvio = abrirModalEnvio;
  window.fecharModalEnvio = fecharModalEnvio;
  window.toggleProdutoEnvio = toggleProdutoEnvio;
  window.removerItemEnvio = removerItemEnvio;
  window.limparSelecaoEnvio = limparSelecaoEnvio;

  q.addEventListener("input", () => render(true));
  cat.addEventListener("input", () => render(true));
  price.addEventListener("change", () => render(true));
  sort.addEventListener("change", () => render(true));
  tamanho.addEventListener("input", () => render(true));
  cor.addEventListener("input", () => render(true));
  pesoBanho.addEventListener("input", () => render(true));
  milesimosBanho.addEventListener("input", () => render(true));

  carregarRevendedoras();
  render(true);
</script>

<div id="modalCrop" class="modal hidden">
  <div class="modal-card">
    <div class="modal-header">
      <h2>Ajustar imagem</h2>
      <button class="modal-close" onclick="fecharCrop()">✕</button>
    </div>

    <img id="cropImage" style="width:100%; max-height:400px;">

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="fecharCrop()">Cancelar</button>
      <button class="btn" onclick="confirmarCrop()">Confirmar</button>
    </div>
  </div>
</div>

</body>
</html>