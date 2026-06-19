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

// edit
const modalEdit = document.getElementById("modalEdit");
const formEdit = document.getElementById("formEdit");
const editId = document.getElementById("editId");
const editNome = document.getElementById("editNome");
const editPreco = document.getElementById("editPreco");
const editEstoque = document.getElementById("editEstoque");
const editTamanho = document.getElementById("editTamanho");
const editCor = document.getElementById("editCor");
const editPesoBanho = document.getElementById("editPesoBanho");
const editMilesimosBanho = document.getElementById("editMilesimosBanho");
const editBaixa = document.getElementById("editBaixa");
const editFoto = document.getElementById("editFoto");
const editPreview = document.getElementById("editPreview");

// add
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

// envio
const modalEnvio = document.getElementById("modalEnvio");
const formEnvio = document.getElementById("formEnvio");
const revendedoraSelect = document.getElementById("revendedoraSelect");
const buscaEnvio = document.getElementById("buscaEnvio");
const nomearMaleta = document.getElementById("nomearMaleta")
const listaProdutosEnvio = document.getElementById("listaProdutosEnvio");
const itensSelecionadosEnvio = document.getElementById("itensSelecionadosEnvio");

let produtos = [];
let page = 0;
let limit = 12;
let isLoading = false;
let acabou = false;

let revendedoras = [];
let itensEnvio = [];

const revendedorasMock = [
{ id: 1000000000, nome: "?" }
];

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

const term = q.value.trim().toLowerCase();
const catVal = cat.value;
const priceVal = price.value;
const sortVal = sort.value;

try {
    const tags = catVal
    .split(",")
    .map(t => t.trim())
    .filter(t => t.length > 0);
    
    const res = await fetch("/api/produtos", {
    method: "POST",
    headers: { "Content-Type": "application/json"},
    body: JSON.stringify({
        text: term,
        tags: tags,
        price: priceVal,
        sort: sortVal,
        tamanho: tamanho.value.trim(),
        cor: cor.value.trim(),
        peso_banho: pesoBanho.value.trim(),
        milesimos_banho: milesimosBanho.value.trim(),
        page: page,
        limit: limit
    })
    });

    if (!res.ok) throw new Error("Erro na requisição");

    const data = await res.json();
    const novos = Array.isArray(data.produtos) ? data.produtos : [];
    const total = data.total ?? 0;

    if (novos.length < limit) acabou = true;

    produtos = [...produtos, ...novos];

    count.textContent = `Mostrando ${total} produto(s)`;

    grid.innerHTML += novos.map(p => `
    <article class="product">
        <div class="thumb">
        <img src="${p.img}" alt="${p.nome}">
        ${Number(p.estoque) <= 3 ? `<span class="badge">Baixo estoque</span>` : ``}
        </div>

        <div class="info">
        <h3 title="${p.nome}">${p.nome}</h3>
        <div class="meta">
            <p>• Estoque: ${p.estoque}</p>
            ${p.cat ? `<p>• Categoria(s): ${p.cat}</p>` : ``}
            ${p.tamanho ? `<p>• Tamanho: ${p.tamanho}</p>` : ``}
            ${p.cor ? `<p>• Cor: ${p.cor}</p>` : ``}
            ${p.peso_banho ? `<p>• Peso banho: ${p.peso_banho}</p>` : ``}
            ${p.milesimos_banho ? `<p>• Milésimos: ${p.milesimos_banho}</p>` : ``}
        </div>
        <div class="price">R$ ${parseFloat(p.preco).toFixed(2).replace(".", ",")}</div>

        <div class="actions">
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
            <button class="btn btn-editar" type="button" onclick="abrirModal(${p.id})">Editar</button>
            <?php endif; ?>

            <button class="btn btn-outline" type="button" onclick="imprimirEtiqueta(${p.id})">
            Etiqueta
            </button>

            <button class="btn btn-outline" type="button" onclick="formDel(${p.id})">
            Excluir
            </button>
        </div>
        </div>
    </article>
    `).join("");

    page++;
    atualizarListaProdutosEnvio();

} catch (err) {
    console.error(err);
    if (reset) grid.innerHTML = "<p>Erro ao carregar produtos.</p>";
}

isLoading = false;
}

main.addEventListener("scroll", () => {
if (main.scrollTop + main.clientHeight >= main.scrollHeight - 200) {
    render();
}
});

