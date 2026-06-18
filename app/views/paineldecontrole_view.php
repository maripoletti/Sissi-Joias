<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/estilo2.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
  <body>
  <div class="container">
    <div class="card paineldecontrole">

      <aside class="sidebar"></aside>

      <main class="main">
        <header class="top">
          <div>
            <h1>Painel de Controle</h1>
            <span class="subtitle" id="data-atual"></span>
          </div>

          <div class="top-actions">
            <a class="btn-certificado" href="certificado_garantia.pdf" download>
              ↓ Certificado de Garantia
            </a>

            <a class="btn-contrato" href="contratoassinado.pdf" download>
              ↓ Contrato
          </a>

            <a class="btn-primary" href="/novavenda">+ Nova Venda</a>

            <a href="/logout" class="btn-sair">🔐 Sair</a>
          </div>
        </header>

        <section class="painel-grid">

          <div class="panel panel-calendar">
            <div class="panel-head">
              <h3>Calendário</h3>

              <div class="cal-actions">
                <button class="icon-btn" type="button" id="prevMes">‹</button>
                <span class="cal-title" id="calTitulo">Mês</span>
                <button class="icon-btn" type="button" id="nextMes">›</button>

                <button class="btn-add-evento" type="button" id="btnAddEvento">
                  + Evento
                </button>
              </div>
            </div>

            <div class="calendar">
              <div class="cal-week">
                <span>Dom</span><span>Seg</span><span>Ter</span>
                <span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
              </div>
              <div class="cal-days" id="calDias"></div>
            </div>

            <div class="cal-legend">
              <span><span class="dot dot-aniver"></span> Aniversário</span>
              <span><span class="dot dot-outro"></span> Outro</span>
            </div>

            <div class="cal-detail" id="calDetail">
              <div class="cal-detail-head">
                <h4 id="detailTitle">Clique no número do dia para ler</h4>
                <button type="button" class="detail-close" id="btnFecharDetalhe">×</button>
              </div>

              <div class="cal-detail-body" id="detailBody">
                <p class="muted">Anote um lembrete, marque uma data ou registre uma reserva para não esquecer.</p>
              </div>
            </div>

            <div class="cal-birthdays">
              <h4>Aniversariantes</h4>
              <div id="listaAniversarios" class="birth-list">
                <p class="muted">Nenhum aniversariante cadastrado.</p>
              </div>
            </div>
          </div>

          <div class="panel panel-top">
            <div class="panel-head">
              <h3>Top Vendedoras</h3>
              <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
              <button class="btn-campanhas" id="abrirModal">
                <i class="fa-solid fa-gear"></i>
                Campanhas
              </button>
               <?php endif; ?>
            </div>

            <div class="top-list"></div>
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

      <div class="campanhas-lista" id="campanhasLista"></div>

      <button class="btn-add" id="novaCampanha">
        <i class="fa-solid fa-plus"></i>
        Nova Campanha
      </button>

      <button class="btn-salvar" id="salvarCampanhas">
        <i class="fa-regular fa-floppy-disk"></i>
        Salvar
      </button>
    </div>
  </div>

  <div id="modalEvento" class="modal hidden">
    <div class="modal-content">
      <button type="button" id="btnFecharModal" class="close">×</button>

      <h3>+ Evento</h3>

      <form id="formEvento">
        <input type="text" id="eventoTitulo" placeholder="Título" required>
        <input type="date" id="eventoData" required>
        <input type="time" id="eventoHora">

        <select id="eventoTipo">
          <option value="outro">Outro</option>
          <option value="aniversario">Aniversário</option>
          <option value="lembrete">Lembrete</option>
          <option value="reserva">Reserva</option>
        </select>

        <textarea id="eventoComentario" placeholder="Comentário"></textarea>

        <div class="modal-actions">
          <button type="button" id="btnCancelar">Cancelar</button>
          <button type="submit" class="btn-salvar-evento">Salvar</button>
        </div>
      </form>
    </div>
  </div>
  <script src="js/paineldecontrole.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>