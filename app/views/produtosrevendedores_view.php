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
      <aside class="sidebar"></aside>

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
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>

          <div id="sentinela"></div>
        </div>

      </main>
    </div>
  </div>

  <script src="js/produtosrevendedores.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>