function abrirModal(id) {
const prod = produtos.find(p => Number(p.id) === Number(id));
if (!prod) return;

editId.value = prod.id;
editNome.value = prod.nome || "";
editPreco.value = prod.preco || "";
editEstoque.value = prod.estoque || 0;
editTamanho.value = prod.tamanho || "";
editCor.value = prod.cor || "";
editPesoBanho.value = prod.peso_banho || "";
editMilesimosBanho.value = prod.milesimos_banho || "";

editFoto.value = "";
editBaixa.value = "";
editPreview.src = prod.img;

modalEdit.classList.remove("hidden");
}

function fecharModal() {
modalEdit.classList.add("hidden");
}

modalEdit.addEventListener("click", (e) => {
if (e.target === modalEdit) fecharModal();
});

editFoto.addEventListener("change", () => {
    const file = editFoto.files && editFoto.files[0];
    if (!file) return;
    editPreview.src = URL.createObjectURL(file);
});

function darBaixaEstoque() {
    const id = Number(editId.value);
    const idx = produtos.findIndex(p => Number(p.id) === id);
    if (idx === -1) return;

    const atual = Number(editEstoque.value);
    const baixa = Number(editBaixa.value);

    if (!baixa || baixa <= 0) {
        alert("Digite uma quantidade válida pra dar baixa.");
        return;
    }

    if (baixa > atual) {
        alert("Não dá: baixa maior que o estoque.");
        return;
    }

    const novo = atual - baixa;

    editEstoque.value = novo;
    produtos[idx].estoque = novo;

    editBaixa.value = "";
    render(true);
}

formEdit.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = Number(editId.value);
    const idx = produtos.findIndex(p => Number(p.id) === id);
    if (idx === -1) return;

    produtos[idx].nome = editNome.value.trim();
    produtos[idx].preco = Number(editPreco.value);
    produtos[idx].estoque = Number(editEstoque.value);
    produtos[idx].tamanho = editTamanho.value.trim();
    produtos[idx].cor = editCor.value.trim();
    produtos[idx].peso_banho = editPesoBanho.value.trim();
    produtos[idx].milesimos_banho = editMilesimosBanho.value.trim();

    const file = editFoto.files && editFoto.files[0];
    if (file) produtos[idx].img = URL.createObjectURL(file);

    const data = new FormData();
    data.append("id", id);
    data.append("nome", editNome.value.trim());
    data.append("preco", editPreco.value);
    data.append("estoque", editEstoque.value);
    data.append("tamanho", editTamanho.value.trim());
    data.append("cor", editCor.value.trim());
    data.append("peso_banho", editPesoBanho.value.trim());
    data.append("milesimos_banho", editMilesimosBanho.value.trim());

    if (editFoto.files[0]) data.append("foto", editFoto.files[0]);

    try {
        const res = await fetch("/api/produtos/update", {
        method: "POST",
        body: data
        });

        if (!res.ok) throw new Error("Erro ao atualizar produto");

        fecharModal();
        render(true);

    } catch (err) {
        console.error(err);
        alert("Não foi possível atualizar o produto.");
    }
});

function abrirModalAdicionar() {
modalAdd.classList.remove("hidden");
}

function fecharModalAdicionar() {
modalAdd.classList.add("hidden");
}

modalAdd.addEventListener("click", (e) => {
if (e.target === modalAdd) fecharModalAdicionar();
});

let imagemFinalFile = null;
let imagemFinalBlob = null;

formAdd.addEventListener("submit", async (e) => {
e.preventDefault();

const addCategoriaClean = addCategoria.value
    .split(",")
    .map(t => t.trim())
    .filter(t => t.length > 0);

const data = new FormData();

addCategoriaClean.forEach(cat => {
    data.append("categoria[]", cat);
});

data.append("nome", addNome.value.trim());
data.append("preco", addPreco.value);
data.append("estoque", addEstoque.value);
data.append("tamanho", addTamanho.value.trim());
data.append("cor", addCor.value.trim());
data.append("peso_banho", addPesoBanho.value.trim());
data.append("milesimos_banho", addMilesimosBanho.value.trim());

if (imagemFinalFile) {
    data.append("foto", imagemFinalFile);
} else if (addFoto.files[0]) {
    data.append("foto", addFoto.files[0]);
}

const res = await fetch("/api/produtos/add", {
    method: "POST",
    body: data
});

if (!res.ok) {
    console.error("Erro ao enviar pro back");
    return;
}

formAdd.reset();

if (addPreview) {
    addPreview.src = "";
    addPreview.style.display = "none";
}

imagemFinalBlob = null;
imagemFinalFile = null;

fecharModalAdicionar();
render(true);
});

