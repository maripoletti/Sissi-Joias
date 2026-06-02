<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<div class="container">
  <div class="card paineldecontrole">

    <aside class="sidebar">
      <h2>Sissi Semi Joias e Acessórios</h2>

      <nav>
        <a href="/paineldecontrole" class="active">Painel de Controle</a>
        <a href="/produtos">Produtos</a>
        <a href="/vendas">Vendas</a>
        
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
          <a href="/impressoras">Impressoras</a>
          <a href="/relatorios">Relatórios</a>
          <a href="/controledeusuarios">Controle de Revendedores</a>
          <a href="/fornecedores">Fornecedores</a>
          <a href="/cadastrarimpressora">Cadastrar Impressora</a>
          <a href="/produtosrevendedores">Produtos dos Revendedores</a>
          <a href="/precificacao">Precificação</a>
          <a href="/toprevendedoras">Top Revendedoras</a>
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

  inputFoto.addEventListener("change", async () => {
    const file = inputFoto.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("imagem", file);

    try {
        const response = await fetch("/api/usuario/upload", {
            method: "POST",
            body: formData
        });

        const text = await response.text();

        if (!text) {
            throw new Error("Resposta vazia do servidor");
        }

        const data = JSON.parse(text);

        await carregarDadosUsuario();
    } catch (err) {
        console.error(err);
    }
  });

  function definirGrupoEMeta(total) {
    let grupoNome = "Ametista";
    let proximoGrupo = "Safira";
    let proximaMeta = 800;
    let classeGrupo = "ametista";

    if (total >= 800 && total < 1300) {
      grupoNome = "Safira";
      proximoGrupo = "Topázio";
      proximaMeta = 1800;
      classeGrupo = "safira";
    } else if (total >= 1300 && total < 1800) {
      grupoNome = "Topázio";
      proximoGrupo = "Esmeralda";
      proximaMeta = 1800;
      classeGrupo = "topazio";
    } else if (total >= 1800 && total < 2300) {
      grupoNome = "Esmeralda";
      proximoGrupo = "Rubi";
      proximaMeta = 2300;
      classeGrupo = "esmeralda";
    } else if (total >= 2300 && total < 5000) {
      grupoNome = "Rubi";
      proximoGrupo = "Rubi Black";
      proximaMeta = 5000;
      classeGrupo = "rubi";
    } else if (total >= 5000) {
      grupoNome = "Rubi Black";
      proximoGrupo = null;
      proximaMeta = null;
      classeGrupo = "rubiblack";
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
      recado = `Parabéns! Você está no grupo <span>Rubi Black</span> e bateu a meta máxima. 🔥`;
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

  carregarDadosUsuario();
</script>
<script src="script.js"></script>
</body>
</html>