<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/dashboard.css">
</head>
<body>

  <div class="container">
    <div class="card dashboard">

      <aside class="sidebar">
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
          <a class="active">Painel de Controle</a>
          <a>Produtos</a>
          <a>Vendas</a>
          <a>Relatórios</a>
          <a>Estoque</a>
          <a>Dar Baixa em Produtos</a>
          <a>Cadastro</a>
          <a>Controle de Usuários</a>
        </nav>
      </aside>

      <main class="main">
        <header class="top">
          <div>
            <h1>Painel de Controle</h1>
            <span id="data-atual"></span>
          </div>
          <button class="btn">Nova Venda</button>
        </header>

        <section class="cards">
          <div class="box">Vendas Hoje<br><strong>R$ 0,00</strong></div>
          <div class="box">Vendas do Mês<br><strong>R$ 0,00</strong></div>
          <div class="box">Produtos em Estoque<br><strong>0</strong></div>
          <div class="box">Produtos Ativos<br><strong>0</strong></div>
        </section>

        <section class="table">
          <h3>Vendas Recentes</h3>
          <p>Nenhuma venda registrada ainda</p>
        </section>
      </main>

    </div>
  </div>

  <script src="script.js"></script>

</body>
</html>