<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/estilo2.css">
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

      <aside class="sidebar"></aside>

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

      <button class="btn-salvar" id="salvarCampanhas">
        <i class="fa-regular fa-floppy-disk"></i>
        Salvar
      </button>

    </div>

  </div>

  <script src="js/toprevendedoras.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>