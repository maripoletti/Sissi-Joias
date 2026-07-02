const lista = document.getElementById("listaVendas");
const qtd = document.getElementById("qtdVendas");
const btn = document.getElementById("btnRegistrar");
const main = document.querySelector(".main");
const scanner = document.getElementById("scanner");

let page = 0;
let limit = 10;
let loading = false;
let acabou = false;

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
        page,
        limit
      })
    });

    if (!res.ok) throw new Error("Erro ao buscar vendas");

    const data = await res.json();
    const vendas = Array.isArray(data.sales) ? data.sales : [];

    if (!vendas.length) {
      acabou = true;
      return;
    }

    const html = vendas.map((v) => criarCard({
      ...v,
      OrderDate: new Date(v.OrderDate).toLocaleDateString("pt-BR"),
      Sales: parseFloat(v.Sales)
    })).join("");

    lista.insertAdjacentHTML("beforeend", html);
    qtd.textContent = data.total ?? document.querySelectorAll(".sale-card").length;

    page++;
  } catch (err) {
    console.error(err);

    if (page === 0 && lista) {
      lista.innerHTML = "<p>Não foi possível carregar as vendas.</p>";
      qtd.textContent = 0;
    }
  } finally {
    loading = false;
  }
}

function criarCard(v) {
  const produtos = Array.isArray(v.produtos) ? v.produtos : [];

  const nomes = produtos.length
    ? produtos.map(p => `${p.nome} x ${p.qtd}`).join("<br>")
    : "Sem produtos";

  const qtdTotal = produtos.reduce((acc, p) => acc + Number(p.qtd || 0), 0);

  return `
    <article class="sale-card" data-id="${v.OrderID}">
      <div class="card-left">
        <div class="title-row">
          <h3 class="prod-title">${nomes}</h3>
          <span class="pill">${v.PaymentMethod || "-"}</span>
        </div>

        <div class="meta">
          <b>${v.EmployeeName || "-"}</b> · Cliente: <b>${v.ClienteName || "-"}</b> · Qtd: <b>${qtdTotal}</b>
        </div>

        <div class="date">${v.OrderDate || "-"}</div>
      </div>

      <div class="card-right">
        <div class="price">${formatBRL(Number(v.Sales || 0))}</div>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
          <button class="btn-delete" type="button" onclick="delVenda(${v.OrderID})">🗑 Apagar</button>
        <?php endif; ?>n 
      </div>
    </article>
  `;
}

function formatBRL(valor) {
  return Number(valor).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL"
  });
}

async function delVenda(id) {
  if (!confirm("Deseja realmente excluir esta venda?")) return;

  try {
    const res = await fetch("/api/vendas/delete", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });

    if (!res.ok) {
      throw new Error("Erro ao excluir venda");
    }

    const card = document.querySelector(`.sale-card[data-id="${id}"]`);
    if (card) card.remove();

    const totalCards = document.querySelectorAll(".sale-card").length;
    if (qtd) qtd.textContent = totalCards;
  } catch (err) {
    console.error(err);
    alert("Não foi possível excluir a venda.");
  }
}

if (btn) {
  btn.addEventListener("click", () => {
    window.location.href = "/novavenda";
  });
}

if (main) {
  main.addEventListener("scroll", () => {
    const scroll = main.scrollTop + main.clientHeight;
    const height = main.scrollHeight - 200;

    if (scroll >= height) {
      carregarVendas();
    }
  });
}

if (scanner) {
  scanner.addEventListener("keydown", async (e) => {
    if (e.key !== "Enter") return;

    const codigo = scanner.value.trim();
    scanner.value = "";

    if (!codigo) return;

    try {
      const res = await fetch("/api/novavenda/scan", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cdb: codigo })
      });

      const data = await res.json();

      if (data.success) {
        alert(`Venda registrada: ${data.produto_nome}`);
        page = 0;
        acabou = false;
        lista.innerHTML = "";
        await carregarVendas();
      } else {
        alert("Produto não encontrado");
      }
    } catch (err) {
      console.error("Erro na venda:", err);
      alert("Erro ao registrar venda.");
    }
  });

  scanner.focus();
  setInterval(() => {
    if (document.activeElement !== scanner) {
      scanner.focus();
    }
  }, 500);
}

carregarVendas();