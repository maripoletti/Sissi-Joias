const gastoInsumo = document.querySelector("#item-insumo")
const gastoBanho = document.querySelector("#item-banho")
const entradaPecas = document.querySelector("#item-entrada")
const saidaPecas = document.querySelector("#item-saida")

const metalInput = document.querySelector("#metalBanho")
const resultadoMetalBanhoDiv = document.querySelector("#resultado-metalBanho")

const inputNomeGasto = document.querySelector("#input-nome-gasto");
const inputCustoGasto = document.querySelector("#input-custo-gasto");
const inputDataGasto = document.querySelector("#input-data-gasto");

const inputAddNomeGasto = document.querySelector("#input-add-nome-gasto");
const inputAddCustoGasto = document.querySelector("#input-add-custo-gasto");
const inputAddDataGasto = document.querySelector("#input-add-data-gasto");

const sincronizarGastos = document.querySelector("#sincronizarGastos");
//let dadosGerais;
let primeira = true;
let dadosFixos;
let gastosCarregados = [];
let tipoAtual = null;
let gastoAtual = null;


let agoraBruto = new Date();
let menos30diasBruto = new Date(agoraBruto);
menos30diasBruto.setDate(menos30diasBruto.getDate() - 30);
let agora = agoraBruto.toLocaleDateString('pt-BR');
let menos30dias = menos30diasBruto.toLocaleDateString('pt-BR');

const intervalos = {
    "item-insumo": {
        inicio: menos30diasBruto.toISOString().split("T")[0],
        fim: agoraBruto.toISOString().split("T")[0]
    },
    "item-banho": {
        inicio: menos30diasBruto.toISOString().split("T")[0],
        fim: agoraBruto.toISOString().split("T")[0]
    },
    "item-entrada": {
        inicio: menos30diasBruto.toISOString().split("T")[0],
        fim: agoraBruto.toISOString().split("T")[0]
    },
    "item-saida": {
        inicio: menos30diasBruto.toISOString().split("T")[0],
        fim: agoraBruto.toISOString().split("T")[0]
    },
    "gastos-dinamicos": {
        inicio: menos30diasBruto.toISOString().split("T")[0],
        fim: agoraBruto.toISOString().split("T")[0]
    }
};

let metalAtual = "Todos";

function fecharModal() {
    document.querySelectorAll(".modal").forEach(modal => {
        modal.style.display = "none";
    })
};

document.querySelectorAll(".modal").forEach(modal => {
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            fecharModal();
        };
    });
});

document.querySelectorAll(".modal-fechar").forEach(botaoFecharIntervalo => {
    botaoFecharIntervalo.addEventListener("click", (e) => {
        if (e.target === botaoFecharIntervalo) {
            fecharModal();
        };
    });
});

function abrirModalIntervalo(botaoIntervalo) {
    const card = botaoIntervalo.closest(".cg-item")

    tipoAtual = card.id

    document.querySelector("#modal-tituloIntervalo").textContent =
    card.querySelector(".cg-texto").textContent;

    document.querySelector("#dataInicial").value = intervalos[tipoAtual]["inicio"];
    document.querySelector("#dataFinal").value = intervalos[tipoAtual]["fim"];

    document.querySelector("#modal-intervalo").style.display = "flex";
};

document.querySelectorAll(".trocar-intervalo").forEach(botaoIntervalo => {
    botaoIntervalo.addEventListener("click", () => {
        abrirModalIntervalo(botaoIntervalo);
    });
});

function abrirModalMetal(botaoMetal) {
    const card = botaoMetal.closest(".cg-item");

    tipoAtual = card.id

    document.querySelector("#modal-banho").style.display = "flex";

    metalInput.focus();
};

document.querySelectorAll(".trocar-banhoMetal").forEach(botaoMetal => {
    botaoMetal.addEventListener("click", () => {
        abrirModalMetal(botaoMetal);
    })
})

