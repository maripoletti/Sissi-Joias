document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar");

    if (!sidebar) return;

    const path = window.location.pathname;

    const links = [
        { href: "/paineldecontrole", text: "Painel de Controle" },
        { href: "/produtos", text: "Produtos" },
        { href: "/vendas", text: "Vendas" }
    ];

    if (window.userData?.role === 2) {
        links.push(
            { href: "/impressoras", text: "Impressoras" },
            { href: "/relatorios", text: "Relatórios" },
            { href: "/controledegastos", text: "Controle de Gastos" },
            { href: "/controledeusuarios", text: "Controle de Revendedores" },
            { href: "/fornecedores", text: "Fornecedores" },
            { href: "/cadastrarimpressora", text: "Cadastrar Impressora" },
            { href: "/produtosrevendedores", text: "Produtos dos Revendedores" },
            { href: "/precificacao", text: "Precificação" },
            { href: "/toprevendedoras", text: "Top Revendedoras" }
        );
    }

    sidebar.innerHTML = `
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
            ${links.map(link => `
                <a href="${link.href}"
                   class="${path === link.href ? "active" : ""}">
                    ${link.text}
                </a>
            `).join("")}
        </nav>

        <div class="rev-profile">
            <label for="trocarFoto" class="revAvatar-wrap">
                <div class="revAvatar">
                    <img id="revAvatarPreview" src="" alt="Foto de perfil" style="display:none;">
                    <span id="revAvatarIcon">👤</span>
                </div>
            </label>

            <div class="rev-meta">
                <strong class="rev-name" id="revName">
                    ${window.revData?.nome ?? "Usuário"}
                </strong>

                <div class="rev-group-badge ametista" id="revGroupBadge">
                    Ametista
                </div>

                <p class="rev-recado" id="revRecado">
                    Carregando informações...
                </p>
            </div>

            <input type="file" id="trocarFoto" accept="image/*" hidden>
        </div>
    `;

    inicializarPerfilUsuario();
});

function inicializarPerfilUsuario() {
    const inputFoto = document.getElementById("trocarFoto");
    const revAvatarPreview = document.getElementById("revAvatarPreview");
    const revAvatarIcon = document.getElementById("revAvatarIcon");
    const revNameEl = document.getElementById("revName");
    const badge = document.getElementById("revGroupBadge");
    const revRecadoEl = document.getElementById("revRecado");

    
    function definirGrupoEMeta(total) {
        let grupoNome = "Ametista";
        let proximoGrupo = "Safira";
        let proximaMeta = 800;
        let classeGrupo = "ametista";

        if (total >= 800 && total < 1300) {
            grupoNome = "Safira";
            proximoGrupo = "Topázio";
            proximaMeta = 1800;
            classeGrupo = "safira";
        } else if (total >= 1300 && total < 1800) {
            grupoNome = "Topázio";
            proximoGrupo = "Esmeralda";
            proximaMeta = 1800;
            classeGrupo = "topazio";
        } else if (total >= 1800 && total < 2300) {
            grupoNome = "Esmeralda";
            proximoGrupo = "Rubi";
            proximaMeta = 2300;
            classeGrupo = "esmeralda";
        } else if (total >= 2300 && total < 5000) {
            grupoNome = "Rubi";
            proximoGrupo = "Rubi Black";
            proximaMeta = 5000;
            classeGrupo = "rubi";
        } else if (total >= 5000) {
            grupoNome = "Rubi Black";
            proximoGrupo = null;
            proximaMeta = null;
            classeGrupo = "rubiblack";
        }

        return { grupoNome, proximoGrupo, proximaMeta, classeGrupo };
    }

    function gerarRecado(total) {
    const { grupoNome, proximoGrupo, proximaMeta, classeGrupo } = definirGrupoEMeta(total);

    let recado = `Continue vendendo para subir de nível 💜`;

    if (proximaMeta !== null) {
        const faltam = proximaMeta - total;
        const faltamFormatado = faltam.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
        });

        if (faltam <= 100) {
        recado = `Tá voando! Faltam só <strong>R$ ${faltamFormatado}</strong> para chegar em <span>${proximoGrupo}</span>.`;
        } else if (faltam <= 300) {
        recado = `Você está pertinho do grupo <span>${proximoGrupo}</span>.<br>Faltam <strong>R$ ${faltamFormatado}</strong>.`;
        } else if (faltam <= 500) {
        recado = `Bom ritmo! Continue assim para alcançar <span>${proximoGrupo}</span>.`;
        } else {
        recado = `Seu próximo objetivo é o grupo <span>${proximoGrupo}</span>.`;
        }
    } else {
        recado = `Parabéns! Você está no grupo <span>Rubi Black</span> e bateu a meta máxima. 🔥`;
    }

    return { grupoNome, recado, classeGrupo };
    }

    function preencherDadosUsuario(usuario) {
    const nome = usuario.nome || "Sem nome";
    const foto = usuario.foto || "";
    const total = Number(usuario.total) || 0;

    revNameEl.textContent = nome;

    if (foto) {
        revAvatarPreview.src = foto;
        revAvatarPreview.style.display = "block";
        revAvatarIcon.style.display = "none";
    } else {
        revAvatarPreview.style.display = "none";
        revAvatarIcon.style.display = "block";
    }

    const dadosMeta = gerarRecado(total);

    badge.textContent = dadosMeta.grupoNome;
    badge.className = `rev-group-badge ${dadosMeta.classeGrupo}`;

    revRecadoEl.innerHTML = dadosMeta.recado;
    }

    async function carregarDadosUsuario() {
    try {
        const res = await fetch("/api/usuario/perfil", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }
        });

        if (!res.ok) {
        throw new Error("Erro ao buscar dados do usuário");
        }

        const data = await res.json();
        preencherDadosUsuario(data);
    } catch (err) {
        console.error(err);
        revNameEl.textContent = "Erro ao carregar";
        badge.textContent = "Ametista";
        badge.className = "rev-group-badge ametista";
        revRecadoEl.innerHTML = "Não foi possível carregar os dados da revendedora.";
    }
    }

    inputFoto?.addEventListener("change", async () => {
        const file = inputFoto.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("imagem", file);

        try {
            const response = await fetch("/api/usuario/upload", {
                method: "POST",
                body: formData
            });

            const text = await response.text();

            if (!text) {
                throw new Error("Resposta vazia do servidor");
            }

            const data = JSON.parse(text);

            await carregarDadosUsuario();
        } catch (err) {
            console.error(err);
        }
    });

    carregarDadosUsuario();
}