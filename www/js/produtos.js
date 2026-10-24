const q = document.getElementById("q");
const cat = document.getElementById("cat");
const price = document.getElementById("price");
const sort = document.getElementById("sort");
const grid = document.getElementById("grid");
const count = document.getElementById("count");
const main = document.querySelector(".main");

const tamanho = document.getElementById("tamanho");
const cor = document.getElementById("cor");
const pesoBanho = document.getElementById("pesoBanho");
const milesimosBanho = document.getElementById("milesimosBanho");

// Editar produto
const modalEdit = document.getElementById("modalEdit");
const formEdit = document.getElementById("formEdit");
const editId = document.getElementById("editId");
const editNome = document.getElementById("editNome");
const editCategoria = document.getElementById("editCategoria");
const editPreco = document.getElementById("editPreco");
const editEstoque = document.getElementById("editEstoque");
const editTamanho = document.getElementById("editTamanho");
const editCor = document.getElementById("editCor");
const editPesoBanho = document.getElementById("editPesoBanho");
const editMilesimosBanho = document.getElementById("editMilesimosBanho");
const editBaixa = document.getElementById("editBaixa");
const editFoto = document.getElementById("editFoto");
const editPreview = document.getElementById("editPreview");

// Adicionar produto
const modalAdd = document.getElementById("modalAdd");
const formAdd = document.getElementById("formAdd");
const addNome = document.getElementById("addNome");
const addCategoria = document.getElementById("addCategoria");
const addPreco = document.getElementById("addPreco");
const addEstoque = document.getElementById("addEstoque");
const addTamanho = document.getElementById("addTamanho");
const addCor = document.getElementById("addCor");
const addPesoBanho = document.getElementById("addPesoBanho");
const addMilesimosBanho = document.getElementById("addMilesimosBanho");
const addFoto = document.getElementById("addFoto");
const addPreview = document.getElementById("addPreview");

// Envio para revendedora
const modalEnvio = document.getElementById("modalEnvio");
const formEnvio = document.getElementById("formEnvio");
const revendedoraSelect = document.getElementById("revendedoraSelect");
const buscaEnvio = document.getElementById("buscaEnvio");
const nomearMaleta = document.getElementById("nomearMaleta");
const listaProdutosEnvio = document.getElementById("listaProdutosEnvio");
const itensSelecionadosEnvio = document.getElementById("itensSelecionadosEnvio");

// Categorias
const modalCategorias = document.getElementById("modalCategorias");
const listaCategorias = document.getElementById("listaCategorias");
const novaCategoria = document.getElementById("novaCategoria");

// Cupons por produto
const modalCupomEnvio = document.getElementById("modalCupomEnvio");
const formCupomEnvio = document.getElementById("formCupomEnvio");
const cupomProdutoId = document.getElementById("cupomProdutoId");
const cupomSelecionado = document.getElementById("cupomSelecionado");
const cupomPrevia = document.getElementById("cupomPrevia");
const salvarA4Pasta = document.getElementById("salvarA4Pasta");
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let cuponsRevendedora = [];
let enviandoMaleta = false;
let tentativaEnvio = null;

let produtos = [];
let produtosEnvio = [];
let categorias = [];
let revendedoras = [];
let itensEnvio = [];
let cuponsPendentesEnvio = {};

let page = 0;
const limit = 12;
let isLoading = false;
let acabou = false;

let imagemFinalFile = null;
let imagemFinalBlob = null;

const userRole = Number(window.userData?.role ?? 0);