async function formDel(id) {
if (!confirm("Deseja realmente excluir este produto?")) return;

try {
    const res = await fetch(`/api/produtos/delete`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
    });

    if (!res.ok) throw new Error("Erro ao excluir produto");

    produtos = produtos.filter(p => Number(p.id) !== Number(id));
    render(true);

} catch (err) {
    console.error(err);
    alert("Não foi possível excluir o produto.");
}
}

function imprimirEtiqueta(id){
const prod = produtos.find(p => Number(p.id) === Number(id));
if(!prod) return;

const preco = parseFloat(prod.preco).toFixed(2).replace(".", ",");

const html = `
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode/dist/JsBarcode.all.min.js"><\/script>
<style>
    @media print {
    @page {
        size: 40mm 35mm;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
    }
    }

    body {
    font-family: Arial;
    margin: 0;
    }

    .etiqueta {
    width: 40mm;
    height: 35mm;
    padding: 2mm;
    box-sizing: border-box;

    display: flex;
    flex-direction: column;
    }

    /* NOME ocupa o espaço restante */
    .nome {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;

    text-align: center;
    line-height: 1.1;
    word-break: break-word;
    overflow: hidden;
    }

    /* PREÇO fixo */
    .preco {
    height: 5mm;
    flex-shrink: 0;

    font-size: 10pt;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    /* BARCODE fixo */
    .barcode-container {
    height: 12mm;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;
    }

    svg {
    width: 100%;
    height: auto;
    max-height: 100%;
    }
</style>
</head>

<body>
    <div class="etiqueta">
    <div class="nome">${prod.nome}</div>
    <div class="preco">R$ ${preco}</div>

    <div class="barcode-container">
        <svg id="barcode"></svg>
    </div>
    </div>

    <script>
    function ajustarFonteNome() {
        const el = document.querySelector(".nome");

        let fontSize = 100;
        const minFont = 6;

        el.style.fontSize = fontSize + "pt";

        while (el.scrollHeight > el.clientHeight && fontSize > minFont) {
        fontSize -= 0.5;
        el.style.fontSize = fontSize + "pt";
        }
    }

    JsBarcode("#barcode", "${prod.cdb || prod.id}", {
        format: "CODE128",
        width: 2,
        height: 50,
        displayValue: true,
        fontSize: 18,
        textMargin: 0,
        margin: 0
    });

    ajustarFonteNome();

    window.print();
    <\/script>
</body>
</html>
`;

const win = window.open();
win.document.write(html);
}

async function carregarRevendedoras() {
try {
    const res = await fetch("/api/produtos/revendedoras");
    if (!res.ok) throw new Error("Erro ao buscar revendedoras");

    const data = await res.json();
    revendedoras = Array.isArray(data) ? data : [];

    if (!revendedoras.length) {
    revendedoras = revendedorasMock;
    }
} catch (err) {
    console.warn("Usando revendedoras mock no front.");
    revendedoras = revendedorasMock;
}

preencherSelectRevendedoras();
}

function preencherSelectRevendedoras() {
revendedoraSelect.innerHTML = `
    <option value="">Selecione uma revendedora</option>
    ${revendedoras.map(r => `<option value="${r.id}">${r.nome}</option>`).join("")}
`;
}

async function abrirModalEnvio() {
modalEnvio.classList.remove("hidden");

if (!produtosEnvio.length) {
    await carregarProdutosEnvio();
}

atualizarListaProdutosEnvio();
atualizarResumoEnvio();
}

function fecharModalEnvio() {
modalEnvio.classList.add("hidden");
}

modalEnvio.addEventListener("click", (e) => {
if (e.target === modalEnvio) fecharModalEnvio();
});

let produtosEnvio = [];

