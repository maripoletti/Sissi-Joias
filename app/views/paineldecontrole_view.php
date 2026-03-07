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
        <a href="/produtos">Produtos</a>
        <a href="/vendas">Vendas</a>
        <a href="/relatoris">Relatórios</a>
        <a href="/estoque">Estoque</a>
        <a href="/controledeusuarios">Controle de Usuários</a>
        <a href="/impressoras">Impressoras</a>
        <a href="/fornecedores">Fornecedores</a>
        <a href="/cadastrarimpressora">Cadastrar Impressora</a>
      </nav>
    </aside>

    <main class="main">
      <header class="top">
        <div>
          <h1>Painel de Controle</h1>
          <span class="subtitle" id="data-atual"></span>
        </div>

        <div class="top-actions">
           <a class="btn-certificado" href="certificado_garantia.pdf" download>
            ↓ Certificado de Garantia </a>
          <a class="btn-primary" href="vendas.php">+ Nova Venda</a>
        </div>
      </header>

      <section class="painel-grid">

        <!-- CALENDÁRIO -->
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

          <!-- LEGENDA -->
          <div class="cal-legend">
            <span class="dot dot-reserva"></span> Reserva
            <span class="dot dot-aniver"></span> Aniversário
            <span class="dot dot-outro"></span> Outro
          </div>

          <!-- DETALHE DO DIA -->
          <div class="cal-detail" id="calDetail">
            <div class="cal-detail-head">
              <h4 id="detailTitle">Clique no número do dia para ler</h4>
              <button type="button" class="detail-close" id="btnFecharDetalhe">×</button>
            </div>

            <div class="cal-detail-body" id="detailBody">
              <p class="muted">Anote um lembrete, marque uma data ou registre uma reserva para não esquecer.</p>
            </div>
          </div>

          <!-- LISTAS ABAIXO -->
          <div class="cal-birthdays">
            <h4>Aniversariantes</h4>
            <div id="listaAniversarios" class="birth-list">
              <p class="muted">Nenhum aniversariante cadastrado.</p>
            </div>
          </div>

          <div class="cal-birthdays">
            <h4>Reservas</h4>
            <div id="listaReservas" class="birth-list">
              <p class="muted">Nenhuma reserva cadastrada.</p>
            </div>
          </div>

          <div class="cal-birthdays">
            <h4>Outros</h4>
            <div id="listaOutros" class="birth-list">
              <p class="muted">Nenhum item cadastrado.</p>
            </div>
          </div>

        </div>

        <!-- TOP VENDEDORAS -->
        <div class="panel panel-top">
          <div class="panel-head">
            <h3>Top Vendedoras</h3>
          </div>

          <div class="top-list">
            <div class="top-item">
              <span class="rank gold">1</span>
              <span class="name">Ana Paula</span>
              <span class="valor">R$ 908</span>
            </div>
            <div class="barra"><span class="fill gold" style="width:100%"></span></div>

            <div class="top-item">
              <span class="rank roxo">2</span>
              <span class="name">Fernanda Lima</span>
              <span class="valor">R$ 610</span>
            </div>
            <div class="barra"><span class="fill roxo" style="width:70%"></span></div>

            <div class="top-item">
              <span class="rank lilas">3</span>
              <span class="name">Beatriz Mendes</span>
              <span class="valor">R$ 290</span>
            </div>
            <div class="barra"><span class="fill lilas" style="width:35%"></span></div>

            <div class="top-item">
              <span class="rank cinza">4</span>
              <span class="name">Carla Santos</span>
              <span class="valor">R$ 65</span>
            </div>
            <div class="barra"><span class="fill cinza" style="width:10%"></span></div>
          </div>
        </div>

        <!-- MODAL -->
        <div id="modalEvento" class="modal hidden">
          <div class="modal-content">
            <div class="modal-top">
              <h3>Adicionar Evento</h3>
              <button id="btnFecharModal" class="btn-close" type="button">×</button>
            </div>

            <form id="formEvento" class="form-evento">

              <label>
                Título
                <input type="text" id="eventoTitulo" maxlength="60" required>
              </label>

              <div class="grid-2">
                <label>
                  Data
                  <input type="date" id="eventoData" required>
                </label>

                <label>
                  Hora (opcional)
                  <input type="time" id="eventoHora">
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
                <textarea id="eventoComentario" maxlength="200"></textarea>
              </label>

              <div class="modal-actions">
                <button type="button" id="btnCancelar" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary">Salvar</button>
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