function escapeHTML(value) {
    return String(value ?? "").replace(/[&<>"']/g, char => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    })[char]);
}

function formatarPreco(valor) {
    return Number(valor || 0).toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

async function respostaJSON(res) {
    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
        throw new Error(data.message || data.erro || data.mensagem || "Erro na requisição.");
    }

    return data;
}

// ==================== LISTAGEM DE PRODUTOS ====================

function checkScroll() {
    if (main.scrollTop + main.clientHeight >= main.scrollHeight - 200) {
        render();
    }
}

main.addEventListener("scroll", checkScroll);
window.addEventListener("load", checkScroll);

async function render(reset = false) {
    if (isLoading) return;

    if (reset) {
        page = 0;
        acabou = false;
        produtos = [];
        grid.innerHTML = "";
    }

    if (acabou) return;

    isLoading = true;

    try {
        const res = await fetch("/api/produtos", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                text: q.value.trim(),
                categoria: cat.value,
                price: price.value,
                sort: sort.value,
                tamanho: tamanho.value.trim(),
                cor: cor.value.trim(),
                peso_banho: pesoBanho.value.trim(),
                milesimos_banho: milesimosBanho.value.trim(),
                page,
                limit
            })
        });

        const data = await respostaJSON(res);
        const novos = Array.isArray(data.produtos) ? data.produtos : [];
        const total = Number(data.total ?? novos.length);

        if (novos.length < limit) acabou = true;

        produtos = [...produtos, ...novos];
        count.textContent = `Mostrando ${produtos.length} de ${total} produto(s)`;

        grid.innerHTML += novos.map(p => `
            <article class="product">
                <div class="thumb">
                    <img src="${escapeHTML(p.img)}" alt="${escapeHTML(p.nome)}">
                    ${Number(p.estoque) <= 3
                        ? '<span class="badge">Baixo estoque</span>'
                        : ""}
                </div>

                <div class="info">
                    <h3 title="${escapeHTML(p.nome)}">${escapeHTML(p.nome)}</h3>

                    <div class="meta">
                        <p>• Estoque: ${Number(p.estoque || 0)}</p>

                        ${userRole === 2
                            ? `<p>• Estoque em uso: ${Number(p.estoque_em_uso || 0)}</p>`
                            : ""}

                        ${p.categoria ? `<p>• Categoria: ${escapeHTML(p.categoria)}</p>` : ""}
                        ${p.tamanho ? `<p>• Tamanho: ${escapeHTML(p.tamanho)}</p>` : ""}
                        ${p.cor ? `<p>• Cor: ${escapeHTML(p.cor)}</p>` : ""}
                        ${p.peso_banho ? `<p>• Peso banho: ${escapeHTML(p.peso_banho)}</p>` : ""}
                        ${p.milesimos_banho ? `<p>• Milésimos: ${escapeHTML(p.milesimos_banho)}</p>` : ""}
                    </div>

                    <div class="price">R$ ${formatarPreco(p.preco)}</div>

                    <div class="actions">
                        ${userRole === 2 ? `
                            <button class="btn btn-editar" type="button"
                                onclick="abrirModal(${Number(p.id)})">Editar</button>

                            <button class="btn btn-outline" type="button"
                                onclick="formDel(${Number(p.id)})">Excluir</button>
                        ` : ""}

                        <button class="btn btn-outline" type="button"
                            onclick="imprimirEtiqueta(${Number(p.id)})">Etiqueta</button>
                    </div>
                </div>
            </article>
        `).join("");

        page++;
        atualizarListaProdutosEnvio();

    } catch (err) {
        console.error("Erro ao carregar produtos:", err);

        if (reset) {
            grid.innerHTML = "<p>Erro ao carregar produtos. Tente novamente.</p>";
        }
    } finally {
        isLoading = false;

        if (!acabou && main) {
            requestAnimationFrame(checkScroll);
        }
    }
}

if (main) {
    main.addEventListener("scroll", () => {
        if (main.scrollTop + main.clientHeight >= main.scrollHeight - 200) {
            render();
        }
    });
}

// ==================== EDITAR PRODUTO ====================

function abrirModal(id) {
    const prod = produtos.find(p => Number(p.id) === Number(id));
    if (!prod) return;

    editId.value = prod.id;
    editNome.value = prod.nome || "";
    editCategoria.value = prod.categoria_id ?? "";
    editPreco.value = prod.preco ?? "";
    editEstoque.value = prod.estoque ?? 0;
    editTamanho.value = prod.tamanho || "";
    editCor.value = prod.cor || "";
    editPesoBanho.value = prod.peso_banho || "";
    editMilesimosBanho.value = prod.milesimos_banho || "";
    editFoto.value = "";
    editBaixa.value = "";

    if (editPreview) editPreview.src = prod.img || "";

    modalEdit.classList.remove("hidden");
}