function abrirModalIntervaloGerais() {
    document.querySelector("#modal-tituloIntervalo").textContent = "Custo total";
    tipoAtual = "gastos-dinamicos";

    document.querySelector("#modal-intervalo").style.display = "flex";

    document.querySelector("#dataInicial").value = intervalos["gastos-dinamicos"]["inicio"];
    document.querySelector("#dataFinal").value = intervalos["gastos-dinamicos"]["fim"];
}

function abrirModalAdicionarGasto() {
    document.querySelector("#modal-gastoAdicionar").style.display = "flex";
    inputAddDataGasto.value = agoraBruto.toISOString().split("T")[0];
}

metalInput.addEventListener("input", () => {
    pegarMetais(metalInput.value)
}) 

async function pegarMetais(texto) {
    try {
        const response = await fetch("/api/controledegastos/metais", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                texto
            })
        })

        const metais = await response.json();

        resultadoMetalBanhoDiv.innerHTML = metais.map((metais) => `
            <button class="resultado-metalBanho-item">${metais.nome}</button>
        `).join("");
        
        if (metalInput.value.trim() === "") {
            esconderProcuraMetais();
        } else {
            mostrarProcuraMetais(metais)
        }

        resultadoMetalBanhoDiv.querySelectorAll(".resultado-metalBanho-item").forEach(metal => {
            metal.addEventListener("click", () => {
                console.log(metal.textContent)
                metalInput.value = metal.textContent
            })
        })
    } catch (error) {
        console.log(error);
        alert("Não foi possível pegar os metais")
    }
}

document.addEventListener("click", event => {
  if (!resultadoMetalBanhoDiv || !metalInput) return;

  const clicouNaBusca =
    resultadoMetalBanhoDiv.contains(event.target) ||
    metalInput.contains(event.target);

  if (!clicouNaBusca) {
    esconderProcuraMetais();
  }
});

function mostrarProcuraMetais(metais) {
    resultadoMetalBanhoDiv.style.display = "block";

    if(!metais || metais.length == 0) {
        resultadoMetalBanhoDiv.innerHTML = `
        <div class="sugestao-vazia">
        Nenhum metal encontrado.
        </div>
        `;
    }
}

function esconderProcuraMetais() {
    resultadoMetalBanhoDiv.style.display = "none";
    resultadoMetalBanhoDiv.innerHTML = "";
}

function trocarIntervalo() {
    intervalos[tipoAtual]["inicio"] = document.querySelector("#dataInicial").value;
    intervalos[tipoAtual]["fim"] = document.querySelector("#dataFinal").value;

    carregarFixos();
    carregarDinamicos();
    fecharModal();
}

function atualizarIntervalos() {
    document.querySelectorAll(".data").forEach(data => {
        const id = data.closest(".cg-item")?.id ?? "gastos-dinamicos"

        data.textContent = intervalos[id]["inicio"] + " a " + intervalos[id]["fim"]
    })
};

function trocarMetal() {  
    if (document.querySelector("#metalBanho").value.trim() == "") {
        metalAtual = "Todos"
    } else {
        metalAtual = document.querySelector("#metalBanho").value
    };

    carregarFixos();
    fecharModal();
}

function atualizarMetal() {
    document.querySelector(".metal-valor").textContent = metalAtual
}

function atualizarDados(insumos, banho, saida, entrada) {
    gastoInsumo.querySelector(".cg-dado").textContent = formatarDinheiro(insumos);
    gastoBanho.querySelector(".cg-dado").textContent = formatarDinheiro(banho);
    saidaPecas.querySelector(".cg-dado").textContent = saida;
    entradaPecas.querySelector(".cg-dado").textContent = entrada;
}

function formatarDinheiro(valor) {
    const numero = Number(valor) || 0;

    return numero.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}

async function carregarFixos() {
    try {
        const { ["gastos-dinamicos"]: _, ...intervalosFixos } = intervalos;

        const response = await fetch("/api/controledegastos/fixos", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                intervalos: intervalosFixos,
                metalBanho: metalAtual
            })
        });

        dadosFixos = await response.json()

        carregarCustoTotal();
        atualizarMetal();
        atualizarIntervalos();
        atualizarDados(dadosFixos.insumos, dadosFixos.banho, dadosFixos.saidas, dadosFixos.entradas);
    } catch (error) {
        console.log(error);
        alert("Não foi possível carregar os dados fixos");
    }
}



