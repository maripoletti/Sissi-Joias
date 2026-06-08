<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios - Relatórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/relatorios.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
  <div class="container">
    <div class="card paineldecontrole">

      <aside class="sidebar"></aside>

      <main class="main relatorios-page">

        <header class="rel-top">
          <h1>Relatórios</h1>
        </header>

        <!-- Cards topo -->
        <section class="rel-cards">
          <div class="rel-card">
            <p class="rel-label">Total</p>
            <h2 class="rel-value">R$ 0,00</h2>
            <div class="rel-badge">$</div>
          </div>

          <div class="rel-card">
            <p class="rel-label">Qtd vendas</p>
            <h2 class="rel-value">0</h2>
            <div class="rel-badge">🧾</div>
          </div>

          <div class="rel-card">
            <p class="rel-label">Valor médio</p>
            <h2 class="rel-value">R$ 0,00</h2>
            <div class="rel-badge">📈</div>
          </div>

          <div class="rel-card">
            <p class="rel-label">Vendedoras ativas</p>
            <h2 class="rel-value">0</h2>
            <div class="rel-badge">👤</div>
          </div>
        </section>

        <section class="estoque-stats">

          <div class="stat-card">
            <div class="stat-icon gold">💎</div>
            <div class="stat-value" id="totalPecas">0</div>
            <div class="stat-label">Total de peças</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon lilac">⬡</div>
            <div class="stat-value" id="totalUnidades">0</div>
            <div class="stat-label">Unidades em estoque</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon red">⚠</div>
            <div class="stat-value" id="totalAlertas">0</div>
            <div class="stat-label">Alertas de estoque</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon gold">↗</div>
            <div class="stat-value" id="valorEstoque">R$ 0,00</div>
            <div class="stat-label">Valor em estoque</div>
          </div>

        </section>


        <!-- Blocos do meio -->
        <section class="rel-grid">

          <!-- Produtos mais vendidos -->
          <div class="rel-box">
            <h3>Produtos mais vendidos</h3>

            <div class="rel-bars">
              
            </div>
          </div>

          <!-- Vendas por vendedora -->
          <div class="rel-box">
            <h3>Vendas por vendedora</h3>

            <div class="rel-sellers">


            </div>
          </div>

        </section>

        <!-- Pagamentos -->
        <section class="rel-box rel-pay">
          <h3>Por forma de pagamento</h3>

          <div class="rel-pay-grid">
            
          </div>
        </section>

      </main>

    </div>
  </div>
  <script src="js/relatorios.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>