function fecharModal() {
    modalEdit.classList.add("hidden");
}

modalEdit.addEventListener("click", e => {
    if (e.target === modalEdit) fecharModal();
});

editFoto.addEventListener("change", () => {
    const file = editFoto.files?.[0];
    if (!file) return;

    if (editPreview) {
        if (editPreview.dataset.objectUrl) {
            URL.revokeObjectURL(editPreview.dataset.objectUrl);
        }

        const url = URL.createObjectURL(file);
        editPreview.src = url;
        editPreview.dataset.objectUrl = url;
    }
});

function darBaixaEstoque() {
    const id = Number(editId.value);
    const prod = produtos.find(p => Number(p.id) === id);

    if (!prod) return;

    const atual = Number(editEstoque.value);
    const baixa = Number(editBaixa.value);

    if (!Number.isFinite(baixa) || baixa <= 0) {
        alert("Digite uma quantidade válida para dar baixa.");
        return;
    }

    if (baixa > atual) {
        alert("A baixa não pode ser maior que o estoque.");
        return;
    }

    // Atualiza o campo. A persistência ocorre ao salvar o produto.
    editEstoque.value = atual - baixa;
    editBaixa.value = "";
}

formEdit.addEventListener("submit", async e => {
    e.preventDefault();

    const id = Number(editId.value);
    const file = editFoto.files?.[0];

    const data = new FormData();
    data.append("id", id);
    data.append("nome", editNome.value.trim());
    data.append("categoria", editCategoria.value);
    data.append("preco", editPreco.value);
    data.append("estoque", editEstoque.value);
    data.append("tamanho", editTamanho.value.trim());
    data.append("cor", editCor.value.trim());
    data.append("peso_banho", editPesoBanho.value.trim());
    data.append("milesimos_banho", editMilesimosBanho.value.trim());

    if (file) data.append("foto", file);

    try {
        const res = await fetch("/api/produtos/update", {
            method: "POST",
            body: data
        });

        await respostaJSON(res);

        fecharModal();
        await render(true);

    } catch (err) {
        console.error(err);
        alert(err.message || "Não foi possível atualizar o produto.");
    }
});

// ==================== ADICIONAR PRODUTO ====================

function abrirModalAdicionar() {
    modalAdd.classList.remove("hidden");
}

function fecharModalAdicionar() {
    modalAdd.classList.add("hidden");
}

modalAdd.addEventListener("click", e => {
    if (e.target === modalAdd) fecharModalAdicionar();
});

formAdd.addEventListener("submit", async e => {
    e.preventDefault();

    const data = new FormData();
    data.append("categoria", addCategoria.value);
    data.append("nome", addNome.value.trim());
    data.append("preco", addPreco.value);
    data.append("estoque", addEstoque.value);
    data.append("tamanho", addTamanho.value.trim());
    data.append("cor", addCor.value.trim());
    data.append("peso_banho", addPesoBanho.value.trim());
    data.append("milesimos_banho", addMilesimosBanho.value.trim());

    if (imagemFinalFile) {
        data.append("foto", imagemFinalFile);
    } else if (addFoto.files?.[0]) {
        data.append("foto", addFoto.files[0]);
    }

    try {
        const res = await fetch("/api/produtos/add", {
            method: "POST",
            body: data
        });

        await respostaJSON(res);

        formAdd.reset();

        if (addPreview) {
            addPreview.src = "";
            addPreview.style.display = "none";
        }

        imagemFinalBlob = null;
        imagemFinalFile = null;

        fecharModalAdicionar();
        await render(true);

    } catch (err) {
        console.error(err);
        alert(err.message || "Não foi possível adicionar o produto.");
    }
});

// ==================== EXCLUIR PRODUTO ====================

async function formDel(id) {
    if (!confirm("Deseja realmente excluir este produto?")) return;

    try {
        const res = await fetch("/api/produtos/delete", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });

        await respostaJSON(res);

        await render(true);

    } catch (err) {
        console.error(err);
        alert(err.message || "Não foi possível excluir o produto.");
    }
}