async function carregarDinamicos() {
    try {
        const response = await fetch("/api/controledegastos/dinamicos", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                intervalo: intervalos["gastos-dinamicos"]
            })
        });

        const gastos = await response.json();
        gastosCarregados = gastos;

        carregarCustoTotal();

        document.querySelector("#lista-gastos").innerHTML = gastos.map((gastos) => `
            <div class="gasto">
                <p class="gasto-nome">${gastos.nome}</p>
                <p class="gasto-custo">${formatarDinheiro(gastos.custo)}</p>
                <p class="gasto-data">${gastos.data}</p>
                <div class="gasto-modificar">
                    <button type="button" class="gasto-editar" onclick="abrirEditarGasto(${gastos.id})">Editar gasto</button>
                    <button type="button" class="gasto-excluir" onclick="excluirGasto(${gastos.id})">Excluir gasto</button>
                </div>
            </div>
        `).join("");
    } catch (error) {
        console.log(error);
        alert("Não foi possível carregar os dados dinâmicos");
    }
}

document.querySelector("#botaoGastoEditar").addEventListener("click", () => {
    fecharModal()
    editarGasto(gastoAtual)
})

function abrirEditarGasto(id) {
    const gasto = gastosCarregados.find(g => g.id == id);

    if (!gasto) return;

    gastoAtual = id

    document.querySelector("#modal-gastoEditar").style.display = "flex";

    inputNomeGasto.value = gasto.nome;
    inputCustoGasto.value = gasto.custo;
    inputDataGasto.value = gasto.data;
}

async function editarGasto(id) {
    try {
        const response = await fetch("/api/controledegastos/dinamicos/upd", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                nome: inputNomeGasto.value,
                preco: inputCustoGasto.value,
                data: inputDataGasto.value,
                id: id
            })
        })

        const result = await response.json();

        if (!result.success) {
            alert(result.message || "Erro.");
            return;
        }

        carregarDinamicos();
    } catch (error) {
        console.log(error);
        alert("Não foi possível editar gasto");
    }
}



async function excluirGasto(id) {
    if (!confirm("Deseja realmente excluir este gasto?")) return;

    try {
        const response = await fetch("/api/controledegastos/dinamicos/del", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                id: id
            })
        })
        const result = await response.json();

        if (!result.success) {
            alert(result.message || "Erro.");
            return;
        }

        carregarDinamicos();
    } catch (error) {
        console.log(error);
        alert("Não foi possível excluir gasto");
    }
}

document.querySelector("#botaoGastoAdicionar").addEventListener("click", () => {
    fecharModal();
    adicionarGasto();

    inputAddNomeGasto.value = ""
    inputAddCustoGasto.value = ""
})

async function adicionarGasto() {
    try {
        const response = await fetch("/api/controledegastos/dinamicos/add", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                nome: inputAddNomeGasto.value,
                preco: inputAddCustoGasto.value,
                data: inputAddDataGasto.value
            })
        })

        const result = await response.json();

        if (!result.success) {
            alert(result.message || "Erro.");
            return;
        }

        carregarDinamicos();

    } catch (error) {
        console.log(error);
        alert("Não foi possível adicionar gasto");
    }
}

sincronizarGastos.addEventListener("change", () => {
    carregarCustoTotal()
});

function carregarCustoTotal() {
    const baseDinamica = gastosCarregados.reduce((total, gasto) => {
        return total + Number(gasto.custo || 0);
    }, 0);

    let total = baseDinamica;

    if (sincronizarGastos.value === "sim" && dadosFixos) {
        const insumos = Number(dadosFixos.insumos || 0);
        const banho = Number(dadosFixos.banho || 0);

        total += insumos + banho;
    }

    document.querySelector("#custoTotal").value = formatarDinheiro(total);
}

carregarFixos()
carregarDinamicos()