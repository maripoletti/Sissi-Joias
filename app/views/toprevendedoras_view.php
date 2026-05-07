<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
  <link rel="stylesheet" href="styles/toprevendedoras.css">

  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
  >
</head>

<body>

  <div class="container">

    <div class="card paineldecontrole">

      <aside class="sidebar">

        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
          <a href="/paineldecontrole">Painel de Controle</a>
          <a href="/produtos">Produtos</a>
          <a href="/vendas">Vendas</a>

          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>

            <a href="/impressoras">Impressoras</a>

            <a href="/relatorios">Relatórios</a>

            <a href="/controledeusuarios">
              Controle de Revendedores
            </a>

            <a href="/fornecedores">Fornecedores</a>

            <a href="/cadastrarimpressora">
              Cadastrar Impressora
            </a>

            <a href="/produtosrevendedores">
              Produtos dos Revendedores
            </a>

            <a href="/precificacao">
              Precificação
            </a>

            <a href="/toprevendedoras" class="active">
              Top Revendedoras
            </a>

          <?php endif; ?>
        </nav>

      </aside>

      <main class="main">

        <section class="top-revendedoras">

          <div class="top-header">

            <div class="title-area">
              <i class="fa-solid fa-trophy"></i>
              <h1>Top Revendedoras</h1>
            </div>

            <button class="btn-campanhas" id="abrirModal">
              <i class="fa-solid fa-gear"></i>
              Campanhas
            </button>

          </div>

          <div class="filtros">

            <div class="grupo-filtro">

              <span>Mês:</span>

              <div class="buttons" id="mesesContainer"></div>

            </div>

            <div class="grupo-filtro">

              <span>Ano:</span>

              <div class="buttons" id="anosContainer"></div>

            </div>

            <div class="grupo-filtro">

              <span>Campanha:</span>

              <div
                class="buttons"
                id="campanhasFiltroContainer"
              ></div>

            </div>

          </div>

          <div
            class="ranking-container"
            id="rankingContainer"
          >

            <p class="empty-text">
              Nenhuma revendedora encontrada.
            </p>

          </div>

        </section>

      </main>

    </div>

  </div>

  <!-- MODAL CAMPANHAS -->

  <div class="modal-overlay" id="modalOverlay">

    <div class="modal-campanhas">

      <div class="modal-header">

        <h2>Campanhas</h2>

        <button id="fecharModal">
          <i class="fa-solid fa-xmark"></i>
        </button>

      </div>

      <div
        class="campanhas-lista"
        id="campanhasLista"
      ></div>

      <button
        class="btn-add"
        id="novaCampanha"
      >
        <i class="fa-solid fa-plus"></i>
        Nova Campanha
      </button>

      <button
        class="btn-salvar"
        id="salvarCampanhas"
      >
        <i class="fa-regular fa-floppy-disk"></i>
        Salvar
      </button>

    </div>

  </div>

  <script src="toprevendedoras.js"></script>

</body>
</html>