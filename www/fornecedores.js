document.addEventListener("DOMContentLoaded", () => {
  const buscaInput = document.getElementById("buscaFornecedor");
  const listaFornecedores = document.getElementById("listaFornecedores");
  const totalFornecedores = document.getElementById("totalFornecedores");

  const btnNovoFornecedor = document.getElementById("btnNovoFornecedor");
  const modalFornecedor = document.getElementById("modalFornecedor");
  const fecharModalFornecedor = document.getElementById("fecharModalFornecedor");
  const cancelarModalFornecedor = document.getElementById("cancelarModalFornecedor");
  const formFornecedor = document.getElementById("formFornecedor");
  const btnSalvarFornecedor = document.getElementById("btnSalvarFornecedor");

  const nomeFornecedor = document.getElementById("nomeFornecedor");
  const cnpjFornecedor = document.getElementById("cnpjFornecedor");
  const emailFornecedor = document.getElementById("emailFornecedor");
  const telefoneFornecedor = document.getElementById("telefoneFornecedor");
  const enderecoFornecedor = document.getElementById("enderecoFornecedor");
  const obsFornecedor = document.getElementById("obsFornecedor");

  let cardEditando = null;

  function atualizarTotal() {
    const cardsVisiveis = document.querySelectorAll(".fornecedor-card:not(.oculto)");
    totalFornecedores.textContent = cardsVisiveis.length;
  }

  function atualizarBusca() {
    const cards = document.querySelectorAll(".fornecedor-card");
    const busca = buscaInput.value.toLowerCase().trim();

    cards.forEach(card => {
      const texto = card.innerText.toLowerCase();
      const encontrou = texto.includes(busca);

      if (encontrou) {
        card.classList.remove("oculto");
      } else {
        card.classList.add("oculto");
      }
    });

    atualizarTotal();
  }

  function abrirModal(modoEdicao = false) {
    modalFornecedor.classList.add("ativo");
    document.body.style.overflow = "hidden";

    const tituloModal = modalFornecedor.querySelector(".modal-header h2");
    if (tituloModal) {
      tituloModal.textContent = modoEdicao ? "Editar Fornecedor" : "Novo Fornecedor";
    }

    if (btnSalvarFornecedor) {
      btnSalvarFornecedor.textContent = modoEdicao ? "Salvar Alterações" : "Cadastrar";
    }
  }

  function fecharModal() {
    modalFornecedor.classList.remove("ativo");
    document.body.style.overflow = "";
    formFornecedor.reset();
    cardEditando = null;

    const tituloModal = modalFornecedor.querySelector(".modal-header h2");
    if (tituloModal) {
      tituloModal.textContent = "Novo Fornecedor";
    }

    if (btnSalvarFornecedor) {
      btnSalvarFornecedor.textContent = "Cadastrar";
    }
  }

  function adicionarEventosCard(card) {
    const btnExcluir = card.querySelector(".btn-acao.excluir");
    const btnEditar = card.querySelector(".btn-acao.editar");

    if (btnExcluir) {
      btnExcluir.addEventListener("click", () => {
        const nome = card.querySelector("h3").textContent;

        if (confirm(`Deseja excluir o fornecedor "${nome}"?`)) {
          card.remove();
          atualizarTotal();
        }
      });
    }

    if (btnEditar) {
      btnEditar.addEventListener("click", () => {
        cardEditando = card;

        const nome = card.querySelector("h3")?.textContent || "";
        const cnpj = card.querySelector("small")?.textContent || "";

        const infos = card.querySelectorAll(".card-info p");
        const email = infos[0]?.innerText.replace("✉", "").trim() || "";
        const telefone = infos[1]?.innerText.replace("☎", "").trim() || "";
        const endereco = infos[2]?.innerText.replace("📍", "").trim() || "";
        const observacao = infos[3]?.innerText.replace("📝", "").trim() || "";

        nomeFornecedor.value = nome;
        cnpjFornecedor.value = cnpj === "CNPJ não informado" ? "" : cnpj;
        emailFornecedor.value = email === "Email não informado" ? "" : email;
        telefoneFornecedor.value = telefone === "Telefone não informado" ? "" : telefone;
        enderecoFornecedor.value = endereco === "Endereço não informado" ? "" : endereco;
        obsFornecedor.value = observacao === "Observações não informadas" ? "" : observacao;

        abrirModal(true);
      });
    }
  }

  function criarCardFornecedor(dados) {
    const article = document.createElement("article");
    article.className = "fornecedor-card";

    article.innerHTML = `
      <div class="card-top">
        <div>
          <h3>${dados.nome}</h3>
          <small>${dados.cnpj || "CNPJ não informado"}</small>
        </div>
      </div>

      <div class="card-info">
        <p>✉ ${dados.email || "Email não informado"}</p>
        <p>☎ ${dados.telefone || "Telefone não informado"}</p>
        <p>📍 ${dados.endereco || "Endereço não informado"}</p>
        ${dados.observacoes ? `<p>📝 ${dados.observacoes}</p>` : ""}
      </div>

      <div class="card-footer">
        <div class="acoes">
          <button class="btn-acao editar">Editar</button>
          <button class="btn-acao excluir">Excluir</button>
        </div>
      </div>
    `;

    adicionarEventosCard(article);
    return article;
  }

  if (btnNovoFornecedor) {
    btnNovoFornecedor.addEventListener("click", () => {
      cardEditando = null;
      abrirModal(false);
    });
  }

  if (fecharModalFornecedor) {
    fecharModalFornecedor.addEventListener("click", fecharModal);
  }

  if (cancelarModalFornecedor) {
    cancelarModalFornecedor.addEventListener("click", fecharModal);
  }

  if (modalFornecedor) {
    modalFornecedor.addEventListener("click", (e) => {
      if (e.target === modalFornecedor) {
        fecharModal();
      }
    });
  }

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modalFornecedor.classList.contains("ativo")) {
      fecharModal();
    }
  });

  if (buscaInput) {
    buscaInput.addEventListener("input", atualizarBusca);
  }

  document.querySelectorAll(".fornecedor-card").forEach(adicionarEventosCard);

  if (formFornecedor) {
    formFornecedor.addEventListener("submit", (e) => {
      e.preventDefault();

      const dados = {
        nome: nomeFornecedor.value.trim(),
        cnpj: cnpjFornecedor.value.trim(),
        email: emailFornecedor.value.trim(),
        telefone: telefoneFornecedor.value.trim(),
        endereco: enderecoFornecedor.value.trim(),
        observacoes: obsFornecedor.value.trim()
      };

      if (!dados.nome) {
        alert("Preencha o nome do fornecedor.");
        nomeFornecedor.focus();
        return;
      }

      if (cardEditando) {
        cardEditando.querySelector("h3").textContent = dados.nome;
        cardEditando.querySelector("small").textContent = dados.cnpj || "CNPJ não informado";

        const info = cardEditando.querySelector(".card-info");
        info.innerHTML = `
          <p>✉ ${dados.email || "Email não informado"}</p>
          <p>☎ ${dados.telefone || "Telefone não informado"}</p>
          <p>📍 ${dados.endereco || "Endereço não informado"}</p>
          ${dados.observacoes ? `<p>📝 ${dados.observacoes}</p>` : ""}
        `;
      } else {
        const novoCard = criarCardFornecedor(dados);
        listaFornecedores.prepend(novoCard);
      }

      fecharModal();
      atualizarBusca();
    });
  }

  atualizarTotal();
});