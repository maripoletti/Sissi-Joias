document.addEventListener("DOMContentLoaded", () => {
    const API_URL = "/api/cupons";

    const botoesAbas = document.querySelectorAll(".cupons-tab");
    const abaAdmin = document.getElementById("tab-gerenciar");
    const abaRevendedora = document.getElementById("tab-revendedora");
    const selectAdmin = document.getElementById("revendedora-admin");
    const listaAdmin = document.getElementById("lista-cupons-admin");
    const resumoCliente = document.getElementById("resumo-cupons");
    const listaUsados = document.getElementById("lista-cupons-usados");
    const formNovoCupom = document.getElementById("form-novo-cupom");
    const campoDesconto = document.getElementById("desconto-cupom");
    const campoQuantidade = document.getElementById("quantidade-cupom");
    const feedbackAdmin = document.getElementById("feedback-admin");

    let cupons = [];
    let requisicaoEmAndamento = false;

    async function api(metodo = "GET", dados = null, query = "") {
        const opcoes = {
            method: metodo,
            credentials: "same-origin",
            headers: { Accept: "application/json", "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').content }
        };

        if (dados !== null) {
            opcoes.headers["Content-Type"] = "application/json";
            opcoes.body = JSON.stringify(dados);
        }

        const resposta = await fetch(`${API_URL}${query}`, opcoes);

        let resultado;

        try {
            resultado = await resposta.json();
        } catch {
            throw new Error(
                "A API PHP não retornou JSON válido. Verifique o servidor."
            );
        }

        if (!resposta.ok || resultado.success !== true) {
            throw new Error(
                resultado.message || `Erro HTTP ${resposta.status}`
            );
        }

        return resultado;
    }

    function mensagemErro(erro) {
        console.error("Cupons:", erro);
        feedbackAdmin.textContent =
            erro.message || "Não foi possível concluir a operação.";
    }

    function celula(texto, classe = "") {
        const td = document.createElement("td");
        td.textContent = texto;

        if (classe) td.className = classe;

        return td;
    }

    function mostrarAba(nome) {
        const admin = nome === "gerenciar";

        abaAdmin.hidden = !admin;
        abaRevendedora.hidden = admin;

        botoesAbas.forEach(botao => {
            const ativa = botao.dataset.tab === nome;

            botao.classList.toggle("active", ativa);
            botao.setAttribute("aria-selected", String(ativa));
        });

        if (admin) {
            carregarCuponsAdmin();
        } else {
            carregarCuponsRevendedora();
        }
    }

    botoesAbas.forEach(botao => {
        botao.setAttribute("role", "tab");

        botao.addEventListener("click", () => {
            mostrarAba(botao.dataset.tab);
        });
    });

    async function carregarCuponsAdmin() {
        if (requisicaoEmAndamento) return;

        requisicaoEmAndamento = true;
        selectAdmin.disabled = true;
        listaAdmin.replaceChildren();

        const linha = document.createElement("tr");
        const td = celula("Carregando cupons...");
        td.colSpan = 4;

        linha.appendChild(td);
        listaAdmin.appendChild(linha);

        try {
            const id = selectAdmin.value;

            const resultado = await api(
                "GET",
                null,
                `?revendedora_id=${encodeURIComponent(id)}`
            );

            cupons = Array.isArray(resultado.cupons)
                ? resultado.cupons
                : [];

            renderizarAdmin();
        } catch (erro) {
            listaAdmin.replaceChildren();

            const linhaErro = document.createElement("tr");
            const tdErro = celula(erro.message, "cupons-vazio");

            tdErro.colSpan = 4;
            linhaErro.appendChild(tdErro);
            listaAdmin.appendChild(linhaErro);

            mensagemErro(erro);
        } finally {
            requisicaoEmAndamento = false;
            selectAdmin.disabled = false;
        }
    }

    function renderizarAdmin() {
        listaAdmin.replaceChildren();

        if (cupons.length === 0) {
            const linha = document.createElement("tr");
            const td = celula(
                "Nenhum cupom cadastrado para esta revendedora.",
                "cupons-vazio"
            );

            td.colSpan = 4;
            linha.appendChild(td);
            listaAdmin.appendChild(linha);
            return;
        }

        cupons.forEach(cupom => {
            const linha = document.createElement("tr");

            linha.appendChild(
                celula(`Cupom de ${cupom.desconto}%`)
            );

            const descontoTd = document.createElement("td");
            const etiqueta = document.createElement("span");

            etiqueta.className = "cupons-desconto";
            etiqueta.textContent = `${cupom.desconto}%`;

            descontoTd.appendChild(etiqueta);
            linha.appendChild(descontoTd);

            const quantidadeTd = document.createElement("td");
            const controles = document.createElement("div");

            controles.className = "cupons-quantidade";

            const menos = document.createElement("button");
            menos.type = "button";
            menos.textContent = "−";
            menos.disabled = Number(cupom.quantidade) <= 0;
            menos.setAttribute("aria-label", "Diminuir quantidade");

            menos.addEventListener("click", () => {
                alterarQuantidade(
                    cupom.id,
                    -1
                );
            });

            const numero = document.createElement("span");
            numero.textContent = cupom.quantidade;

            const mais = document.createElement("button");
            mais.type = "button";
            mais.textContent = "+";
            mais.setAttribute("aria-label", "Aumentar quantidade");

            mais.addEventListener("click", () => {
                alterarQuantidade(
                    cupom.id,
                    1
                );
            });

            controles.append(menos, numero, mais);
            quantidadeTd.appendChild(controles);
            linha.appendChild(quantidadeTd);

            const acoesTd = document.createElement("td");
            const remover = document.createElement("button");

            remover.type = "button";
            remover.className = "cupons-btn-remove";
            remover.textContent = "Remover";

            remover.addEventListener("click", () => {
                removerCupom(cupom.id);
            });

            acoesTd.appendChild(remover);
            linha.appendChild(acoesTd);

            listaAdmin.appendChild(linha);
        });
    }

    formNovoCupom.addEventListener("submit", async evento => {
        evento.preventDefault();
        feedbackAdmin.textContent = "";

        const desconto = Number(campoDesconto.value);
        const quantidade = Number(campoQuantidade.value);

        if (
            !Number.isInteger(desconto) ||
            desconto < 1 ||
            desconto > 100
        ) {
            feedbackAdmin.textContent =
                "Informe um desconto inteiro entre 1% e 100%.";
            campoDesconto.focus();
            return;
        }

        if (
            !Number.isInteger(quantidade) ||
            quantidade < 1 ||
            quantidade > 10000
        ) {
            feedbackAdmin.textContent =
                "Informe uma quantidade entre 1 e 10.000.";
            campoQuantidade.focus();
            return;
        }

        try {
            await api("POST", {
                acao: "adicionar",
                revendedora_id: selectAdmin.value,
                desconto,
                quantidade
            });

            feedbackAdmin.textContent =
                "Cupom cadastrado com sucesso!";

            formNovoCupom.reset();
            campoDesconto.value = 10;
            campoQuantidade.value = 1;

            await carregarCuponsAdmin();
        } catch (erro) {
            mensagemErro(erro);
        }
    });

    async function alterarQuantidade(id, delta) {
        try {
            await api("PATCH", { id, delta });

            feedbackAdmin.textContent = "Quantidade atualizada.";

            await carregarCuponsAdmin();
        } catch (erro) {
            mensagemErro(erro);
        }
    }

    async function removerCupom(id) {
        if (!confirm("Deseja realmente remover este cupom?")) {
            return;
        }

        try {
            await api("DELETE", { id });

            feedbackAdmin.textContent =
                "Cupom removido com sucesso.";

            await carregarCuponsAdmin();
        } catch (erro) {
            mensagemErro(erro);
        }
    }

    async function carregarCuponsRevendedora() {
        resumoCliente.replaceChildren();
        listaUsados.replaceChildren();

        try {
            // O PHP identifica a revendedora pela sessão autenticada.
            const query = Number(window.userData.role) === 2 && selectAdmin.value
                ? `?revendedora_id=${encodeURIComponent(selectAdmin.value)}` : "";
            const resultado = await api("GET", null, query);

            renderizarVisaoRevendedora(
                resultado.cupons || [],
                resultado.usados || []
            );
        } catch (erro) {
            const aviso = document.createElement("p");

            aviso.className = "cupons-vazio";
            aviso.textContent =
                erro.message || "Não foi possível carregar seus cupons.";

            resumoCliente.appendChild(aviso);
        }
    }

    function renderizarVisaoRevendedora(lista, usados) {
        resumoCliente.replaceChildren();

        if (lista.length === 0) {
            const vazio = document.createElement("p");

            vazio.className = "cupons-vazio";
            vazio.textContent =
                "Você não possui cupons disponíveis.";

            resumoCliente.appendChild(vazio);
        }

        lista.forEach(cupom => {
            const cartao = document.createElement("div");
            cartao.className = "cupons-resumo-card";

            const desconto = document.createElement("strong");
            desconto.textContent = `${cupom.desconto}%`;

            const quantidade = document.createElement("span");
            quantidade.textContent =
                `${cupom.quantidade} disponível(is)`;

            cartao.append(desconto, quantidade);
            resumoCliente.appendChild(cartao);
        });

        listaUsados.replaceChildren();

        if (usados.length === 0) {
            const linha = document.createElement("tr");
            const td = celula(
                "Nenhum cupom usado ainda.",
                "cupons-vazio"
            );

            td.colSpan = 4;
            linha.appendChild(td);
            listaUsados.appendChild(linha);
            return;
        }

        usados.forEach(item => {
            const linha = document.createElement("tr");

            linha.append(
                celula(`${item.produto || "—"} (${item.quantidade} un.)`),
                celula(`${item.desconto}%`),
                celula(Number(item.original).toLocaleString("pt-BR", {style: "currency", currency: "BRL"})),
                celula(Number(item.final).toLocaleString("pt-BR", {style: "currency", currency: "BRL"}))
            );

            listaUsados.appendChild(linha);
        });
    }

    selectAdmin.addEventListener("change", () => {
        feedbackAdmin.textContent = "";
        carregarCuponsAdmin();
    });

    mostrarAba(Number(window.userData.role) === 2 ? "gerenciar" : "revendedora");
});