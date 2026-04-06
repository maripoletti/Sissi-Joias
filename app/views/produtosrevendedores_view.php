<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Produtos dos Revendedores</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/produtosrevendedores.css">
</head>

<body>

<div class="container">
  <div class="card app">

    <aside class="sidebar">
      <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
        <a href="/paineldecontrole" class="active">Painel de Controle</a>
        <a href="/produtos">Produtos</a>
        <a href="/vendas">Vendas</a>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
          <a href="/relatorios">Relatórios</a>
          <a href="/controledeusuarios">Controle de Revendedores</a>
          <a href="/fornecedores">Fornecedores</a>
          <a href="/cadastrarimpressora">Cadastrar Impressora</a>
          <a href="/produtosrevendedores">Produtos dos Revendedores</a>
          <a href="/impressoras">Impressoras</a>
        <?php endif; ?>
      </nav>
    </aside>

    <main class="main">
      <header class="top">
        <h1>Produtos dos Revendedores</h1>
      </header>

      <div class="filtros">
        <input type="text" id="filtroProduto" placeholder="Buscar produto...">
        <input type="text" id="filtroRevendedor" placeholder="Buscar revendedor...">
      </div>

      <div class="tabela-container">
        <table id="tabelaRevendedores">
          <thead>
            <tr>
              <th>Produto</th>
              <th>Revendedor</th>
              <th>Quantidade</th>
              <th>Preço</th>
              <th>Status</th>
              <th>Data</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </main>
  </div>
</div>

<script src="produtosrevendedores.js"></script>

</body>
</html>