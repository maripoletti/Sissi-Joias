<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
</head>
<body>

<div class="container">
  <div class="card paineldecontrole">

    <aside class="sidebar">
      <h2>Sissi Semi Joias e Acessórios</h2>
      <nav>
        <a href="/paineldecontrole" class="active">Painel de Controle</a>
        <a href="produtos.php">Produtos</a>
        <a href="vendas.php">Vendas</a>
        <a>Relatórios</a>
        <a>Estoque</a>
        <a>Controle de Usuários</a>
        <a>Impressoras</a>
        <a>Fornecedores</a>
      </nav>
    </aside>

    <main class="main">
      <header class="top">
        <div>
          <h1>Painel de Controle</h1>
          <span class="subtitle" id="data-atual"></span>
        </div>

        <div class="top-actions">
          <a class="btn-primary" href="produtos.php">+ Nova Reserva</a>
          <a class="btn btn-outline" href="vendas.php">+ Nova Venda</a>
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
            <span class="dot dot-reserva"></span> Reserva
            <span class="dot dot-aniver"></span> Aniversário
          </div>
        </div>


        <div id="modalEvento" class="modal hidden">
          <div class="modal-content">

            <div class="modal-top">
              <h3>Adicionar Evento</h3>
              <button id="btnFecharModal" class="btn-close" type="button">×</button>
            </div>

            <form id="formEvento" class="form-evento">

              <label>
                Título
                <input type="text"
                       id="eventoTitulo"
                       maxlength="60"
                       required>
              </label>

              <div class="grid-2">
                <label>
                  Data
                  <input type="date"
                         id="eventoData"
                         required>
                </label>

                <label>
                  Hora (opcional)
                  <input type="time"
                         id="eventoHora">
                </label>
              </div>

              <label>
                Tipo
                <select id="eventoTipo" required>
                  <option value="">Selecione</option>
                  <option value="reserva">Reserva</option>
                  <option value="aniversario">Aniversário</option>
                  <option value="lembrete">Lembrete</option>
                  <option value="outro">Outro</option>
                </select>
              </label>

              <label>
                Comentário (opcional)
                <textarea id="eventoComentario"
                          maxlength="200"></textarea>
              </label>

              <div class="modal-actions">
                <button type="button"
                        id="btnCancelar"
                        class="btn-secondary">
                  Cancelar
                </button>

                <button type="submit"
                        class="btn-primary">
                  Salvar
                </button>
              </div>

            </form>

          </div>
        </div>

      </section>
    </main>

  </div>
</div>

<script src="script.js"></script>
</body>
</html>