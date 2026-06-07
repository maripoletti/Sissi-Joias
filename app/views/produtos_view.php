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

      <aside class="sidebar"></aside>

      <main class="main">

        <header class="top">
          <div>
            <h1>Produtos</h1>
            <span class="subtitle">Gerencie seus itens</span>
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

        <!-- FOTO COM PREVIEW -->
        <label>Foto</label>
        <input type="file" id="addFoto" accept="image/*" />

        <div class="modal-preview">
          <p>Preview:</p>
          <img id="addPreview" alt="Preview da foto" />
        </div>

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

  <div id="modalCrop" class="modal hidden">
    <div class="modal-card">
      <div class="modal-header">
        <h2>Ajustar imagem</h2>
        <button type="button" class="modal-close" onclick="fecharCrop()">✕</button>
      </div>

      <div class="crop-container">
        <img id="cropImage" alt="Imagem para recorte" style="width:100%; max-height:400px;">
      </div>

      <div class="modal-actions">
        <button type="button" class="btn btn-outline" onclick="fecharCrop()">Cancelar</button>
        <button type="button" class="btn" onclick="confirmarCrop()">Confirmar</button>
      </div>
    </div>
  </div>

  <div id="modalCrop" class="modal hidden">
    <div class="modal-card">
      <div class="modal-header">
        <h2>Ajustar imagem</h2>
        <button type="button" class="modal-close" onclick="fecharCrop()">✕</button>
      </div>

      <div class="crop-container">
        <img id="cropImage" alt="Imagem para recorte">
      </div>

      <div class="modal-actions">
        <button type="button" class="btn btn-outline" onclick="fecharCrop()">Cancelar</button>
        <button type="button" class="btn" onclick="confirmarCrop()">Confirmar</button>
      </div>
    </div>
  </div>
  <script src="js/produtos.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>