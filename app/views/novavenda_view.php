<!DOCTYPE html> 
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Venda</title>
    <link rel="stylesheet" href="styles/novavenda.css">
</head>
<body>

<div class="container">

    <header class="header">
        <div>
            <h1>Nova Venda</h1>
            <p>Sissi Semi Joias e Acessórios</p>
        </div>
    </header>

    <button class="btn-voltar" onclick="history.back()">← Voltar</button>

    <div class="progress">
        <div class="step active">Produtos</div>
        <div class="step">Cliente</div>
        <div class="step">Finalização</div>
    </div>

    <div id="etapa-produtos">
        <div class="content">

            <div class="left">
                <div class="card">
                    <h2>Selecionar Produtos</h2>
                    <input type="text" id="buscar" placeholder="Buscar produtos">
                    <div id="lista-produtos"></div>
                    <div id="mensagem-vazia" class="empty" style="display:none;">Nenhum produto encontrado</div>
                </div>

                <button class="btn-continuar" onclick="irParaCliente()">Continuar</button>
            </div>

            <div class="right">
                <div class="card">
                    <h2>Carrinho</h2>
                    <div id="carrinho-itens">
                        <div class="empty">Carrinho vazio</div>
                    </div>
                </div>

                <div class="card resumo">
                    <h2>Resumo</h2>
                    <div class="linha">
                        <span>Subtotal</span>
                        <span id="subtotal">R$ 0,00</span>
                    </div>
                    <div class="linha total">
                        <span>Total</span>
                        <span id="total">R$ 0,00</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="etapa-cliente" style="display:none;">
        <div class="card">
            <h2>Dados do Cliente</h2>
            <input type="text" placeholder="Nome do cliente">
            <input type="text" placeholder="CPF">
            <button onclick="irParaFinalizacao()">Continuar</button>
        </div>
    </div>

    <div id="etapa-finalizacao" style="display:none;">
        <div class="card">
            <h2>Finalização</h2>
            <p>Escolha forma de pagamento</p>
            <select>
                <option>Dinheiro</option>
                <option>Cartão</option>
                <option>Pix</option>
            </select>
            <button>Finalizar Venda</button>
        </div>
    </div>

</div>

<script>
const buscar = document.getElementById("buscar");

let produtosRenderizados = [];
let carrinho = [];

function formatarMoeda(valor) {
    return Number(valor).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}

function atualizarResumo() {
    const subtotalEl = document.getElementById("subtotal");
    const totalEl = document.getElementById("total");

    const subtotal = carrinho.reduce((acc, item) => {
        return acc + (Number(item.preco) * item.quantidade);
    }, 0);

    subtotalEl.textContent = formatarMoeda(subtotal);
    totalEl.textContent = formatarMoeda(subtotal);
}

function removerDoCarrinho(id) {
    carrinho = carrinho.filter(item => String(item.id) !== String(id));
    renderCarrinho();
    renderListaProdutos();
}

function alterarQuantidade(id, delta) {
    const item = carrinho.find(prod => String(prod.id) === String(id));
    if (!item) return;

    const estoqueMaximo = Number(item.estoque ?? 0);
    const novaQuantidade = item.quantidade + delta;

    if (novaQuantidade <= 0) {
        removerDoCarrinho(id);
        return;
    }

    if (novaQuantidade > estoqueMaximo) {
        return;
    }

    item.quantidade = novaQuantidade;
    renderCarrinho();
    renderListaProdutos();
}

function adicionarAoCarrinho(produto) {
    const itemExistente = carrinho.find(item => String(item.id) === String(produto.id));

    if (itemExistente) {
        if (itemExistente.quantidade < Number(produto.estoque ?? 0)) {
            itemExistente.quantidade += 1;
        }
    } else {
        carrinho.push({
            ...produto,
            quantidade: 1
        });
    }

    renderCarrinho();
    renderListaProdutos();
}

