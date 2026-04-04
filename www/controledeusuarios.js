(() => {

  const tabs = document.querySelectorAll(".tab");
  const listPendentes = document.getElementById("list-pendentes");
  const listAprovadas = document.getElementById("list-aprovadas");
  const listRecusadas = document.getElementById("list-recusadas");

  const modalBg = document.getElementById("modalBg");
  const modalNivel = document.getElementById("modalNivel");
  const nivelSelect = document.getElementById("nivelSelect");
  const btnCancelar = document.getElementById("cancelarNivel");
  const btnSalvar = document.getElementById("salvarNivel");

  let cardAtual = null;

  function abrirAba(nome) {

    tabs.forEach(t => {
      const on = t.dataset.tab === nome;
      t.classList.toggle("active", on);
      t.setAttribute("aria-selected", on ? "true" : "false");
    });

    listPendentes.hidden = nome !== "pendentes";
    listAprovadas.hidden = nome !== "aprovadas";
    listRecusadas.hidden = nome !== "recusadas";

    if (nome === "pendentes") carregarUsuarios("pendente");
    if (nome === "aprovadas") carregarUsuarios("aprovado");
    if (nome === "recusadas") carregarUsuarios("rejeitado");
  }

  tabs.forEach(t => t.addEventListener("click", () => abrirAba(t.dataset.tab)));

  async function carregarUsuarios(status) {

    const lista =
      status === "pendente" ? listPendentes :
      status === "aprovado" ? listAprovadas :
      listRecusadas;

    try {

      const res = await fetch(`/api/controledeusuarios?status=${status}`);
      if (!res.ok) throw new Error("Erro na requisição");

      const data = await res.json();
      const usuarios = Array.isArray(data) ? data : (data.usuarios || []);

      lista.innerHTML = usuarios.map(u => {
        
        const nivel = String(u.nivel || "ametista").toLowerCase();

        return `
        <article class="user-card" data-id="${u.id}" data-status="${status}">

          <div class="user-top">
            <div class="user-left">

              <div class="avatar">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M20 21c0-4.4-3.6-8-8-8s-8 3.6-8 8" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>

              <div class="user-info">
                <h3 class="user-name">${u.nome}</h3>

                <div class="meta">
                  <span class="meta-item">${u.email}</span>
                  <span class="meta-item">${u.telefone}</span>
                </div>
              </div>

            </div>

            <span class="badge ${
              status === "pendente" ? "pending" :
              status === "aprovado" ? "approved" :
              "refused"
            }" data-badge>

              ${
                status === "pendente" ? "Pendente" :
                status === "aprovado" ? "Aprovada" :
                "Recusada"
              }

            </span>

          </div>

          <div class="user-bottom">
            <div class="level-row">
              <span class="label">Nível:</span>

              <span class="pill ${nivel}-pill" data-pill>
                <span class="dot ${nivel}-dot" data-dot></span>
                <span data-pill-text>${nomeNivel(nivel)}</span>
              </span>
            </div>

            <div class="user-actions-right">

              <button
                class="link link-danger"
                type="button"
                data-action="delete">
                Excluir
              </button>

              <button
                class="link"
                type="button"
                data-action="nivel"
                data-nivel="${u.nivel}">
                Alterar nível
              </button>

            </div>
          </div>

          <div class="actions">

            <button class="btn accept" type="button" data-action="accept">
              <span class="btn-ico">✓</span>
              Aceitar
            </button>

            <button class="btn reject" type="button" data-action="reject">
              <span class="btn-ico">✕</span>
              Recusar
            </button>

          </div>

        </article>
      `;
      }).join("");

    } catch (err) {
      console.error(err);
      lista.innerHTML = "<p>Erro ao carregar usuários</p>";
    }
  }

  function abrirModal(card) {
    cardAtual = card;

    const btnNivel = card.querySelector('[data-action="nivel"]');
    nivelSelect.value = btnNivel?.dataset.nivel || "ametista";

    modalBg.hidden = false;
    modalNivel.hidden = false;
  }

  function fecharModal() {
    modalBg.hidden = true;
    modalNivel.hidden = true;
    cardAtual = null;
  }

  modalBg.addEventListener("click", fecharModal);
  btnCancelar.addEventListener("click", fecharModal);

  function nomeNivel(n) {
    return ({
      ametista: "Ametista",
      safira: "Safira",
      topazio: "Topázio",
      esmeralda: "Esmeralda",
      rubi: "Rubi",
      "rubi-black": "Rubi Black"
    }[n] || "Ametista");
  }

  function aplicarNivel(card, nivel) {

    const pill = card.querySelector("[data-pill]");
    const dot = card.querySelector("[data-dot]");
    const text = card.querySelector("[data-pill-text]");
    const btnNivel = card.querySelector('[data-action="nivel"]');
    
    if (pill) pill.className = `pill ${nivel}-pill`;
    if (dot) dot.className = `dot ${nivel}-dot`;
    if (text) text.textContent = nomeNivel(nivel);
    if (btnNivel) btnNivel.dataset.nivel = nivel;
  }

  document.addEventListener("click", (e) => {

    const btn = e.target.closest("[data-action]");
    if (!btn) return;

    const action = btn.dataset.action;
    const card = btn.closest(".user-card");
    if (!card) return;

    if (action === "nivel") {
      abrirModal(card);
      return;
    }

    if (action === "accept") {
      aceitarUsuario(card);
      return;
    }

    if (action === "reject") {
      rejeitarUsuario(card);
      return;
    }

    if (action === "delete") {
      excluirUsuario(card);
      return;
    }

  });

  btnSalvar.addEventListener("click", async () => {
    if (!cardAtual) return;

    const id = cardAtual.dataset.id;
    const nivel = nivelSelect.value;

    try {
      const res = await fetch("/api/controledeusuarios/alterarnivel", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          id: id,
          nivel: nivel
        })
      });

      if (!res.ok) throw new Error("Erro ao atualizar nível");

      aplicarNivel(cardAtual, nivel);
      fecharModal();

    } catch (err) {
      console.error(err);
    }
  });

  function moverCard(card, destino) {

    const badge = card.querySelector("[data-badge]");

    if (destino === "aprovadas") {

      card.dataset.status = "aprovado";

      if (badge) {
        badge.textContent = "Aprovada";
        badge.className = "badge approved";
      }

      listAprovadas.appendChild(card);
      return;
    }

    if (destino === "recusadas") {

      card.dataset.status = "rejeitado";

      if (badge) {
        badge.textContent = "Recusada";
        badge.className = "badge refused";
      }

      listRecusadas.appendChild(card);
    }

  }

  async function aceitarUsuario(card) {

    const id = card.dataset.id;

    try {

      const res = await fetch("/api/controledeusuarios/aprovar", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ id })
      });

      if (!res.ok) throw new Error("Erro ao aprovar usuário");

      moverCard(card, "aprovadas");
      abrirAba("aprovadas");

    } catch (err) {
      console.error(err);
    }

  }

  async function rejeitarUsuario(card) {

    const id = card.dataset.id;

    try {

      const res = await fetch("/api/controledeusuarios/rejeitar", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ id })
      });

      if (!res.ok) throw new Error("Erro ao rejeitar usuário");

      moverCard(card, "recusadas");
      abrirAba("recusadas");

    } catch (err) {
      console.error(err);
    }

  }

  async function excluirUsuario(card) {

    const id = card.dataset.id;

    if (!confirm("Tem certeza que deseja excluir esta usuária?")) return;

    try {

      const res = await fetch("/api/controledeusuarios/excluir", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ id })
      });

      if (!res.ok) throw new Error("Erro ao excluir usuário");

      card.remove();

    } catch (err) {
      console.error(err);
      alert("Erro ao excluir usuário");
    }
  }

  abrirAba("pendentes");

})();