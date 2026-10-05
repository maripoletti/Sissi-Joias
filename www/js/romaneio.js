// ─── Totais ───────────────────────────────────────────────

function atualizarTotais() {

    let totalPecas = 0;
    let valorGeral = 0;

    document.querySelectorAll("tbody tr").forEach(linha => {

        if (linha.classList.contains("categoria")) return;

        const qtd   = parseFloat(linha.querySelector(".qtd").value)   || 0;
        const valor = parseFloat(linha.querySelector(".valor").value)  || 0;
        const total = qtd * valor;

        linha.querySelector(".total").innerText = total.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });

        totalPecas  += qtd;
        valorGeral  += total;
    });

    document.getElementById("pecas").innerText = totalPecas;

    document.getElementById("valorTotal").innerText = valorGeral.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}

document.addEventListener("input", atualizarTotais);


// ─── Adicionar produto ────────────────────────────────────

function adicionarProduto(botao) {

    const linhaCategoria = botao.closest("tr");
    const nomeCategoria  = linhaCategoria.dataset.categoria;

    // Encontra a última linha desta categoria antes da próxima categoria (ou fim)
    let referencia = linhaCategoria;
    let proximo    = linhaCategoria.nextElementSibling;

    while (proximo && !proximo.classList.contains("categoria")) {
        referencia = proximo;
        proximo    = proximo.nextElementSibling;
    }

    const novaLinha = document.createElement("tr");

    novaLinha.innerHTML = `
        <td>${nomeCategoria}</td>
        <td><input type="text" class="nome-produto" placeholder="Nome do produto"></td>
        <td><input class="qtd"   type="number" value="0"  min="0"></td>
        <td><input class="valor" type="number" value="0"  min="0"></td>
        <td class="total">R$ 0,00</td>
        <td class="no-print">
            <button class="btn-remove" onclick="removerProduto(this)">×</button>
        </td>
    `;

    // Insere após a última linha da categoria
    referencia.insertAdjacentElement("afterend", novaLinha);

    // Foca no campo de nome do novo produto
    novaLinha.querySelector(".nome-produto").focus();

    atualizarTotais();
}


// ─── Remover produto ──────────────────────────────────────

function removerProduto(botao) {

    const linha = botao.closest("tr");

    if (!confirm("Remover este produto?")) return;

    linha.remove();
    atualizarTotais();
}


// ─── Gerar PDF ────────────────────────────────────────────

function gerarPDF() {

    const elemento = document.getElementById("romaneio");

    html2pdf()
        .set({
            margin:     10,
            filename:   "romaneio.pdf",
            image:      { type: "jpeg", quality: 1 },
            html2canvas:{ scale: 2 },
            jsPDF:      { unit: "mm", format: "a4", orientation: "portrait" }
        })
        .from(elemento)
        .save();
}


// ─── Inicialização ────────────────────────────────────────

atualizarTotais();