<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Controle de Usuários</title>

<link rel="stylesheet" href="styles/global.css">
<link rel="stylesheet" href="styles/controledeusuarios.css">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

</head>

<body>

<div class="container">
<div class="card app">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <h2>Sissi Semi Joias</h2>

   <nav>
          <a href="/paineldecontrole">Painel de Controle</a>
          <a href="/produtos">Produtos</a>
          <a href="/vendas">Vendas</a>
          <a href="/impressoras">Impressoras</a>
          
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
            <a href="/relatorios">Relatórios</a>
            <a href="/controledeusuarios" class="active">Controle de Revendedores</a>
            <a href="/fornecedores">Fornecedores</a>
            <a href="/cadastrarimpressora">Cadastrar Impressora</a>
          <?php endif; ?>

        </nav>
      </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- HEADER -->
    <div class="page-header">
      <div class="page-icon">👥</div>

      <div>
        <h1 class="page-title">Controle de Usuários</h1>
        <p class="page-subtitle">Gerencie as solicitações de cadastro dos seus Revendedores</p>
      </div>
    </div>

    <!-- 🔥 NÍVEIS -->
    <div class="panel">
      <div class="panel-title">NÍVEIS — INICIANTE AO PRO</div>

      <div class="levels">

        <div class="level">
          <div class="level-bar ametista"></div>
          <div class="level-name">🔮 Ametista</div>
          <div class="level-info">Até R$999 → 20%</div>
        </div>

        <div class="level">
          <div class="level-bar safira"></div>
          <div class="level-name">🔵 Safira</div>
          <div class="level-info">R$1.000 a R$1.999 → 25%</div>
        </div>

        <div class="level">
          <div class="level-bar topazio"></div>
          <div class="level-name">🟡 Topázio</div>
          <div class="level-info">R$2.000 a R$2.999 → 30%</div>
        </div>

        <div class="level">
          <div class="level-bar esmeralda"></div>
          <div class="level-name">🟢 Esmeralda</div>
          <div class="level-info">R$3.000 a R$3.999 → 35%</div>
        </div>

        <div class="level">
          <div class="level-bar rubi"></div>
          <div class="level-name">🔴 Rubi</div>
          <div class="level-info">Acima de R$4.000 → 40%</div>
        </div>

      </div>
    </div>

    <!-- TABS -->
    <div class="tabs">
      <button class="tab active" data-tab="pendentes">Pendentes</button>
      <button class="tab" data-tab="aprovadas">Aprovadas</button>
      <button class="tab" data-tab="recusadas">Recusadas</button>
    </div>

    <!-- LISTAS -->
    <div id="list-pendentes" class="list"></div>
    <div id="list-aprovadas" class="list" hidden></div>
    <div id="list-recusadas" class="list" hidden></div>

  </main>

</div>
</div>

<div id="modalBg" class="modal-bg" hidden></div>

<div id="modalNivel" class="modal" hidden>
  <h3>Alterar nível</h3>

  <label>Nível</label>
  <select id="nivelSelect">
    <option value="ametista">Ametista</option>
    <option value="safira">Safira</option>
    <option value="topazio">Topázio</option>
    <option value="esmeralda">Esmeralda</option>
    <option value="rubi">Rubi</option>
  </select>

  <div class="modal-actions">
    <button id="cancelarNivel" class="btn secondary">Cancelar</button>
    <button id="salvarNivel" class="btn primary">Salvar</button>
  </div>
</div>

<script src="js/controledeusuarios.js"></script>

</body>
</html>