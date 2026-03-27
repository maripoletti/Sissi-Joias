<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
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
        <a href="/impressoras">Impressoras</a>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
          <a href="/relatorios">Relatórios</a>
          <a href="/controledeusuarios">Controle de Revendedores</a>
          <a href="/fornecedores">Fornecedores</a>
          <a href="/cadastrarimpressora">Cadastrar Impressora</a>
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
          <strong class="user-name" id="userName">Carregando...</strong>

          <div class="user-group-badge ametista" id="userGroupBadge">
            Ametista
          </div>

          <p class="user-recado" id="userRecado">
            Carregando informações...
          </p>
        </div>

        <input type="file" id="trocarFoto" accept="uploads/*" hidden>
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

<script>
  const dataAtual = document.getElementById("data-atual");
  const hoje = new Date();
  dataAtual.textContent = hoje.toLocaleDateString("pt-BR", {
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

  carregarDadosUsuario();
</script>
<script src="script.js"></script>
</body>
</html>