// ==================== ETIQUETA ====================

function imprimirEtiqueta(id) {
    const prod = produtos.find(p => Number(p.id) === Number(id));
    if (!prod) return;

    const win = window.open("", "_blank", "width=400,height=400");

    if (!win) {
        alert("Permita pop-ups no navegador para imprimir a etiqueta.");
        return;
    }

    const nome = escapeHTML(prod.nome);
    const codigo = escapeHTML(prod.cdb || prod.id);
    const preco = formatarPreco(prod.preco);

    win.document.write(`
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Etiqueta</title>
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>
            <style>
                @page { size: 40mm 35mm; margin: 0; }
                body { font-family: Arial, sans-serif; margin: 0; }
                .etiqueta {
                    width: 40mm;
                    height: 35mm;
                    padding: 2mm;
                    box-sizing: border-box;
                    display: flex;
                    flex-direction: column;
                }
                .nome {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    line-height: 1.1;
                    overflow: hidden;
                    overflow-wrap: anywhere;
                }
                .preco {
                    height: 5mm;
                    flex-shrink: 0;
                    font-size: 10pt;
                    font-weight: bold;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .barcode-container {
                    height: 12mm;
                    flex-shrink: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                svg { width: 100%; max-height: 100%; }
            </style>
        </head>
        <body>
            <div class="etiqueta">
                <div class="nome">${nome}</div>
                <div class="preco">R$ ${preco}</div>
                <div class="barcode-container"><svg id="barcode"></svg></div>
            </div>

            <script>
                window.onload = function () {
                    if (typeof JsBarcode !== "function") {
                        alert("Não foi possível carregar o código de barras.");
                        return;
                    }

                    JsBarcode("#barcode", ${JSON.stringify(String(prod.cdb || prod.id))}, {
                        format: "CODE128",
                        width: 2,
                        height: 35,
                        displayValue: true,
                        fontSize: 12,
                        textMargin: 0,
                        margin: 0
                    });

                    const el = document.querySelector(".nome");
                    let fontSize = 18;

                    el.style.fontSize = fontSize + "pt";

                    while (el.scrollHeight > el.clientHeight && fontSize > 6) {
                        fontSize -= 0.5;
                        el.style.fontSize = fontSize + "pt";
                    }

                    window.print();
                };
            <\/script>
        </body>
        </html>
    `);

    win.document.close();
}

// ==================== CATEGORIAS ====================

function abrirModalCategorias() {
    atualizarCategorias();
    modalCategorias.classList.remove("hidden");
}

function fecharModalCategorias() {
    modalCategorias.classList.add("hidden");
}

async function carregarCategorias() {
    const res = await fetch("/api/categorias");
    const data = await respostaJSON(res);

    categorias = Array.isArray(data) ? data : [];

    cat.innerHTML = `
        <option value="">Todas</option>
        ${categorias.map(c =>
            `<option value="${Number(c.id)}">${escapeHTML(c.nome)}</option>`
        ).join("")}
    `;

    addCategoria.innerHTML = `
        <option value="">Selecione</option>
        ${categorias.map(c =>
            `<option value="${Number(c.id)}">${escapeHTML(c.nome)}</option>`
        ).join("")}
    `;

    editCategoria.innerHTML = `
        <option value="">Selecione</option>
        ${categorias.map(c =>
            `<option value="${Number(c.id)}">${escapeHTML(c.nome)}</option>`
        ).join("")}
    `;
}

async function atualizarCategorias() {
    try {
        await carregarCategorias();

        listaCategorias.innerHTML = categorias.map(c => `
            <div class="categoria-item">
                <input id="cat-${Number(c.id)}" value="${escapeHTML(c.nome)}">

                <div class="categoria-actions">
                    <button type="button" class="btn"
                        onclick="editarCategoria(${Number(c.id)})">Salvar</button>

                    <button type="button" class="btn btn-outline"
                        onclick="removerCategoria(${Number(c.id)})">Excluir</button>
                </div>
            </div>
        `).join("");

    } catch (err) {
        console.error(err);
        alert("Não foi possível carregar as categorias.");
    }
}

