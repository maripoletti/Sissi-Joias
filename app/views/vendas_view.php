<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vendas - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/vendas.css">
  <link rel="shortcut icon" href=".ico" type="image/x-icon">
</head>

<body>

<div class="container">
<div class="card app">

    <aside class="sidebar">
      <h2>Sissi Semi Joias e Acessórios</h2>

      <nav>
        <a href="/paineldecontrole">Painel de Controle</a>
        <a href="/produtos">Produtos</a>
        <a href="/vendas" class="active">Vendas</a>
        <a href="/impressoras">Impressoras</a>
        
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
          <a href="/relatorios">Relatórios</a>
          <a href="/controledeusuarios">Controle de Usuários</a>
          <a href="/fornecedores">Fornecedores</a>
          <a href="/cadastrarimpressora">Cadastrar Impressora</a>
        <?php endif; ?>

      </nav>
    </aside>

<main class="main">

<section class="wrap">

<header class="top">
  <div class="top-left">
    <h1>Vendas</h1>
    <p class="subtitle">
      <span id="qtdVendas">0</span> venda(s) registrada(s)
    </p>
  </div>

  <button class="btn-primary" id="btnRegistrar">
    + Registrar Venda
  </button>
</header>


<div style="margin: 10px 0;">
  <input type="text" id="scanner" style="width:200px; height:30px;" placeholder="Digite o código ou escaneie" autofocus>
</div>

<section class="list" id="listaVendas">
</section>

</section>

</main>

</div>
</div>

<script>
    window.USER = {
        id: <?php echo $_SESSION['user_id'] ?? 'null'; ?>,
        role: <?php echo $_SESSION['role'] ?? 'null'; ?>
    };
</script>

<script src="script.js"></script>
</body>
</html>