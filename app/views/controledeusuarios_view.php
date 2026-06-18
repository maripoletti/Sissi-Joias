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
      <aside class="sidebar"></aside>

      <!-- MAIN -->
      <main class="main">

        <!-- HEADER -->
        <div class="page-header">
          <div class="page-icon">👥</div>

          <div>
            <h1 class="page-title">Controle de Revendedores</h1>
            <p class="page-subtitle">Gerencie as solicitações de cadastro dos seus Revendedores</p>
          </div>
        </div>

        <!-- NÍVEIS -->
        <div class="panel">
          <div class="panel-title">NÍVEIS — INICIANTE AO PRO</div>

          <div class="levels">

            <div class="level">
              <div class="level-bar ametista"></div>
              <div class="level-name">🔮 Ametista</div>
              <div class="level-info">de R$300,00 a R$799,00 → 20%</div>
            </div>

            <div class="level">
              <div class="level-bar safira"></div>
              <div class="level-name">🔵 Safira</div>
              <div class="level-info">de R$800,00 a R$1.299,00 → 25%</div>
            </div>

            <div class="level">
              <div class="level-bar topazio"></div>
              <div class="level-name">🟡 Topázio</div>
              <div class="level-info">de R$1.300,00 a R$1.799,00 → 30%</div>
            </div>

            <div class="level">
              <div class="level-bar esmeralda"></div>
              <div class="level-name">🟢 Esmeralda</div>
              <div class="level-info">de R$1.800,00 a R$2.299,00 → 35%</div>
            </div>

            <div class="level">
              <div class="level-bar rubi"></div>
              <div class="level-name">🔴 Rubi</div>
              <div class="level-info">de R$2.300,00 a R$4.999,00 → 40%</div>
            </div>

            <div class="level">
              <div class="level-bar rubi-black"></div>
              <div class="level-name rubi-black-name">⚫ Rubi Black</div>
              <div class="level-info">Acima de R$5.000,00 → 40% + brindes</div>
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
      <option value="rubi-black">Rubi Black</option>
    </select>

    <div class="modal-actions">
      <button id="cancelarNivel" class="btn secondary">Cancelar</button>
      <button id="salvarNivel" class="btn primary">Salvar</button>
    </div>
  </div>

  <script src="js/controledeusuarios.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>