async function adicionarCategoria() {
    const nome = novaCategoria.value.trim();

    if (!nome) {
        alert("Informe o nome da categoria.");
        return;
    }

    const data = new FormData();
    data.append("nome", nome);

    try {
        await respostaJSON(await fetch("/api/categorias/add", {
            method: "POST",
            body: data
        }));

        novaCategoria.value = "";
        await atualizarCategorias();

    } catch (err) {
        console.error(err);
        alert(err.message || "Erro ao adicionar categoria.");
    }
}

async function editarCategoria(id) {
    const campo = document.getElementById(`cat-${id}`);
    const nome = campo?.value.trim();

    if (!nome) {
        alert("Informe o nome da categoria.");
        return;
    }

    const data = new FormData();
    data.append("id", id);
    data.append("nome", nome);

    try {
        await respostaJSON(await fetch("/api/categorias/update", {
            method: "POST",
            body: data
        }));

        await atualizarCategorias();
        await render(true);

    } catch (err) {
        console.error(err);
        alert(err.message || "Erro ao editar categoria.");
    }
}

async function removerCategoria(id) {
    if (!confirm("Excluir categoria?")) return;

    const data = new FormData();
    data.append("id", id);

    try {
        await respostaJSON(await fetch("/api/categorias/delete", {
            method: "POST",
            body: data
        }));

        await atualizarCategorias();
        await render(true);

    } catch (err) {
        console.error(err);
        alert(err.message || "Erro ao excluir categoria.");
    }
}

modalCategorias.addEventListener("click", e => {
    if (e.target === modalCategorias) fecharModalCategorias();
});

// ==================== REVENDEDORAS ====================

async function carregarRevendedoras() {
    try {
        const res = await fetch("/api/produtos/revendedoras");
        const data = await respostaJSON(res);

        revendedoras = Array.isArray(data) ? data : [];
        preencherSelectRevendedoras();

    } catch (err) {
        console.error("Erro ao carregar revendedoras:", err);
        revendedoras = [];
        preencherSelectRevendedoras();
        alert("Não foi possível carregar as revendedoras.");
    }
}

function preencherSelectRevendedoras() {
    revendedoraSelect.innerHTML = `
        <option value="">Selecione uma revendedora</option>
        ${revendedoras.map(r =>
            `<option value="${Number(r.id)}">${escapeHTML(r.nome)}</option>`
        ).join("")}
    `;
}

// ==================== MONTAGEM DA MALETA ====================

async function abrirModalEnvio() {
    modalEnvio.classList.remove("hidden");

    try {
        if (!produtosEnvio.length) {
            await carregarProdutosEnvio();
        }

        atualizarListaProdutosEnvio();
        atualizarResumoEnvio();

    } catch (err) {
        console.error(err);
        alert("Não foi possível carregar os produtos para a maleta.");
    }
}

function fecharModalEnvio() {
    modalEnvio.classList.add("hidden");
}

modalEnvio.addEventListener("click", e => {
    if (e.target === modalEnvio) fecharModalEnvio();
});

async function carregarProdutosEnvio() {
    const acumulados = [];
    let pageEnvio = 0;
    let acabouEnvio = false;

    while (!acabouEnvio) {
        const res = await fetch("/api/produtos", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                text: "",
                categoria: "",
                price: "all",
                sort: "relevancia",
                tamanho: "",
                cor: "",
                peso_banho: "",
                milesimos_banho: "",
                page: pageEnvio,
                limit: 100
            })
        });

        const data = await respostaJSON(res);
        const novos = Array.isArray(data.produtos) ? data.produtos : [];

        acumulados.push(...novos);

        if (novos.length < 100) {
            acabouEnvio = true;
        } else {
            pageEnvio++;
        }
    }

    produtosEnvio = acumulados;
}

// ==================== CUPOM POR PRODUTO ====================

async function carregarCuponsEnvio() {
    const revId = revendedoraSelect.value;
    cuponsRevendedora = [];
    if (!revId) return;
    const response = await fetch(`/api/cupons?revendedora_id=${encodeURIComponent(revId)}`);
    const data = await respostaJSON(response);
    if (revendedoraSelect.value === revId) {
        cuponsRevendedora = (data.cupons || []).filter(c => Number(c.quantidade) > 0);
    }
}

