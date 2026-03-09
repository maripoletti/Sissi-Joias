<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Revendedoras - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/controledeusuarios.css" />
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
          <a href="/vendas">Vendas</a>
          <a href="/impressoras">Impressoras</a>
          
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
            <a href="/relatorios">Relatórios</a>
            <a href="/controledeusuarios" class="active">Controle de Usuários</a>
            <a href="/fornecedores">Fornecedores</a>
            <a href="/cadastrarimpressora">Cadastrar Impressora</a>
          <?php endif; ?>

        </nav>
      </aside>

      <main class="main">

        <header class="page-header">
          <div class="page-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M16 20c0-2.2-1.8-4-4-4H7c-2.2 0-4 1.8-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M9.5 12a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="2"/>
              <path d="M21 20c0-1.6-.9-3-2.3-3.6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M17.5 5.6a3.1 3.1 0 0 1 0 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>

          <div>
            <h1 class="page-title">Controle de Usuários</h1>
            <p class="page-subtitle">Gerencie as solicitações de cadastro.</p>
          </div>
        </header>

        <section class="panel">
          <div class="panel-title">NÍVEIS — INICIANTE AO PRO</div>

          <div class="levels" aria-label="Níveis">
            <div class="level">
              <div class="level-bar ametista"></div>
              <span class="level-name">Ametista</span>
            </div>

            <div class="level">
              <div class="level-bar safira"></div>
              <span class="level-name">Safira</span>
            </div>

            <div class="level">
              <div class="level-bar topazio"></div>
              <span class="level-name">Topázio</span>
            </div>

            <div class="level">
              <div class="level-bar esmeralda"></div>
              <span class="level-name">Esmeralda</span>
            </div>

            <div class="level">
              <div class="level-bar rubi"></div>
              <span class="level-name">Rubi</span>
            </div>
          </div>
        </section>

        <div class="tabs" role="tablist">
          <button class="tab active" type="button" data-tab="pendentes" role="tab" aria-selected="true">Pendentes</button>
          <button class="tab" type="button" data-tab="aprovadas" role="tab" aria-selected="false">Aprovadas</button>
          <button class="tab" type="button" data-tab="recusadas" role="tab" aria-selected="false">Recusadas</button>
        </div>
        <section class="list" id="list-pendentes"></section>
        <section class="list" id="list-aprovadas" hidden></section>
        <section class="list" id="list-recusadas" hidden></section>

        <!-- MODAL NÍVEL -->
        <div class="modal-bg" id="modalBg" hidden></div>

        <div class="modal" id="modalNivel" hidden>
          <h3>Alterar nível</h3>

          <label for="nivelSelect">Nível</label>
          <select id="nivelSelect">
            <option value="ametista">Ametista</option>
            <option value="safira">Safira</option>
            <option value="topazio">Topázio</option>
            <option value="esmeralda">Esmeralda</option>
            <option value="rubi">Rubi</option>
          </select>

          <div class="modal-actions">
            <button type="button" class="btn secondary" id="cancelarNivel">Cancelar</button>
            <button type="button" class="btn primary" id="salvarNivel">Salvar</button>
          </div>
        </div>

      </main>

    </div>
  </div>

  <script src="controledeusuarios.js"></script>
  
</body>
</html>