function renderCarrinho() {
    const carrinhoEl = document.getElementById("carrinho-itens");
    carrinhoEl.innerHTML = "";

    if (carrinho.length === 0) {
        carrinhoEl.innerHTML = `<div class="empty">Carrinho vazio</div>`;
        atualizarResumo();
        return;
    }

    carrinho.forEach(item => {
        const div = document.createElement("div");
        div.className = "item-carrinho";
        div.innerHTML = `
            <div class="carrinho-info">
                <strong>${item.nome}</strong>
                <span>${formatarMoeda(item.preco)} cada</span>
                <small>Estoque: ${item.estoque ?? 0}</small>
            </div>

            <div class="carrinho-acoes">
                <button type="button" onclick="alterarQuantidade('${item.id}', -1)">−</button>
                <span>${item.quantidade}</span>
                <button type="button" onclick="alterarQuantidade('${item.id}', 1)">+</button>
                <button type="button" class="btn-remover" onclick="removerDoCarrinho('${item.id}')">Remover</button>
            </div>
        `;
        carrinhoEl.appendChild(div);
    });

    atualizarResumo();
}

function renderListaProdutos() {
    const lista = document.getElementById("lista-produtos");
    const msg = document.getElementById("mensagem-vazia");

    lista.innerHTML = "";
    msg.style.display = "none";

    if (produtosRenderizados.length === 0) {
        msg.style.display = "block";
        return;
    }

    produtosRenderizados.forEach(produto => {
        const estoque = Number(produto.estoque ?? 0);
        const itemCarrinho = carrinho.find(item => String(item.id) === String(produto.id));
        const quantidadeNoCarrinho = itemCarrinho ? itemCarrinho.quantidade : 0;
        const semEstoque = estoque <= 0;
        const esgotadoNoCarrinho = quantidadeNoCarrinho >= estoque;

        const div = document.createElement("div");
        div.className = "produto-item";

        if (semEstoque) {
            div.classList.add("sem-estoque");
        }

        div.innerHTML = `
            <div class="produto-dados">
                <strong>${produto.nome}</strong>
                <span>${formatarMoeda(produto.preco)}</span>
                <small>Estoque: ${estoque}</small>
            </div>
            <button type="button" ${semEstoque || esgotadoNoCarrinho ? "disabled" : ""}>
                ${semEstoque ? "Sem estoque" : esgotadoNoCarrinho ? "Limite atingido" : "Adicionar"}
            </button>
        `;

        const botao = div.querySelector("button");

        if (!semEstoque && !esgotadoNoCarrinho) {
            botao.addEventListener("click", () => adicionarAoCarrinho(produto));
            div.addEventListener("click", (e) => {
                if (e.target.tagName !== "BUTTON") {
                    adicionarAoCarrinho(produto);
                }
            });
        }

        lista.appendChild(div);
    });
}

async function render() {
    const texto = buscar.value.trim();
    const lista = document.getElementById("lista-produtos");
    const msg = document.getElementById("mensagem-vazia");

    lista.innerHTML = "";
    msg.style.display = "none";

    try {
        const res = await fetch("/novavenda", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ texto })
        });

        if (!res.ok) {
            throw new Error("Resposta inválida");
        }

        const data = await res.json();
        produtosRenderizados = Array.isArray(data) ? data : [];
    } catch (err) {
        produtosRenderizados = [];
    }

    renderListaProdutos();
}

buscar.addEventListener("input", render);

function atualizarBarra(indice) {
    const steps = document.querySelectorAll(".progress .step");
    steps.forEach((step, i) => {
        step.classList.toggle("active", i === indice);
    });
}

function irParaCliente() {
    if (carrinho.length === 0) {
        alert("Selecione pelo menos um produto para continuar.");
        return;
    }

    document.getElementById("etapa-produtos").style.display = "none";
    document.getElementById("etapa-cliente").style.display = "block";
    document.getElementById("etapa-finalizacao").style.display = "none";
    atualizarBarra(1);
}

function irParaFinalizacao() {
    document.getElementById("etapa-produtos").style.display = "none";
    document.getElementById("etapa-cliente").style.display = "none";
    document.getElementById("etapa-finalizacao").style.display = "block";
    atualizarBarra(2);
}

atualizarBarra(0);
render();
</script>

</body>
</html>