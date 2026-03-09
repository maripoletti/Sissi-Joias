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

    window.location.href = "/paineldecontrole";
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

(function initPainel() {
  const calDias = document.getElementById("calDias");
  const calTitulo = document.getElementById("calTitulo");
  const prevMes = document.getElementById("prevMes");
  const nextMes = document.getElementById("nextMes");
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
  const calDetail = document.getElementById("calDetail");
  const detailTitle = document.getElementById("detailTitle");
  const detailBody = document.getElementById("detailBody");
  const btnFecharDetalhe = document.getElementById("btnFecharDetalhe");
  const listaAniversarios = document.getElementById("listaAniversarios");

  if (!calDias || !calTitulo || !prevMes || !nextMes) return;

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

  let eventos = [];
  let view = new Date();

  // --- FETCH EVENTOS ---
  async function carregarEventos() {
    try {
      const res = await fetch("/api/eventos");
      if (!res.ok) throw new Error("Erro ao carregar eventos");
      const data = await res.json();
      eventos = Array.isArray(data) ? data : [];
      renderCalendar();
      renderizarListaAniversarios();
    } catch (e) {
      console.error(e);
    }
  }

  async function adicionarEvento(evento) {
    try {
      const res = await fetch("/api/eventos/add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(evento)
      });
      if (!res.ok) throw new Error("Erro ao adicionar evento");
      await carregarEventos();
    } catch (e) {
      console.error(e);
    }
  }

  async function apagarEvento(id) {
    try {
      const res = await fetch("/api/eventos/del", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
      });
      if (!res.ok) throw new Error("Erro ao apagar evento");
      await carregarEventos();
    } catch (e) {
      console.error(e);
    }
  }

  // --- MODAL ---
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
  modal?.addEventListener("click", (e) => { if (e.target === modal) fecharModal(); });
  btnAddEvento?.addEventListener("click", () => abrirModal());
  btnFecharModal?.addEventListener("click", fecharModal);
  btnCancelar?.addEventListener("click", fecharModal);

  // --- DETALHE ---
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
        const comentario = e.comentario || "(Sem descrição)";
        const titulo = e.titulo || e.text || "Evento";
        return `
          <div class="detail-item" style="padding:10px;border:1px solid rgba(0,0,0,.06);border-radius:12px;margin-bottom:8px;background:#fff;">
            <div style="display:flex;justify-content:space-between;gap:10px;">
              <strong>${titulo}</strong>
              <span style="opacity:.7;font-weight:700;">${tipoLabel}${horaLabel}</span>
            </div>
            <div style="margin-top:6px;opacity:.9;">${comentario}</div>
            <div style="margin-top:8px;">
              <button type="button" data-del="${e.id}" style="border:none;background:#f2f2f2;padding:8px 10px;border-radius:10px;cursor:pointer;">🗑 Apagar</button>
            </div>
          </div>
        `;
      }).join("");

      detailBody.querySelectorAll("[data-del]").forEach((btn) => {
        btn.addEventListener("click", (ev) => {
          ev.stopPropagation();
          const id = btn.getAttribute("data-del");
          if (!id) return;
          if (!confirm("Excluir este evento?")) return;
          apagarEvento(id);
          fecharDetalhe();
        });
      });
    }
    calDetail.style.display = "block";
  }

  // --- ANIVERSÁRIOS ---
  function renderizarListaAniversarios() {
    if (!listaAniversarios) return;
    const anivs = eventos.filter((e) => isAniversario(e.type)).sort((a,b)=>a.date.localeCompare(b.date));
    if (!anivs.length) {
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

    listaAniversarios.querySelectorAll(".birth-item").forEach((item) => {
      item.addEventListener("click", () => {
        const id = item.dataset.id;
        const evItem = eventos.find((x) => x.id === id);
        if (evItem?.date) abrirDetalheDoDia(evItem.date);
      });
    });

    listaAniversarios.querySelectorAll(".birth-del").forEach((btn) => {
      btn.addEventListener("click", (ev) => {
        ev.stopPropagation();
        const card = btn.closest(".birth-item");
        const id = card?.dataset?.id;
        if (!id) return;
        if (!confirm("Apagar este aniversariante?")) return;
        apagarEvento(id);
        fecharDetalhe();
      });
    });
  }

  // --- CALENDÁRIO ---
  function renderCalendar() {
    const year = view.getFullYear();
    const month = view.getMonth();
    const first = new Date(year, month, 1);
    const startDay = first.getDay();
    const monthName = first.toLocaleDateString("pt-BR",{ month:"long", year:"numeric"});
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
      num.addEventListener("click", (evt) => {
        evt.stopPropagation();
        abrirDetalheDoDia(el.dataset.date);
      });
      el.appendChild(num);

      const key = iso(cellDate);
      const evs = eventos.filter((e) => e.date === key);
      evs.forEach((e) => {
        const pill = document.createElement("div");
        pill.className = "pill" + (isAniversario(e.type) ? " aniver" : "");
        pill.textContent = e.text || "Evento";
        pill.addEventListener("click", (evt) => {
          evt.stopPropagation();
          abrirDetalheDoDia(key);
          if (evt.altKey) {
            if (!confirm("Excluir este evento?")) return;
            apagarEvento(e.id);
          }
        });
        el.appendChild(pill);
      });

      el.addEventListener("click", () => abrirModal(el.dataset.date));
      calDias.appendChild(el);
    }
  }

  prevMes.addEventListener("click", () => { view = new Date(view.getFullYear(), view.getMonth()-1,1); renderCalendar(); fecharDetalhe(); });
  nextMes.addEventListener("click", () => { view = new Date(view.getFullYear(), view.getMonth()+1,1); renderCalendar(); fecharDetalhe(); });

  formEvento?.addEventListener("submit", (e) => {
    e.preventDefault();
    const titulo = (evtTitulo?.value || "").trim();
    const data = normalizarData(evtData?.value || "");
    const hora = (evtHora?.value || "").trim();
    const tipo = (evtTipo?.value || "outro").trim();
    const comentario = (evtComentario?.value || "").trim();
    if (!titulo || !data) { alert("Preencha Título e Data."); return; }
    let text = "Evento";
    if (tipo === "aniversario") text = "Aniver";
    else if (tipo === "reserva") text = "Reserva";
    else if (tipo === "lembrete") text = "Lembrete";
    const novo = { id: gerarId(), date: data, type: tipo, text, titulo, hora, comentario, createdAt: new Date().toISOString() };
    adicionarEvento(novo);
    fecharModal();
    const [y,m] = data.split("-").map(Number);
    if (y && m) view = new Date(y, m-1,1);
  });

  // --- INICIALIZA ---
  carregarEventos();


  async function carregarTopVendedoras() {
    const res = await fetch("/api/relatorios");
    if (!res.ok) return;
    const data = await res.json();
    const container = document.querySelector(".top-list");
    if (!container) return;
    container.innerHTML = "";
    if (data.vendedoras && data.vendedoras.length) {
      const maiorVenda = Math.max(...data.vendedoras.map(v => Number(v.valor)));
      data.vendedoras.sort((a,b)=>Number(b.valor)-Number(a.valor)).forEach((v,i)=>{
        const valor = Number(v.valor);
        const percentual = maiorVenda ? (valor/maiorVenda)*100 : 0;
        let rankClass = "";
        if(i===0) rankClass="gold";
        else if(i===1) rankClass="roxo";
        else if(i===2) rankClass="lilas";
        else rankClass="cinza";
        const itemHTML = `
          <div class="top-item">
            <span class="rank ${rankClass}">${i+1}</span>
            <span class="name">${v.nome}</span>
            <span class="valor">R$ ${valor.toFixed(0)}</span>
          </div>
          <div class="barra"><span class="fill ${rankClass}" style="width:${percentual}%"></span></div>
        `;
        container.insertAdjacentHTML("beforeend", itemHTML);
      });
    } else container.innerHTML=`<p class="muted">Nenhuma vendedora encontrada.</p>`;
  }
  document.addEventListener("DOMContentLoaded", ()=>{carregarTopVendedoras();});

})();

