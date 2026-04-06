<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fornecedores - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/produtos.css">
  <link rel="stylesheet" href="styles/fornecedores.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="container">
    <div class="card app">

        <aside class="sidebar">
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
            <a href="/paineldecontrole">Painel de Controle</a>
            <a href="/produtos">Produtos</a>
            <a href="/vendas">Vendas</a>
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
              <a href="/impressoras">Impressoras</a>
              <a href="/relatorios">Relatórios</a>
              <a href="/controledeusuarios">Controle de Revendedores</a>
              <a href="/fornecedores" class="active">Fornecedores</a>
              <a href="/cadastrarimpressora">Cadastrar Impressora</a>
              <a href="/produtosrevendedores">Produtos dos Revendedores</a>
              <a href="/impressoras">Impressoras</a>
            <?php endif; ?>

        </nav>
        </aside>

      <main class="main fornecedores-page">
        <div class="page-header">
          <div>
            <h1>Fornecedores</h1>
            <p>Gerencie seus fornecedores e parceiros</p>
          </div>

          <button class="btn-novo" id="btnNovoFornecedor">
            + Novo Fornecedor
          </button>
        </div>

        <div class="toolbar">
          <div class="search-box">
            <input type="text" id="buscaFornecedor" placeholder="Buscar fornecedor..." />
          </div>
        </div>


        <section class="fornecedores-grid" id="listaFornecedores">



        </section>
      </main>
    </div>
  </div>

   
  <div id="modalFornecedor" class="modal-overlay">
    <div class="modal-fornecedor">
      <div class="modal-header">
        <h2>Fornecedor</h2>
        <button class="modal-close" onclick="fecharModal()">×</button>
      </div>
      <div class="form-grid">
        <input type="hidden" id="editId">
        <div class="form-group">
          <label>Nome</label>
          <input id="nomeFornecedor" type="text" required>
        </div>
        <div class="form-group">
          <label>CNPJ</label>
          <input type="text" id="cnpjFornecedor" maxlength="18" placeholder="00.000.000/0000-00">
        </div>
        <div class="form-group">
          <label>Telefone</label>
          <input type="tel" id="telefoneFornecedor" placeholder="(00) 00000-0000">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="emailFornecedor" placeholder="email@exemplo.com">
        </div>
        <div class="form-group full">
          <label>Endereço</label>
          <input type="text" id="enderecoFornecedor">
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
        <button class="btn-salvar" onclick="salvarFornecedor()">Salvar</button>
      </div>
    </div>
  </div>

  <script src="fornecedores.js"></script>
</body>
</html>