revendedoraSelect.addEventListener("change", async () => {
    cuponsPendentesEnvio = {};
    itensEnvio.forEach(item => { item.cupom = null; });
    fecharCupomProduto();
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
    try { await carregarCuponsEnvio(); }
    catch (err) { alert(err.message || "Não foi possível carregar os cupons."); }
});

async function abrirCupomProduto(id) {
    if (!revendedoraSelect.value) {
        alert("Escolha a revendedora antes de aplicar um cupom.");
        return;
    }
    try { await carregarCuponsEnvio(); }
    catch (err) { alert(err.message || "Não foi possível carregar os cupons."); return; }
    const item = itensEnvio.find(i => Number(i.id) === Number(id));
    const cupom = item?.cupom || cuponsPendentesEnvio[id];
    cupomProdutoId.value = id;
    cupomSelecionado.replaceChildren(new Option("Sem cupom", ""));
    cuponsRevendedora.forEach(c => cupomSelecionado.add(new Option(`${c.desconto}% (${c.quantidade} disponíveis)`, c.id)));
    cupomSelecionado.value = cupom?.id ?? "";
    atualizarPreviaCupom();
    modalCupomEnvio.classList.remove("hidden");
}

function atualizarPreviaCupom() {
    const product = produtosEnvio.find(p => Number(p.id) === Number(cupomProdutoId.value));
    const coupon = cuponsRevendedora.find(c => Number(c.id) === Number(cupomSelecionado.value));
    const original = Math.round(Number(product?.preco || 0) * 100);
    const final = Math.floor((original * (100 - Number(coupon?.desconto || 0)) + 50) / 100);
    cupomPrevia.textContent = `Preço unitário: R$ ${formatarPreco(original / 100)} → R$ ${formatarPreco(final / 100)}`;
}
cupomSelecionado.addEventListener("change", atualizarPreviaCupom);

function fecharCupomProduto() {
    modalCupomEnvio.classList.add("hidden");
}

formCupomEnvio.addEventListener("submit", e => {
    e.preventDefault();
    const id = Number(cupomProdutoId.value);
    const coupon = cuponsRevendedora.find(c => Number(c.id) === Number(cupomSelecionado.value));
    const item = itensEnvio.find(i => Number(i.id) === id);
    const quantity = item?.quantidade ?? Number(document.getElementById(`qtd-envio-${id}`)?.value || 1);
    const reserved = itensEnvio.filter(i => i.id !== id && Number(i.cupom?.id) === Number(coupon?.id))
        .reduce((sum, i) => sum + i.quantidade, 0);
    if (coupon && quantity + reserved > Number(coupon.quantidade)) {
        alert("Cupons insuficientes para a quantidade selecionada. Cada unidade utiliza um cupom.");
        return;
    }
    const selected = coupon ? {id: Number(coupon.id), desconto: Number(coupon.desconto)} : null;
    cuponsPendentesEnvio[id] = selected;
    if (item) item.cupom = selected;
    fecharCupomProduto();
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
});

