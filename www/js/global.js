document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar");

    if (!sidebar) return;

    const path = window.location.pathname;

    const links = [
        { href: "/paineldecontrole", text: "Painel de Controle" },
        { href: "/produtos", text: "Produtos" },
        { href: "/vendas", text: "Vendas" },
        { href: "/pastasrevendedoras", text: "Pasta de Revendedores" },
        { href: "/cupons", text: "Cupons" }
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
            { href: "/precificacao", text: "Precificação" }

        );
    }

    sidebar.innerHTML = `
        <h2>Sissi Semi Joias e Acessórios</h2>

        <button id="menuBotaoMobile">
            ☰
        </button>

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

                <div class="rev-group-badge" id="revGroupBadge">
                    Carregando...
                </div>

                <p class="rev-recado" id="revRecado">
                    Carregando informações...
                </p>
            </div>

            <input type="file" id="trocarFoto" accept="image/*" hidden>
        </div>
    `;

    inicializarPerfilUsuario();

    const btn = document.getElementById("menuBotaoMobile");

    if (btn) {

    btn.addEventListener("click", (e) => {
        e.stopPropagation();

        sidebar.classList.add("open");
        btn.classList.add("hidden");
    });

    document.addEventListener("click", (e) => {
        if (!sidebar.classList.contains("open")) return;

        const clicouNoSidebar = sidebar.contains(e.target);
        const clicouNoBotao = btn.contains(e.target);

        if (!clicouNoSidebar && !clicouNoBotao) {
            sidebar.classList.remove("open");
            btn.classList.remove("hidden");
        }
    });

    sidebar.querySelectorAll("a").forEach(a => {
        a.addEventListener("click", () => {
            sidebar.classList.remove("open");
            btn.classList.remove("hidden");
        });
    });
    }
});

function inicializarPerfilUsuario() {
    const inputFoto = document.getElementById("trocarFoto");
    const revAvatarPreview = document.getElementById("revAvatarPreview");
    const revAvatarIcon = document.getElementById("revAvatarIcon");
    const revNameEl = document.getElementById("revName");
    const badge = document.getElementById("revGroupBadge");
    const revRecadoEl = document.getElementById("revRecado");

    
    function gerarRecado(usuario) {
        const total = Number(usuario.total) || 0;
        const proximo = usuario.proximo_nivel;
        const meta = usuario.proxima_meta;
        if (proximo && meta !== null) {
            const faltam = Math.max(0, Number(meta) - total);
            const valor = faltam.toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (faltam <= 100) return `Tá voando! Faltam só R$ ${valor} para chegar em ${proximo}.`;
            if (faltam <= 300) return `Você está pertinho do grupo ${proximo}. Faltam R$ ${valor}.`;
            return `Seu próximo objetivo é o grupo ${proximo}.`;
        }
        return usuario.nivel === "rubi-black"
            ? "Você está no grupo Rubi Black, o nível máximo. 🔥"
            : "Seu nível atual foi mantido. Não há novas metas automáticas disponíveis.";
    }

    function preencherDadosUsuario(usuario) {
    const nome = usuario.nome || "Sem nome";
    const foto = usuario.foto || "";
    revNameEl.textContent = nome;

    if (foto) {
        revAvatarPreview.src = foto;
        revAvatarPreview.style.display = "block";
        revAvatarIcon.style.display = "none";
    } else {
        revAvatarPreview.style.display = "none";
        revAvatarIcon.style.display = "block";
    }

    const classes = {ametista: "ametista", safira: "safira", topazio: "topazio",
        esmeralda: "esmeralda", rubi: "rubi", "rubi-black": "rubiblack"};
    badge.textContent = usuario.nivel_nome || "Ametista";
    badge.className = `rev-group-badge ${classes[usuario.nivel] || "ametista"}`;
    revRecadoEl.textContent = gerarRecado(usuario);
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
        badge.textContent = "Nível indisponível";
        badge.className = "rev-group-badge";
        revRecadoEl.textContent = "Não foi possível carregar os dados da revendedora.";
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

    document.addEventListener("perfil:atualizar", carregarDadosUsuario);
    carregarDadosUsuario();
}