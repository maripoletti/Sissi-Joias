(function initLogin() {
  const form = document.querySelector("#loginForm");
  if (!form) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = document.querySelector("#email")?.value.trim();
    const senha = document.querySelector("#senha")?.value.trim();

    if (!email || !senha) {
      alert("Preencha todos os campos!");
      return;
    }

    // Aqui depois liga no backend
  });
})();


(function initDataTopo() {
  const dataElemento = document.getElementById("data-atual");
  if (!dataElemento) return;

  const hoje = new Date();
  const opcoes = { weekday: "long", day: "2-digit", month: "long" };

  let dataFormatada = hoje.toLocaleDateString("pt-BR", opcoes);
  dataFormatada = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);

  dataElemento.textContent = dataFormatada;
})();


(function initCadastro() {
  const cadastroForm = document.getElementById("cadastroForm");
  const inputTelefone = document.querySelector('input[name="telefone"]');

  if (inputTelefone) {
    inputTelefone.addEventListener("input", () => {
      inputTelefone.value = inputTelefone.value.replace(/\D/g, "").slice(0, 11);
    });
  }

  if (!cadastroForm) return;

  cadastroForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const nome = document.querySelector('input[name="nome"]')?.value.trim();
    const emailCadastro = document.querySelector('input[name="email"]')?.value.trim();
    const senhaCadastro = document.querySelector('input[name="senha"]')?.value.trim();
    const confirmarSenha = document.querySelector('input[name="confirmar_senha"]')?.value.trim();
    const telefoneValor = inputTelefone ? inputTelefone.value.trim() : "";

    if (!nome || !emailCadastro || !senhaCadastro || !confirmarSenha || !telefoneValor) {
      alert("Preencha todos os campos!");
      return;
    }

    if (senhaCadastro.length < 6) {
      alert("Senha muito curta.");
      return;
    }

    if (senhaCadastro !== confirmarSenha) {
      alert("As senhas não coincidem.");
      return;
    }

    if (telefoneValor.length !== 11) {
      alert("Telefone inválido.");
      return;
    }

    window.location.href = "dashboard.php";
  });
})();

(function initBuscaProdutos() {
  const inputBuscar = document.getElementById("buscar");
  const lista = document.getElementById("lista-produtos");
  const mensagemVazia = document.getElementById("mensagem-vazia");
  const produtosUrl = document.querySelector("[data-produtos-url]")?.dataset?.produtosUrl;

  if (!inputBuscar || !lista || !mensagemVazia || !produtosUrl) return;

  function renderizarProdutos(produtos) {
    lista.innerHTML = "";

    if (!Array.isArray(produtos) || produtos.length === 0) {
      mensagemVazia.style.display = "block";
      return;
    }

    mensagemVazia.style.display = "none";

    produtos.forEach((p) => {
      const nome = p.nome ?? "Produto";
      const preco = Number(p.preco ?? 0);

      const item = document.createElement("div");
      item.className = "produto-item";
      item.textContent = `${nome} - R$ ${preco.toFixed(2)}`;
      lista.appendChild(item);
    });
  }

  async function buscarProdutos(termo) {
    try {
      const resp = await fetch(`${produtosUrl}?search=${encodeURIComponent(termo)}`);
      if (!resp.ok) throw new Error(`Falha ao buscar produtos (HTTP ${resp.status})`);

      const produtos = await resp.json();
      renderizarProdutos(produtos);
    } catch (e) {
      console.error(e);
      renderizarProdutos([]);
    }
  }

  let timer = null;
  inputBuscar.addEventListener("input", () => {
    clearTimeout(timer);
    const termo = inputBuscar.value.trim();
    timer = setTimeout(() => buscarProdutos(termo), 300);
  });

  buscarProdutos("");
})();

