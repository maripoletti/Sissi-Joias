const main = document.querySelector(".main");
const grid = document.getElementById("listaFornecedores");
const q = document.getElementById("buscaFornecedor");

const modalEdit = document.getElementById("modalFornecedor");

const editId = document.getElementById("editId");
const editNome = document.getElementById("nomeFornecedor");
const editCnpj = document.getElementById("cnpjFornecedor");
const editPhone = document.getElementById("telefoneFornecedor");
const editEmail = document.getElementById("emailFornecedor");
const editAddress = document.getElementById("enderecoFornecedor");

let fornecedores = [];
let page = 0;
let limit = 10;
let acabou = false;
let isLoading = false;

async function render(reset = false) {
    if (isLoading) return;

    if (reset) {
        page = 0;
        acabou = false;
        fornecedores = [];
        grid.innerHTML = "";
    }

    if (acabou) return;

    isLoading = true;

    const term = q.value.trim().toLowerCase();

    try {
        const res = await fetch("/api/fornecedores", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ text: term, page, limit })
        });

        if (!res.ok) throw new Error("Erro na API");

        const data = await res.json();
        const novos = data.fornecedores;

        if (novos.length < limit) acabou = true;

        fornecedores = [...fornecedores, ...novos];

        grid.innerHTML += novos.map(f => `
            <article class="fornecedor-card" data-id="${f.id}">
                <div class="card-top">
                    <div>
                        <h3>${f.nome}</h3>
                        <small>${formatCNPJ(f.cnpj)}</small>
                    </div>
                </div>
                <div class="card-info">
                    <p>✉ ${f.email}</p>
                    <p>☎ ${formatTelefone(f.telefone)}</p>
                    <p>📍 ${f.endereco}</p>
                </div>
                <div class="card-footer">
                    <div class="acoes">
                        <button class="btn-acao editar">Editar</button>
                        <button class="btn-acao excluir">Excluir</button>
                    </div>
                </div>
            </article>
        `).join("");

        
        document.querySelectorAll(".fornecedor-card").forEach(card => {
            const id = Number(card.getAttribute("data-id"));
            const btnEdit = card.querySelector(".btn-acao.editar");
            const btnDel = card.querySelector(".btn-acao.excluir");

            btnEdit.onclick = () => abrirModal(id);
            btnDel.onclick = () => excluirFornecedor(id);
        });

        page++;
    } catch (err) {
        console.error(err);
        if (reset) grid.innerHTML = "<p>Erro ao carregar fornecedores.</p>";
    }

    isLoading = false;
}

main.addEventListener("scroll", () => {
    if (main.scrollTop + main.clientHeight >= main.scrollHeight - 200) {
        render();
    }
});

q.addEventListener("input", () => render(true));

function abrirModal(id) {
    const f = fornecedores.find(p => p.id === id);
    if (!f) return;

    editId.value = f.id;
    editNome.value = f.nome || "";
    editCnpj.value = f.cnpj || "";
    editPhone.value = f.telefone || "";
    editEmail.value = f.email || "";
    editAddress.value = f.endereco || "";

    modalEdit.classList.add("ativo");
}

function fecharModal() {
    modalEdit.classList.remove("ativo");
}


modalEdit.addEventListener("click", e => {
    if (e.target === modalEdit) fecharModal();
});

const btnNovo = document.getElementById("btnNovoFornecedor");
btnNovo.addEventListener("click", () => {
    editId.value = "";       
    editNome.value = "";
    editCnpj.value = "";
    editPhone.value = "";
    editEmail.value = "";
    editAddress.value = "";

    
    modalEdit.classList.add("ativo");
});


async function salvarFornecedor() {
    const body = {
        nome: editNome.value,
        cnpj: editCnpj.value,
        telefone: editPhone.value,
        email: editEmail.value,
        endereco: editAddress.value
    };

    let url = "/api/fornecedores/add";

    
    if (editId.value) {
        body.id = editId.value;
        url = "/api/fornecedores/update";
    }

    try {
        await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body)
        });

        fecharModal();
        render(true);
    } catch (err) {
        console.error(err);
        alert("Erro ao salvar fornecedor");
    }
}

async function excluirFornecedor(id) {
    if (!confirm("Deseja excluir este fornecedor?")) return;

    try {
        await fetch("/api/fornecedores/delete", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });

        render(true);
    } catch (err) {
        console.error(err);
    }
}

function formatCNPJ(cnpj) {
    if (!cnpj) return "";
    cnpj = cnpj.replace(/\D/g, "");
    return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5");
}

editCnpj.addEventListener("input", () => {
    let v = editCnpj.value.replace(/\D/g, "");
    if (v.length > 14) v = v.slice(0, 14);
    v = v.replace(/^(\d{2})(\d)/, "$1.$2");
    v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
    v = v.replace(/\.(\d{3})(\d)/, ".$1/$2");
    v = v.replace(/(\d{4})(\d)/, "$1-$2");
    editCnpj.value = v;
});

editPhone.addEventListener("input", () => {
    let v = editPhone.value.replace(/\D/g, "");
    if (v.length > 11) v = v.slice(0, 11); 
    if (v.length > 10) { 
        v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
    } else if (v.length > 5) { 
        v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, "($1) $2-$3");
    } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
    }
    editPhone.value = v;
});

function formatTelefone(tel) {
    if (!tel) return "";
    let v = tel.replace(/\D/g, "");
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 10) {
        v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
    } else if (v.length > 5) {
        v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, "($1) $2-$3");
    } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
    }
    return v;
}

render(true);