const lista = document.getElementById("listaVendas");
const qtd = document.getElementById("qtdVendas");

let page = 0;
let limit = 10;
let loading = false;
let acabou = false;
let total = 0;

async function carregarVendas() {

    if (!lista || !qtd) return;
    if (loading || acabou) return;

    loading = true;

    try {

        const res = await fetch("/api/vendas", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                page: page,
                limit: limit
            })
        });

        if (!res.ok) throw new Error("Erro ao buscar vendas");

        const data = await res.json();

        const vendas = data.sales;

        if (!vendas.length) {
            acabou = true;
            return;
        }

        const html = vendas.map(v => criarCard({
            produto: v.ProductName,
            pagamento: v.PaymentMethod,
            vendedora: v.EmployeeName,
            cliente: v.ClienteName,
            qtd: v.Quantity,
            data: new Date(v.OrderDate).toLocaleDateString("pt-BR"),
            valor: parseFloat(v.Sales)
        })).join("");

        lista.insertAdjacentHTML("beforeend", html);

        qtd.textContent = data.total;

        page++;

    } catch (err) {

        console.error(err);

        if (page === 0) {
            lista.innerHTML = "<p>Não foi possível carregar as vendas.</p>";
            qtd.textContent = 0;
        }

    }

    loading = false;
}

function criarCard(v) {
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
        <div class="price">${formatBRL(v.valor)}</div>
    </article>
    `;
}

function formatBRL(valor) {
    return valor.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

const btn = document.getElementById("btnRegistrar");
if (btn) {
    btn.addEventListener("click", () => {
        window.location.href = "/novavenda";
    });
}

const main = document.querySelector(".main");

main.addEventListener("scroll", () => {

    const scroll = main.scrollTop + main.clientHeight;
    const height = main.scrollHeight - 200;

    if (scroll >= height) {
        carregarVendas();
    }

});

const scanner = document.getElementById("scanner");

scanner.addEventListener("keydown", async (e) => {
  if (e.key === "Enter") {
    const codigo = scanner.value.trim();
    scanner.value = ""; 

    try {
      const res = await fetch("/api/novavenda/scan", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cdb: codigo })
      });

      const data = await res.json();

      if (data.success) {
        alert(`Venda registrada: ${data.produto_nome}`);
      } else {
        alert("Produto não encontrado");
      }
    } catch (err) {
      console.error("Erro na venda:", err);
    }
  }
});

scanner.focus();
setInterval(() => scanner.focus(), 500);

carregarVendas();