async function carregarProdutosEnvio() {
produtosEnvio = [];
let pageEnvio = 0;
let acabouEnvio = false;

while (!acabouEnvio) {
    const res = await fetch("/api/produtos", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        text: "",
        tags: [],
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

    if (!res.ok) break;

    const data = await res.json();
    const novos = Array.isArray(data.produtos) ? data.produtos : [];

    produtosEnvio.push(...novos);

    if (novos.length < 100) {
    acabouEnvio = true;
    } else {
    pageEnvio++;
    }
}
}

function atualizarListaProdutosEnvio() {
const termo = (buscaEnvio.value || "").trim().toLowerCase();

const filtrados = produtosEnvio.filter(p => {
    const nome = (p.nome || "").toLowerCase();
    return nome.includes(termo);
});

if (!filtrados.length) {
    listaProdutosEnvio.innerHTML = `<p class="envio-vazio">Nenhum produto encontrado.</p>`;
    return;
}

listaProdutosEnvio.innerHTML = filtrados.map(p => {
    const jaSelecionado = itensEnvio.find(item => Number(item.id) === Number(p.id));
    const qtdAtual = jaSelecionado ? jaSelecionado.quantidade : 1;

    return `
    <div class="produto-envio-item">
        <div class="produto-envio-left">
        <img src="${p.img}" alt="${p.nome}">
        <div>
            <strong>${p.nome}</strong>
            <span>Estoque disponível: ${p.estoque}</span>
        </div>
        </div>

        <div class="produto-envio-right">
        <input
            type="number"
            min="1"
            max="${p.estoque}"
            value="${qtdAtual}"
            id="qtd-envio-${p.id}"
            ${Number(p.estoque) <= 0 ? "disabled" : ""}
        />

        <button
            type="button"
            class="btn ${jaSelecionado ? "btn-outline" : ""}"
            onclick="toggleProdutoEnvio(${p.id})"
            ${Number(p.estoque) <= 0 ? "disabled" : ""}
        >
            ${jaSelecionado ? "Remover" : "Adicionar"}
        </button>
        </div>
    </div>
    `;
}).join("");
}

function toggleProdutoEnvio(id) {
const prod = produtosEnvio.find(p => Number(p.id) === Number(id));
if (!prod) return;

const idx = itensEnvio.findIndex(item => Number(item.id) === Number(id));

if (idx >= 0) {
    itensEnvio.splice(idx, 1);
} else {
    const inputQtd = document.getElementById(`qtd-envio-${id}`);
    const quantidade = Number(inputQtd?.value || 1);

    if (!quantidade || quantidade <= 0) {
    alert("Digite uma quantidade válida.");
    return;
    }

    if (quantidade > Number(prod.estoque)) {
    alert("A quantidade não pode ser maior que o estoque.");
    return;
    }

    itensEnvio.push({
    id: prod.id,
    nome: prod.nome,
    quantidade,
    estoque: prod.estoque
    });
}

atualizarListaProdutosEnvio();
atualizarResumoEnvio();
}

function atualizarResumoEnvio() {
if (!itensEnvio.length) {
    itensSelecionadosEnvio.innerHTML = `<p class="envio-vazio">Nenhum produto selecionado.</p>`;
    return;
}

itensSelecionadosEnvio.innerHTML = itensEnvio.map(item => `
    <div class="item-selecionado-envio">
    <div>
        <strong>${item.nome}</strong>
        <span>Quantidade: ${item.quantidade}</span>
    </div>
    <button type="button" class="btn btn-outline" onclick="removerItemEnvio(${item.id})">Remover</button>
    </div>
`).join("");
}

function removerItemEnvio(id) {
itensEnvio = itensEnvio.filter(item => Number(item.id) !== Number(id));
atualizarListaProdutosEnvio();
atualizarResumoEnvio();
}

function limparSelecaoEnvio() {
itensEnvio = [];
atualizarListaProdutosEnvio();
atualizarResumoEnvio();
}

formEnvio.addEventListener("submit", async (e) => {
e.preventDefault();

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
    revendedora_id: revendedoraId,
    nome_maleta: nomearMaleta.value,
    produtos: itensEnvio.map(item => ({
    produto_id: item.id,
    quantidade: item.quantidade
    }))
};

try {
    const res = await fetch("/api/produtos/envios_revendedoras", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
    });

    if (!res.ok) {
        alert("Mesmo produto já foi enviado antes, olhe em Produtos dos Revendedores");  
        throw new Error("Erro ao enviar.");
    };
    formEnvio.reset();
    itensEnvio = [];
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
    fecharModalEnvio();

} catch (err) {
    formEnvio.reset();
    itensEnvio = [];
    atualizarListaProdutosEnvio();
    atualizarResumoEnvio();
    fecharModalEnvio();
}
});

buscaEnvio.addEventListener("input", atualizarListaProdutosEnvio);

document.addEventListener("keydown", (e) => {
if (e.key !== "Escape") return;
if (!modalEdit.classList.contains("hidden")) fecharModal();
if (!modalAdd.classList.contains("hidden")) fecharModalAdicionar();
if (!modalEnvio.classList.contains("hidden")) fecharModalEnvio();
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

q.addEventListener("input", () => render(true));
cat.addEventListener("input", () => render(true));
price.addEventListener("change", () => render(true));
sort.addEventListener("change", () => render(true));
tamanho.addEventListener("input", () => render(true));
cor.addEventListener("input", () => render(true));
pesoBanho.addEventListener("input", () => render(true));
milesimosBanho.addEventListener("input", () => render(true));

carregarRevendedoras();
render(true);