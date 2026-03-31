<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<div class="container">
  <div class="card paineldecontrole">

    <aside class="sidebar">
      <h2>Sissi Semi Joias e Acessórios</h2>

      <nav>
        <a href="/paineldecontrole" class="active">Painel de Controle</a>
        <a href="/produtos">Produtos</a>
        <a href="/vendas">Vendas</a>
        <a href="/impressoras">Impressoras</a>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
          <a href="/relatorios">Relatórios</a>
          <a href="/controledeusuarios">Controle de Revendedores</a>
          <a href="/fornecedores">Fornecedores</a>
          <a href="/cadastrarimpressora">Cadastrar Impressora</a>
          <a href="/produtosrevendedores">Produtos dos Revendedores</a>
        <?php endif; ?>
      </nav>

      <div class="user-profile">
        <label for="trocarFoto" class="avatar-wrap">
          <div class="avatar">
            <img id="avatarPreview" src="" alt="Foto de perfil" style="display: none;">
            <span id="avatarIcon">👤</span>
          </div>
        </label>

        <div class="user-meta">
          <strong class="user-name" id="userName"> <?php echo isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário'; ?></strong>

          <div class="user-group-badge ametista" id="userGroupBadge">
            Ametista
          </div>

          <p class="user-recado" id="userRecado">
            Carregando informações...
          </p>
        </div>

        <input type="file" id="trocarFoto" accept="image/*" hidden>
      </div>
    </aside>

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

          <a class="btn-contrato" href="contrato.pdf" download>
            ↓ Contrato
        </a>

          <a class="btn-primary" href="/novavenda">+ Nova Venda</a>

          <a href="logout.php" class="btn-sair">🔐 Sair</a>
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
          </div>

          <div class="top-list"></div>
        </div>

      </section>
    </main>

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