(function initCalendario() {
  const calDias = document.getElementById("calDias");
  const calTitulo = document.getElementById("calTitulo");
  const prevMes = document.getElementById("prevMes");
  const nextMes = document.getElementById("nextMes");

  // Modal / formulário
  const btnAddEvento = document.getElementById("btnAddEvento");
  const modal = document.getElementById("modalEvento");
  const btnFecharModal = document.getElementById("btnFecharModal");
  const btnCancelar = document.getElementById("btnCancelar");
  const formEvento = document.getElementById("formEvento");

  const evtTitulo = document.getElementById("eventoTitulo");
  const evtData = document.getElementById("eventoData");
  const evtHora = document.getElementById("eventoHora");
  const evtTipo = document.getElementById("eventoTipo");
  const evtComentario = document.getElementById("eventoComentario");

  // painel detalhe + lista aniversários
  const calDetail = document.getElementById("calDetail");
  const detailTitle = document.getElementById("detailTitle");
  const detailBody = document.getElementById("detailBody");
  const btnFecharDetalhe = document.getElementById("btnFecharDetalhe");
  const listaAniversarios = document.getElementById("listaAniversarios");

  // se a página não tem calendário, não roda nada (sem erro)
  if (!calDias || !calTitulo || !prevMes || !nextMes) return;

  const STORAGE_KEY = "eventos_calendario_v1";

  function gerarId() {
    return "ev_" + Math.random().toString(16).slice(2) + Date.now().toString(16);
  }

  function iso(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${y}-${m}-${day}`;
  }

  function normalizarData(valor) {
    const v = (valor || "").trim();

    if (/^\d{4}-\d{2}-\d{2}$/.test(v)) return v;

    if (/^\d{2}\/\d{2}\/\d{4}$/.test(v)) {
      const [dd, mm, yyyy] = v.split("/");
      return `${yyyy}-${mm}-${dd}`;
    }

    return "";
  }

  function formatarDataBR(yyyy_mm_dd) {
    if (!yyyy_mm_dd) return "";
    const [y, m, d] = yyyy_mm_dd.split("-");
    return `${d}/${m}/${y}`;
  }

  function isAniversario(tipo) {
    const t = (tipo || "").toLowerCase();
    return t === "aniversario" || t === "aniver";
  }

  function loadEventos() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      console.error("Erro ao ler eventos do localStorage:", e);
      return [];
    }
  }

  function saveEventos(eventos) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(eventos));
  }

  let eventos = loadEventos();

  // seeds só pra testar
  const seed = [
    { date: "2026-02-28", type: "reserva", text: "Reserva", titulo: "Reserva", comentario: "" },
    { date: "2000-05-12", type: "aniversario", text: "Aniver", titulo: "Aniversário Fulano", comentario: "Cliente prefere Whats, gosta de dourado." }
  ];

  seed.forEach((s) => {
    const existe = eventos.some((e) => e.date === s.date && e.type === s.type && e.text === s.text);
    if (!existe) eventos.push({ id: gerarId(), hora: "", createdAt: new Date().toISOString(), ...s });
  });

  saveEventos(eventos);

  let view = new Date();

  function abrirModal(preencherData = "") {
    if (!modal || !formEvento) return;

    modal.classList.remove("hidden");

    if (evtData && preencherData) evtData.value = preencherData;

    evtTitulo?.focus();
  }

  function fecharModal() {
    if (!modal || !formEvento) return;
    modal.classList.add("hidden");
    formEvento.reset();
  }

  modal?.addEventListener("click", (e) => {
    if (e.target === modal) fecharModal();
  });

  btnAddEvento?.addEventListener("click", () => abrirModal());
  btnFecharModal?.addEventListener("click", fecharModal);
  btnCancelar?.addEventListener("click", fecharModal);

  function fecharDetalhe() {
    if (!calDetail) return;
    calDetail.style.display = "none";
  }

  btnFecharDetalhe?.addEventListener("click", fecharDetalhe);

  function abrirDetalheDoDia(dataISO) {
    if (!calDetail || !detailTitle || !detailBody) return;

    const evs = eventos.filter((e) => e.date === dataISO);

    detailTitle.textContent = `Dia ${formatarDataBR(dataISO)}`;

    if (evs.length === 0) {
      detailBody.innerHTML = `<p class="muted">Sem eventos nesse dia.</p>`;
    } else {
      detailBody.innerHTML = evs.map((e) => {
        const tipoLabel = isAniversario(e.type) ? "Aniversário" : (e.type || "Evento");
        const horaLabel = e.hora ? ` • ${e.hora}` : "";
        const comentario = e.comentario ? e.comentario : "(Sem descrição)";
        const titulo = e.titulo || e.text || "Evento";

        return `
          <div class="detail-item" style="padding:10px;border:1px solid rgba(0,0,0,.06);border-radius:12px;margin-bottom:8px;background:#fff;">
            <div style="display:flex;justify-content:space-between;gap:10px;">
              <strong>${titulo}</strong>
              <span style="opacity:.7;font-weight:700;">${tipoLabel}${horaLabel}</span>
            </div>
            <div style="margin-top:6px;opacity:.9;">${comentario}</div>
            <div style="margin-top:8px;">
              <button type="button" data-del="${e.id}" style="border:none;background:#f2f2f2;padding:8px 10px;border-radius:10px;cursor:pointer;">
                🗑 Apagar
              </button>
            </div>
          </div>
        `;
      }).join("");

      // botão apagar dentro do detalhe
      detailBody.querySelectorAll("[data-del]").forEach((btn) => {
        btn.addEventListener("click", (ev) => {
          ev.stopPropagation();
          const id = btn.getAttribute("data-del");
          if (!id) return;

          const ok = confirm("Excluir este evento?");
          if (!ok) return;

          eventos = eventos.filter((x) => x.id !== id);
          saveEventos(eventos);
          renderCalendar();
          renderizarListaAniversarios();
          fecharDetalhe();
        });
      });
    }

    calDetail.style.display = "block";
  }

  // LISTA COM BOTÃO DE APAGAR (🗑) DO LADO
  function renderizarListaAniversarios() {
    if (!listaAniversarios) return;

    const anivs = eventos
      .filter((e) => isAniversario(e.type))
      .sort((a, b) => (a.date || "").localeCompare(b.date || ""));

    if (anivs.length === 0) {
      listaAniversarios.innerHTML = `<p class="muted">Nenhum aniversariante cadastrado.</p>`;
      return;
    }

    listaAniversarios.innerHTML = anivs.map((e) => `
      <div class="birth-item" data-id="${e.id}">
        <span class="birth-name">${e.titulo || "Cliente"}</span>

        <div class="birth-right">
          <span class="birth-date">${formatarDataBR(e.date)}</span>
          <button class="birth-del" type="button" title="Apagar">🗑</button>
        </div>
      </div>
    `).join("");

    // clica no item abre o detalhe do dia (pra ler descrição)
    listaAniversarios.querySelectorAll(".birth-item").forEach((item) => {
      item.addEventListener("click", () => {
        const id = item.dataset.id;
        const evItem = eventos.find((x) => x.id === id);
        if (evItem?.date) abrirDetalheDoDia(evItem.date);
      });
    });

    // clica no 🗑 apaga
    listaAniversarios.querySelectorAll(".birth-del").forEach((btn) => {
      btn.addEventListener("click", (ev) => {
        ev.stopPropagation();
        const card = btn.closest(".birth-item");
        const id = card?.dataset?.id;
        if (!id) return;

        const ok = confirm("Apagar este aniversariante?");
        if (!ok) return;

        eventos = eventos.filter((x) => x.id !== id);
        saveEventos(eventos);
        renderCalendar();
        renderizarListaAniversarios();
        fecharDetalhe();
      });
    });
  }

  function renderCalendar() {
    const year = view.getFullYear();
    const month = view.getMonth();

    const first = new Date(year, month, 1);
    const startDay = first.getDay(); // 0 dom

    const monthName = first.toLocaleDateString("pt-BR", {
      month: "long",
      year: "numeric"
    });

    calTitulo.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);
    calDias.innerHTML = "";

    const totalCells = 42;

    for (let i = 0; i < totalCells; i++) {
      const cellDate = new Date(year, month, 1 + (i - startDay));
      const inMonth = cellDate.getMonth() === month;

      const el = document.createElement("div");
      el.className = "day" + (inMonth ? "" : " muted");
      el.dataset.date = iso(cellDate);

      const num = document.createElement("div");
      num.className = "num";
      num.textContent = cellDate.getDate();

      // clicar no número abre detalhe (ler descrição)
      num.addEventListener("click", (evt) => {
        evt.stopPropagation();
        abrirDetalheDoDia(el.dataset.date);
      });

      el.appendChild(num);

      const key = iso(cellDate);
      const evs = eventos.filter((e) => e.date === key);

      evs.forEach((e) => {
        const pill = document.createElement("div");
        const aniver = isAniversario(e.type);

        pill.className = "pill" + (aniver ? " aniver" : "");
        pill.textContent = e.text || "Evento";

        // clique normal: ler detalhe
        // ALT + clique: apagar
        pill.addEventListener("click", (evt) => {
          evt.stopPropagation();

          abrirDetalheDoDia(key);

          if (evt.altKey) {
            const ok = confirm("Excluir este evento?");
            if (!ok) return;

            eventos = eventos.filter((x) => x.id !== e.id);
            saveEventos(eventos);
            renderCalendar();
            renderizarListaAniversarios();
            fecharDetalhe();
          }
        });

        el.appendChild(pill);
      });

      // clicar no dia abre modal pra criar evento
      el.addEventListener("click", () => abrirModal(el.dataset.date));

      calDias.appendChild(el);
    }

    renderizarListaAniversarios();
  }

  prevMes.addEventListener("click", () => {
    view = new Date(view.getFullYear(), view.getMonth() - 1, 1);
    renderCalendar();
    fecharDetalhe();
  });

  nextMes.addEventListener("click", () => {
    view = new Date(view.getFullYear(), view.getMonth() + 1, 1);
    renderCalendar();
    fecharDetalhe();
  });

  formEvento?.addEventListener("submit", (e) => {
    e.preventDefault();

    const titulo = (evtTitulo?.value || "").trim();
    const data = normalizarData(evtData?.value || "");
    const hora = (evtHora?.value || "").trim();
    const tipo = (evtTipo?.value || "outro").trim();
    const comentario = (evtComentario?.value || "").trim();

    if (!titulo || !data) {
      alert("Preencha Título e Data.");
      return;
    }

    let text = "Evento";
    if (tipo === "aniversario" || tipo === "aniver") text = "Aniver";
    else if (tipo === "reserva") text = "Reserva";
    else if (tipo === "lembrete") text = "Lembrete";

    const novo = {
      id: gerarId(),
      date: data,
      type: tipo,
      text,
      titulo,
      hora,
      comentario,
      createdAt: new Date().toISOString()
    };

    eventos.push(novo);
    saveEventos(eventos);

    fecharModal();

    const [y, m] = data.split("-").map(Number);
    if (y && m) view = new Date(y, m - 1, 1);

    renderCalendar();
    abrirDetalheDoDia(data);
  });

  renderCalendar();
  renderizarListaAniversarios();
})();

const vendas = [
{
produto:"Anel Solitário Ouro 18k",
pagamento:"Pix",
vendedora:"Ana Paula",
cliente:"Maria Silva",
qtd:1,
data:"28/02/2026",
valor:420
},
{
produto:"Pulseira Tennis Cristal",
pagamento:"Cartão Crédito",
vendedora:"Fernanda Lima",
cliente:"Carla Souza",
qtd:1,
data:"01/03/2026",
valor:250
},
{
produto:"Colar Coração Folheado",
pagamento:"Dinheiro",
vendedora:"Ana Paula",
cliente:"Juliana Costa",
qtd:2,
data:"01/03/2026",
valor:178
},
{
produto:"Conjunto Pérola Clássico",
pagamento:"Pix",
vendedora:"Beatriz Mendes",
cliente:"Patrícia Rocha",
qtd:1,
data:"02/03/2026",
valor:290
}
];

function formatBRL(valor){
return valor.toLocaleString("pt-BR",{style:"currency",currency:"BRL"});
}

function criarCard(v){
return `
<article class="sale-card">

<div>
<div class="title-row">
<h3 class="prod-title">${v.produto}</h3>
<span class="pill">${v.pagamento}</span>
</div>

<div class="meta">
<b>${v.vendedora}</b> · Cliente: <b>${v.cliente}</b> · Qtd: <b>${v.qtd}</b>
</div>

<div class="date">${v.data}</div>
</div>

<div class="price">
${formatBRL(v.valor)}
</div>

</article>
`;
}

function carregarVendas(){

const lista = document.getElementById("listaVendas");
const qtd = document.getElementById("qtdVendas");

if(!lista || !qtd) return;

lista.innerHTML = vendas.map(criarCard).join("");
qtd.textContent = vendas.length;

}

const btn = document.getElementById("btnRegistrar");

if(btn){
btn?.addEventListener("click", () => {
window.location.href = "novavenda.php";
});
}

carregarVendas();