function atualizarListaProdutosEnvio() {
    const termo = (buscaEnvio.value || "").trim().toLowerCase();

    const filtrados = produtosEnvio.filter(p =>
        (p.nome || "").toLowerCase().includes(termo)
    );

    if (!filtrados.length) {
        listaProdutosEnvio.innerHTML =
            '<p class="envio-vazio">Nenhum produto encontrado.</p>';
        return;
    }

    listaProdutosEnvio.innerHTML = filtrados.map(p => {
        const id = Number(p.id);
        const selecionado = itensEnvio.find(i => Number(i.id) === id);
        const qtdAtual = selecionado?.quantidade ?? 1;
        const estoqueDisponivel = Math.max(
            0,
            Number(p.estoque || 0) - Number(p.estoque_em_uso || 0)
        );
        const cupom = selecionado?.cupom || cuponsPendentesEnvio[id];

        return `
            <div class="produto-envio-item">
                <div class="produto-envio-left">
                    <img src="${escapeHTML(p.img)}" alt="${escapeHTML(p.nome)}">
                    <div>
                        <strong>${escapeHTML(p.nome)}</strong>
                        <span>Estoque: ${Number(p.estoque || 0)}</span>
                        <span>Estoque em uso: ${Number(p.estoque_em_uso || 0)}</span>
                        ${p.categoria ? `<span>Categoria: ${escapeHTML(p.categoria)}</span>` : ""}
                        <span>Ref: ${id}</span>
                        ${cupom ? `<span>Cupom: ${Number(cupom.desconto)}%</span>` : ""}
                    </div>
                </div>

                <div class="produto-envio-right">
                    <input
                        type="number"
                        min="1"
                        max="${estoqueDisponivel}"
                        value="${qtdAtual}"
                        id="qtd-envio-${id}"
                        ${estoqueDisponivel <= 0 ? "disabled" : ""}
                    >

                    <button type="button" class="btn-cupom-envio"
                        onclick="abrirCupomProduto(${id})">
                        ${cupom ? "Editar cupom" : "Cupom"}
                    </button>

                    <button type="button"
                        class="btn ${selecionado ? "btn-outline" : ""}"
                        onclick="toggleProdutoEnvio(${id})"
                        ${estoqueDisponivel <= 0 ? "disabled" : ""}>
                        ${selecionado ? "Remover" : "Adicionar"}
                    </button>
                </div>
            </div>
        `;
    }).join("");
}

function toggleProdutoEnvio(id) {
    const prod = produtosEnvio.find(p => Number(p.id) === Number(id));
    if (!prod) return;

    const idx = itensEnvio.findIndex(i => Number(i.id) === Number(id));

    if (idx >= 0) {
        itensEnvio.splice(idx, 1);
    } else {
        const inputQtd = document.getElementById(`qtd-envio-${id}`);
        const quantidade = Number(inputQtd?.value || 1);
        const estoqueDisponivel = Math.max(
            0,
            Number(prod.estoque || 0) - Number(prod.estoque_em_uso || 0)
        );

        if (!Number.isInteger(quantidade) || quantidade <= 0) {
            alert("Digite uma quantidade inteira válida.");
            return;
        }

        if (quantidade > estoqueDisponivel) {
            alert("A quantidade excede o estoque disponível.");
            return;
        }

        itensEnvio.push({
            id: Number(prod.id),
            nome: prod.nome,
            quantidade,
            estoque: Number(prod.estoque || 0),
            preco: Number(prod.preco || 0),
            cupom: cuponsPendentesEnvio[id] || null
        });
    }

    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
}

function atualizarResumoEnvio() {
    if (!itensEnvio.length) {
        itensSelecionadosEnvio.innerHTML =
            '<p class="envio-vazio">Nenhum produto selecionado.</p>';
        return;
    }

    itensSelecionadosEnvio.innerHTML = itensEnvio.map(item => `
        <div class="item-selecionado-envio">
            <div>
                <strong>${escapeHTML(item.nome)}</strong>
                <span>Quantidade: ${item.quantidade}</span>
                <span>Unitário: R$ ${formatarPreco(item.preco)} → R$ ${formatarPreco(Math.floor((Math.round(item.preco * 100) * (100 - Number(item.cupom?.desconto || 0)) + 50) / 100) / 100)}</span>
                ${item.cupom
                    ? `<span>Cupom: ${Number(item.cupom.desconto)}% (${item.quantidade} cupom(ns))</span>`
                    : ""}
            </div>
            <button type="button" class="btn btn-outline"
                onclick="removerItemEnvio(${item.id})">Remover</button>
        </div>
    `).join("");
}

function removerItemEnvio(id) {
    itensEnvio = itensEnvio.filter(i => Number(i.id) !== Number(id));
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
}

function limparSelecaoEnvio() {
    itensEnvio = [];
    cuponsPendentesEnvio = {};
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
}

// ==================== ENVIAR MALETA AO BACK-END ====================

