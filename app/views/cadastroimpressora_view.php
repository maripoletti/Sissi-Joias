<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Revendedoras - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/cadastroimpressora.css" />
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
        <a href="/controledeusuarios">Controle de Usuários</a>
          <a href="/fornecedores">Fornecedores</a>
          <a href="/cadastrarimpressora" class="active">Cadastrar Impressora</a>
        <?php endif; ?>

      </nav>
    </aside>

      <main class="main">
  <header class="page-header">
    <div class="ph-left">
      <div class="ph-icon" aria-hidden="true">
       
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M7 7V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path d="M6 17H5a3 3 0 0 1-3-3v-3a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v3a3 3 0 0 1-3 3h-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path d="M7 14h10v8H7v-8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
          <path d="M17 11h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        </svg>
      </div>

      <div>
        <h1 class="ph-title">Nova Impressora</h1>
        <p class="ph-sub">Cadastre uma impressora fiscal ou etiquetadora.</p>
      </div>
    </div>
  </header>

  
  <section class="ni-card">
    <div class="ni-card-top">
      <div class="ni-tabs" role="tablist" aria-label="Tipo de impressora">
        <button type="button" class="ni-tab active" data-type="nf" role="tab" aria-selected="true">
          
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7l-5-5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M14 2v5h5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
          </svg>
          Notas
        </button>

        <button type="button" class="ni-tab" data-type="et" role="tab" aria-selected="false">
          <!-- ícone tag -->
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M20 12l-8 8-10-10V2h8l10 10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M7 7h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
          </svg>
          Etiquetas
        </button>
      </div>
    </div>

    <form class="ni-form" id="printerForm">
      <div class="ni-grid">
        <div class="ni-field">
          <label class="ni-label" for="nome">NOME <span class="req">*</span></label>
          <input class="ni-input" id="nome" name="nome" placeholder="Ex: Impressora Cozinha" required>
        </div>

        <div class="ni-field">
          <label class="ni-label" for="marca">MARCA</label>
          <input class="ni-input" id="marca" name="marca" placeholder="Ex: Epson, Zebra...">
        </div>

        <div class="ni-field">
          <label class="ni-label" for="modelo">MODELO</label>
          <input class="ni-input" id="modelo" name="modelo" placeholder="Ex: TM-T20X">
        </div>

        <div class="ni-field">
          <label class="ni-label" for="conexao">CONEXÃO</label>
          <select class="ni-select" id="conexao" name="conexao">
            <option value="" selected disabled>Selecione...</option>
            <option>USB</option>
            <option>Rede (TCP/IP)</option>
            <option>Wi-Fi</option>
            <option>Bluetooth</option>
          </select>
        </div>

        <div class="ni-field">
          <label class="ni-label" for="ip">IP</label>
          <input 
            class="ni-input"
            id="ip"
            name="ip"
            placeholder="192.168.1.100"
            inputmode="numeric"
            pattern="^(\d{1,3}\.){3}\d{1,3}$"
          >
        </div>

        <div class="ni-field">
          <label class="ni-label" for="porta">PORTA</label>
          <input
            class="ni-input"
            id="porta"
            name="porta"
            type="number"
            min="0"
            max="65535"
            placeholder="9100"
          >
        </div>

        <div class="ni-field">
          <label class="ni-label" for="localizacao">LOCALIZAÇÃO</label>
          <input class="ni-input" id="localizacao" name="localizacao" placeholder="Ex: Balcão, Estoque...">
        </div>

        <div class="ni-field ni-full">
          <label class="ni-label" for="status">STATUS</label>
          <select class="ni-select" id="status" name="status">
            <option value="ativa" selected>Ativa</option>
            <option value="inativa">Inativa</option>
            <option value="manutencao">Manutenção</option>
          </select>
        </div>

        <div class="ni-field ni-full">
          <label class="ni-label" for="obs">OBSERVAÇÕES</label>
          <textarea class="ni-textarea" id="obs" name="obs" rows="4" placeholder="Anotações sobre a impressora..."></textarea>
        </div>
      </div>

      <div class="ni-actions">
        <button type="button" class="ni-btn ni-btn-ghost" onclick="history.back()">Cancelar</button>
        <button type="submit" class="ni-btn ni-btn-primary">Cadastrar Impressora</button>
      </div>
    </form>
  </section>
</main>

<script>

const tabs = document.querySelectorAll(".ni-tab");
let tipo = "nf";

const form = document.getElementById("printerForm");

const hiddenType = document.createElement("input");
hiddenType.type = "hidden";
hiddenType.name = "tipo";
hiddenType.value = tipo;
form.appendChild(hiddenType);

tabs.forEach(btn => {
  btn.addEventListener("click", () => {
    tabs.forEach(b => {
      b.classList.remove("active");
      b.setAttribute("aria-selected","false");
    });

    btn.classList.add("active");
    btn.setAttribute("aria-selected","true");

    tipo = btn.dataset.type;
    hiddenType.value = tipo;
  });
});

function validarIP(ip) {
  if (!ip) return true;

  const partes = ip.split(".");
  if (partes.length !== 4) return false;

  return partes.every(p => {
    const n = Number(p);
    return p !== "" && n >= 0 && n <= 255;
  });
}

function validarPorta(porta) {
  if (!porta) return true;

  const n = Number(porta);
  return Number.isInteger(n) && n >= 0 && n <= 65535;
}

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const fd = new FormData(form);
  const data = Object.fromEntries(fd.entries());

  if (!validarIP(data.ip)) {
    alert("IP inválido");
    return;
  }

  if (!validarPorta(data.porta)) {
    alert("Porta inválida");
    return;
  }

  try {

    const res = await fetch("/api/cadastroimpressora", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    });

    const json = await res.json();
    console.log(json);

    form.reset();
    hiddenType.value = tipo;

  } catch (err) {
    console.error(err);
  }
});

</script>