<script>
  const dataAtual = document.getElementById("data-atual");
  const hojeTopo = new Date();
  dataAtual.textContent = hojeTopo.toLocaleDateString("pt-BR", {
    weekday: "long",
    day: "2-digit",
    month: "long",
    year: "numeric"
  });

  const inputFoto = document.getElementById("trocarFoto");
  const avatarPreview = document.getElementById("avatarPreview");
  const avatarIcon = document.getElementById("avatarIcon");
  const userNameEl = document.getElementById("userName");
  const badge = document.getElementById("userGroupBadge");
  const userRecadoEl = document.getElementById("userRecado");

  inputFoto.addEventListener("change", function () {
    const arquivo = this.files[0];

    if (arquivo) {
      const leitor = new FileReader();

      leitor.onload = function (e) {
        avatarPreview.src = e.target.result;
        avatarPreview.style.display = "block";
        avatarIcon.style.display = "none";
      };

      leitor.readAsDataURL(arquivo);
    }
  });

  function definirGrupoEMeta(total) {
    let grupoNome = "Ametista";
    let proximoGrupo = "Safira";
    let proximaMeta = 1000;
    let classeGrupo = "ametista";

    if (total >= 1000 && total < 2000) {
      grupoNome = "Safira";
      proximoGrupo = "Topázio";
      proximaMeta = 2000;
      classeGrupo = "safira";
    } else if (total >= 2000 && total < 3000) {
      grupoNome = "Topázio";
      proximoGrupo = "Esmeralda";
      proximaMeta = 3000;
      classeGrupo = "topazio";
    } else if (total >= 3000 && total < 4000) {
      grupoNome = "Esmeralda";
      proximoGrupo = "Rubi";
      proximaMeta = 4000;
      classeGrupo = "esmeralda";
    } else if (total >= 4000) {
      grupoNome = "Rubi";
      proximoGrupo = null;
      proximaMeta = null;
      classeGrupo = "rubi";
    }

    return { grupoNome, proximoGrupo, proximaMeta, classeGrupo };
  }

  function gerarRecado(total) {
    const { grupoNome, proximoGrupo, proximaMeta, classeGrupo } = definirGrupoEMeta(total);

    let recado = `Continue vendendo para subir de nível 💜`;

    if (proximaMeta !== null) {
      const faltam = proximaMeta - total;
      const faltamFormatado = faltam.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      if (faltam <= 100) {
        recado = `Tá voando! Faltam só <strong>R$ ${faltamFormatado}</strong> para chegar em <span>${proximoGrupo}</span>.`;
      } else if (faltam <= 300) {
        recado = `Você está pertinho do grupo <span>${proximoGrupo}</span>.<br>Faltam <strong>R$ ${faltamFormatado}</strong>.`;
      } else if (faltam <= 500) {
        recado = `Bom ritmo! Continue assim para alcançar <span>${proximoGrupo}</span>.`;
      } else {
        recado = `Seu próximo objetivo é o grupo <span>${proximoGrupo}</span>.`;
      }
    } else {
      recado = `Parabéns! Você está no grupo <span>Rubi</span> e bateu a meta máxima. 🔥`;
    }

    return { grupoNome, recado, classeGrupo };
  }

  function preencherDadosUsuario(usuario) {
    const nome = usuario.nome || "Sem nome";
    const foto = usuario.foto || "";
    const total = Number(usuario.total) || 0;

    userNameEl.textContent = nome;

    if (foto) {
      avatarPreview.src = foto;
      avatarPreview.style.display = "block";
      avatarIcon.style.display = "none";
    } else {
      avatarPreview.style.display = "none";
      avatarIcon.style.display = "block";
    }

    const dadosMeta = gerarRecado(total);

    badge.textContent = dadosMeta.grupoNome;
    badge.className = `user-group-badge ${dadosMeta.classeGrupo}`;

    userRecadoEl.innerHTML = dadosMeta.recado;
  }

  async function carregarDadosUsuario() {
    try {
      const res = await fetch("/api/usuario/perfil", {
        method: "GET",
        headers: {
          "Content-Type": "application/json"
        }
      });

      if (!res.ok) {
        throw new Error("Erro ao buscar dados do usuário");
      }

      const data = await res.json();
      preencherDadosUsuario(data);
    } catch (err) {
      console.error(err);
      userNameEl.textContent = "Erro ao carregar";
      badge.textContent = "Ametista";
      badge.className = "user-group-badge ametista";
      userRecadoEl.innerHTML = "Não foi possível carregar os dados da revendedora.";
    }
  }

  const calDias = document.getElementById("calDias");
  const calTitulo = document.getElementById("calTitulo");
  const prevMes = document.getElementById("prevMes");
  const nextMes = document.getElementById("nextMes");
  const btnAddEvento = document.getElementById("btnAddEvento");
  const detailTitle = document.getElementById("detailTitle");
  const detailBody = document.getElementById("detailBody");
  const calDetail = document.getElementById("calDetail");
  const btnFecharDetalhe = document.getElementById("btnFecharDetalhe");
  const listaAniversarios = document.getElementById("listaAniversarios");

  const modalEvento = document.getElementById("modalEvento");
  const btnFecharModal = document.getElementById("btnFecharModal");
  const btnCancelar = document.getElementById("btnCancelar");
  const formEvento = document.getElementById("formEvento");
  const eventoTitulo = document.getElementById("eventoTitulo");
  const eventoData = document.getElementById("eventoData");
  const eventoHora = document.getElementById("eventoHora");
  const eventoTipo = document.getElementById("eventoTipo");
  const eventoComentario = document.getElementById("eventoComentario");

  let dataCalendario = new Date();
  let diaSelecionado = null;

  const meses = [
    "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
    "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
  ];

  function formatarData(ano, mes, dia) {
    const mesFormatado = String(mes + 1).padStart(2, "0");
    const diaFormatado = String(dia).padStart(2, "0");
    return `${ano}-${mesFormatado}-${diaFormatado}`;
  }

  function obterEventos() {
    return JSON.parse(localStorage.getItem("eventosCalendario")) || {};
  }

  function salvarEventos(eventos) {
    localStorage.setItem("eventosCalendario", JSON.stringify(eventos));
  }

  function abrirModal(data = "") {
    modalEvento.classList.remove("hidden");
    if (data) {
      eventoData.value = data;
    }
  }

  function fecharModal() {
    modalEvento.classList.add("hidden");
    formEvento.reset();
  }

  function abrirDetalheDia(ano, mes, dia) {
    const chave = formatarData(ano, mes, dia);
    const eventos = obterEventos();
    const lista = eventos[chave] || [];

    detailTitle.textContent = `${dia} de ${meses[mes]} de ${ano}`;

    if (lista.length === 0) {
      detailBody.innerHTML = `<p class="muted">Nenhum evento cadastrado para este dia.</p>`;
    } else {
      detailBody.innerHTML = lista.map((evento, index) => {
        return `
          <div class="evento-item">
            <strong>${evento.titulo}</strong>
            <p>${evento.comentario || "Sem descrição."}</p>
            <small>Tipo: ${evento.tipo} ${evento.hora ? "• " + evento.hora : ""}</small>
            <button type="button" class="btn-remover-evento" data-data="${chave}" data-index="${index}">
              Remover
            </button>
          </div>
        `;
      }).join("");

      document.querySelectorAll(".btn-remover-evento").forEach((botao) => {
        botao.addEventListener("click", function () {
          const data = this.getAttribute("data-data");
          const index = Number(this.getAttribute("data-index"));

          const eventosSalvos = obterEventos();

          if (eventosSalvos[data]) {
            eventosSalvos[data].splice(index, 1);

            if (eventosSalvos[data].length === 0) {
              delete eventosSalvos[data];
            }

            salvarEventos(eventosSalvos);

            const [anoStr, mesStr, diaStr] = data.split("-");
            abrirDetalheDia(Number(anoStr), Number(mesStr) - 1, Number(diaStr));
            renderizarCalendario();
          }
        });
      });
    }

    calDetail.style.display = "block";
  }

  function atualizarListaAniversarios() {
    const eventos = obterEventos();
    const ano = dataCalendario.getFullYear();
    const mes = dataCalendario.getMonth();

    const aniversariosDoMes = [];

    Object.keys(eventos).forEach((data) => {
      const [anoEvento, mesEvento, diaEvento] = data.split("-").map(Number);

      if (anoEvento === ano && (mesEvento - 1) === mes) {
        eventos[data].forEach((evento) => {
          if (evento.tipo === "Aniversário") {
            aniversariosDoMes.push({
              dia: diaEvento,
              titulo: evento.titulo
            });
          }
        });
      }
    });

    if (aniversariosDoMes.length === 0) {
      listaAniversarios.innerHTML = `<p class="muted">Nenhum aniversariante cadastrado.</p>`;
      return;
    }

    aniversariosDoMes.sort((a, b) => a.dia - b.dia);

    listaAniversarios.innerHTML = aniversariosDoMes.map((item) => {
      return `<p><strong>${String(item.dia).padStart(2, "0")}/${String(mes + 1).padStart(2, "0")}</strong> — ${item.titulo}</p>`;
    }).join("");
  }

  function renderizarCalendario() {
    const ano = dataCalendario.getFullYear();
    const mes = dataCalendario.getMonth();
    const primeiroDiaSemana = new Date(ano, mes, 1).getDay();
    const totalDiasMes = new Date(ano, mes + 1, 0).getDate();
    const eventos = obterEventos();

    calTitulo.textContent = `${meses[mes]} ${ano}`;
    calDias.innerHTML = "";

    for (let i = 0; i < primeiroDiaSemana; i++) {
      const vazio = document.createElement("div");
      vazio.className = "cal-day empty";
      calDias.appendChild(vazio);
    }

    for (let dia = 1; dia <= totalDiasMes; dia++) {
      const diaEl = document.createElement("div");
      diaEl.className = "cal-day";

      const hoje = new Date();
      const ehHoje =
        dia === hoje.getDate() &&
        mes === hoje.getMonth() &&
        ano === hoje.getFullYear();

      const chave = formatarData(ano, mes, dia);
      const temEvento = eventos[chave] && eventos[chave].length > 0;
      const estaSelecionado =
        diaSelecionado &&
        diaSelecionado.dia === dia &&
        diaSelecionado.mes === mes &&
        diaSelecionado.ano === ano;

      if (ehHoje) {
        diaEl.classList.add("today");
      }

      if (temEvento) {
        diaEl.classList.add("has-event");
      }

      if (estaSelecionado) {
        diaEl.classList.add("selected");
      }

      diaEl.innerHTML = `
        <span class="day-number">${dia}</span>
        ${temEvento ? '<span class="event-marker"></span>' : ''}
      `;

      diaEl.addEventListener("click", function () {
        diaSelecionado = { dia, mes, ano };
        renderizarCalendario();
        abrirDetalheDia(ano, mes, dia);
      });

      calDias.appendChild(diaEl);
    }

    atualizarListaAniversarios();
  }

  btnAddEvento.addEventListener("click", function () {
    let ano = dataCalendario.getFullYear();
    let mes = dataCalendario.getMonth();
    let dia = new Date().getDate();

    if (diaSelecionado) {
      ano = diaSelecionado.ano;
      mes = diaSelecionado.mes;
      dia = diaSelecionado.dia;
    }

    abrirModal(formatarData(ano, mes, dia));
  });

  btnFecharModal.addEventListener("click", fecharModal);
  btnCancelar.addEventListener("click", fecharModal);

  modalEvento.addEventListener("click", function (e) {
    if (e.target === modalEvento) {
      fecharModal();
    }
  });

  formEvento.addEventListener("submit", function (e) {
    e.preventDefault();

    const titulo = eventoTitulo.value.trim();
    const data = eventoData.value;
    const hora = eventoHora.value;
    const tipoSelecionado = eventoTipo.value;
    const comentario = eventoComentario.value.trim();

    if (!titulo || !data) {
      alert("Preencha o título e a data.");
      return;
    }

    const [ano, mes, dia] = data.split("-").map(Number);
    const chave = `${ano}-${String(mes).padStart(2, "0")}-${String(dia).padStart(2, "0")}`;
    const eventos = obterEventos();

    let tipoFinal = "Outro";
    if (tipoSelecionado === "aniversario") tipoFinal = "Aniversário";
    if (tipoSelecionado === "lembrete") tipoFinal = "Lembrete";
    if (tipoSelecionado === "reserva") tipoFinal = "Reserva";

    if (!eventos[chave]) {
      eventos[chave] = [];
    }

    eventos[chave].push({
      titulo: titulo,
      hora: hora,
      tipo: tipoFinal,
      comentario: comentario
    });

    salvarEventos(eventos);
    fecharModal();

    dataCalendario = new Date(ano, mes - 1, 1);
    diaSelecionado = { dia, mes: mes - 1, ano };

    renderizarCalendario();
    abrirDetalheDia(ano, mes - 1, dia);
  });

  prevMes.addEventListener("click", function () {
    dataCalendario.setMonth(dataCalendario.getMonth() - 1);
    renderizarCalendario();
    calDetail.style.display = "none";
  });

  nextMes.addEventListener("click", function () {
    dataCalendario.setMonth(dataCalendario.getMonth() + 1);
    renderizarCalendario();
    calDetail.style.display = "none";
  });

  btnFecharDetalhe.addEventListener("click", function () {
    calDetail.style.display = "none";
  });

  renderizarCalendario();
  carregarDadosUsuario();
</script>
<script src="script.js"></script>
</body>
</html>