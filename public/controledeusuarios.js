(() => {
  // ===== Tabs =====
  const tabs = document.querySelectorAll(".tab");
  const listPendentes = document.getElementById("list-pendentes");
  const listAprovadas = document.getElementById("list-aprovadas");
  const listRecusadas = document.getElementById("list-recusadas");

  function abrirAba(nome) {
    tabs.forEach(t => {
      const on = t.dataset.tab === nome;
      t.classList.toggle("active", on);
      t.setAttribute("aria-selected", on ? "true" : "false");
    });

    listPendentes.hidden = nome !== "pendentes";
    listAprovadas.hidden = nome !== "aprovadas";
    listRecusadas.hidden = nome !== "recusadas";
  }

  tabs.forEach(t => t.addEventListener("click", () => abrirAba(t.dataset.tab)));

  // ===== Modal nível =====
  const modalBg = document.getElementById("modalBg");
  const modalNivel = document.getElementById("modalNivel");
  const nivelSelect = document.getElementById("nivelSelect");
  const btnCancelar = document.getElementById("cancelarNivel");
  const btnSalvar = document.getElementById("salvarNivel");

  let cardAtual = null;

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

  // ===== Aceitar/Recusar + Alterar nível (delegation) =====
  document.addEventListener("click", (e) => {
    const actionBtn = e.target.closest("[data-action]");
    if (!actionBtn) return;

    const action = actionBtn.dataset.action;
    const card = actionBtn.closest(".user-card");
    if (!card) return;

    if (action === "nivel") {
      abrirModal(card);
      return;
    }

    if (action === "accept") {
      moverCard(card, "aprovadas");
      abrirAba("aprovadas");
      return;
    }

    if (action === "reject") {
      moverCard(card, "recusadas");
      abrirAba("recusadas");
      return;
    }
  });

  btnSalvar.addEventListener("click", () => {
    if (!cardAtual) return;
    aplicarNivel(cardAtual, nivelSelect.value);
    fecharModal();
  });

  function moverCard(card, destino) {
    const badge = card.querySelector("[data-badge]");

    if (destino === "aprovadas") {
      card.dataset.status = "aprovada";
      if (badge) {
        badge.textContent = "Aprovada";
        badge.className = "badge approved";
      }
      listAprovadas.appendChild(card);
      return;
    }

    if (destino === "recusadas") {
      card.dataset.status = "recusada";
      if (badge) {
        badge.textContent = "Recusada";
        badge.className = "badge refused";
      }
      listRecusadas.appendChild(card);
      return;
    }
  }
})();