formEnvio.addEventListener("submit", async e => {
    e.preventDefault();

    if (enviandoMaleta) return;
    const revendedoraId = revendedoraSelect.value;

    if (!revendedoraId) {
        alert("Selecione uma revendedora.");
        return;
    }

    if (!itensEnvio.length) {
        alert("Selecione pelo menos um produto.");
        return;
    }

    const payload = {
        revendedora_id: Number(revendedoraId),
        nome_maleta: nomearMaleta.value.trim(),
        salvar_a4: salvarA4Pasta.checked,
        produtos: itensEnvio.map(item => ({
            produto_id: item.id,
            quantidade: item.quantidade,
            cupom_id: item.cupom?.id ?? null
        }))
    };

    const signature = JSON.stringify(payload);
    if (!tentativaEnvio || tentativaEnvio.signature !== signature) {
        const bytes = crypto.getRandomValues(new Uint8Array(32));
        tentativaEnvio = {signature, key: Array.from(bytes, b => b.toString(16).padStart(2, "0")).join("")};
    }
    payload.request_key = tentativaEnvio.key;
    enviandoMaleta = true;
    const submit = formEnvio.querySelector('[type="submit"]');
    submit.disabled = true;
    try {
        const res = await fetch("/api/produtos/envios_revendedoras", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
            body: JSON.stringify(payload)
        });

        const data = await respostaJSON(res);

        alert(data.mensagem || "Maleta enviada com sucesso.");

        tentativaEnvio = null;
        cuponsRevendedora = [];
        formEnvio.reset();
        itensEnvio = [];
        cuponsPendentesEnvio = {};

        await carregarProdutosEnvio();
        atualizarListaProdutosEnvio();
        atualizarResumoEnvio();
        fecharModalEnvio();

        await render(true);

    } catch (err) {
        console.error("Erro ao enviar maleta:", err);

        // Mantém os itens selecionados para que não sejam perdidos.
        alert(err.message || "Não foi possível enviar a maleta.");
    } finally {
        enviandoMaleta = false;
        submit.disabled = false;
    }
});

buscaEnvio.addEventListener("input", atualizarListaProdutosEnvio);

// ==================== EVENTOS E INICIALIZAÇÃO ====================

document.addEventListener("keydown", e => {
    if (e.key !== "Escape") return;

    if (!modalEdit.classList.contains("hidden")) fecharModal();
    if (!modalAdd.classList.contains("hidden")) fecharModalAdicionar();
    if (!modalEnvio.classList.contains("hidden")) fecharModalEnvio();
    if (modalCupomEnvio && !modalCupomEnvio.classList.contains("hidden")) {
        fecharCupomProduto();
    }
});

window.formDel = formDel;
window.abrirModal = abrirModal;
window.fecharModal = fecharModal;
window.abrirModalAdicionar = abrirModalAdicionar;
window.fecharModalAdicionar = fecharModalAdicionar;
window.darBaixaEstoque = darBaixaEstoque;
window.abrirModalEnvio = abrirModalEnvio;
window.fecharModalEnvio = fecharModalEnvio;
window.toggleProdutoEnvio = toggleProdutoEnvio;
window.removerItemEnvio = removerItemEnvio;
window.limparSelecaoEnvio = limparSelecaoEnvio;
window.abrirCupomProduto = abrirCupomProduto;
window.fecharCupomProduto = fecharCupomProduto;
window.abrirModalCategorias = abrirModalCategorias;
window.fecharModalCategorias = fecharModalCategorias;
window.adicionarCategoria = adicionarCategoria;
window.editarCategoria = editarCategoria;
window.removerCategoria = removerCategoria;

q.addEventListener("input", () => render(true));
cat.addEventListener("change", () => render(true));
price.addEventListener("change", () => render(true));
sort.addEventListener("change", () => render(true));
tamanho.addEventListener("input", () => render(true));
cor.addEventListener("input", () => render(true));
pesoBanho.addEventListener("input", () => render(true));
milesimosBanho.addEventListener("input", () => render(true));

(async function inicializar() {
    try {
        await carregarCategorias();
        await carregarRevendedoras();
        await render(true);
    } catch (err) {
        console.error("Erro ao inicializar a página:", err);
    }
})();
