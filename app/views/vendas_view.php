<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vendas - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/vendas.css">
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
<a href="relatorios.php">Relatórios</a>
<a href="/controledeusuarios">Controle de Usuários</a>
<a href="impressoras.php">Impressoras</a>
<a href="fornecedores.php">Fornecedores</a>
<a href="cadastroimpressora.php">Cadastrar Impressora</a>
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

<section class="list" id="listaVendas">
</section>

</section>

</main>

</div>
</div>

<script src="script.js"></script>

</body>
</html>