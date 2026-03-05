<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios - Relatórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
  <link rel="stylesheet" href="styles/relatorios.css">
</head>
<body>

<div class="container">
  <div class="card paineldecontrole">

    <aside class="sidebar">
      <h2>Sissi Semi Joias e Acessórios</h2>
      <nav>
        <a href="/paineldecontrole">Painel de Controle</a>
        <a href="produtos.php">Produtos</a>
        <a href="vendas.php">Vendas</a>
        <a href="relatorios.php" class="active">Relatórios</a>
        <a href="estoque.php">Estoque</a>
        <a href="controledeusuarios.php">Controle de Usuários</a>
        <a href="impressoras.php">Impressoras</a>
        <a href="fornecedores.php">Fornecedores</a>
        <a href="revendedores.php">Revendedores</a>
      </nav>
    </aside>

    <main class="main relatorios-page">

      <header class="rel-top">
        <h1>Relatórios</h1>

        <button class="rel-mes">
          março de 2026
          <span class="rel-icon">📅</span>
        </button>
      </header>

      <!-- Cards topo -->
      <section class="rel-cards">
        <div class="rel-card">
          <p class="rel-label">Total do mês</p>
          <h2 class="rel-value">R$ 1588,00</h2>
          <div class="rel-badge">$</div>
        </div>

        <div class="rel-card">
          <p class="rel-label">Qtd vendas</p>
          <h2 class="rel-value">7</h2>
          <div class="rel-badge">🧾</div>
        </div>

        <div class="rel-card">
          <p class="rel-label">Ticket médio</p>
          <h2 class="rel-value">R$ 226,86</h2>
          <div class="rel-badge">📈</div>
        </div>

        <div class="rel-card">
          <p class="rel-label">Vendedoras ativas</p>
          <h2 class="rel-value">3</h2>
          <div class="rel-badge">👤</div>
        </div>
      </section>

      <!-- Blocos do meio -->
      <section class="rel-grid">

        <!-- Produtos mais vendidos -->
        <div class="rel-box">
          <h3>Produtos mais vendidos</h3>

          <div class="rel-bars">
            <div class="rel-bar">
              <span>Anel Solitário</span>
              <div class="rel-track">
                <div class="rel-fill roxo" style="width:78%"></div>
              </div>
            </div>

            <div class="rel-bar">
              <span>Conjunto Per</span>
              <div class="rel-track">
                <div class="rel-fill dourado" style="width:58%"></div>
              </div>
            </div>

            <div class="rel-bar">
              <span>Pulseira Ten</span>
              <div class="rel-track">
                <div class="rel-fill roxo" style="width:50%"></div>
              </div>
            </div>

            <div class="rel-bar">
              <span>Brinco Argol</span>
              <div class="rel-track">
                <div class="rel-fill dourado" style="width:45%"></div>
              </div>
            </div>

            <div class="rel-bar">
              <span>Colar Coração</span>
              <div class="rel-track">
                <div class="rel-fill roxo" style="width:32%"></div>
              </div>
            </div>

            <div class="rel-bar">
              <span>Colar Pérola</span>
              <div class="rel-track">
                <div class="rel-fill dourado" style="width:25%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vendas por vendedora -->
        <div class="rel-box">
          <h3>Vendas por vendedora</h3>

          <div class="rel-sellers">

            <div class="seller">
              <div class="rank gold">1</div>
              <div class="info">
                <strong>Ana Paula</strong>
                <div class="seller-track">
                  <div class="seller-fill gold" style="width:82%"></div>
                </div>
              </div>
              <div class="money">R$ 688,00</div>
            </div>

            <div class="seller">
              <div class="rank purple">2</div>
              <div class="info">
                <strong>Fernanda Lima</strong>
                <div class="seller-track">
                  <div class="seller-fill purple" style="width:72%"></div>
                </div>
              </div>
              <div class="money">R$ 610,00</div>
            </div>

            <div class="seller">
              <div class="rank purple2">3</div>
              <div class="info">
                <strong>Beatriz Mendes</strong>
                <div class="seller-track">
                  <div class="seller-fill purple2" style="width:38%"></div>
                </div>
              </div>
              <div class="money">R$ 290,00</div>
            </div>

          </div>
        </div>

      </section>

      <!-- Pagamentos -->
      <section class="rel-box rel-pay">
        <h3>Por forma de pagamento</h3>

        <div class="rel-pay-grid">
          <div class="pay-card">
            <span class="pay-title">PIX</span>
            <h4>R$ 800</h4>
            <p>50% do total</p>
          </div>

          <div class="pay-card">
            <span class="pay-title">Crédito</span>
            <h4>R$ 370</h4>
            <p>23% do total</p>
          </div>

          <div class="pay-card">
            <span class="pay-title">Débito</span>
            <h4>R$ 240</h4>
            <p>15% do total</p>
          </div>

          <div class="pay-card">
            <span class="pay-title">Dinheiro</span>
            <h4>R$ 178</h4>
            <p>11% do total</p>
          </div>
        </div>
      </section>

    </main>

  </div>
</div>

</body>
</html>