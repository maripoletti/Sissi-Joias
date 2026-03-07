document.addEventListener("DOMContentLoaded", function () {
  const modalEditar = document.getElementById("modalEditar");
  const fecharEditar = document.getElementById("fecharEditar");
  const salvarEdicao = document.getElementById("salvarEdicao");

  const editNome = document.getElementById("editNome");
  const editCodigo = document.getElementById("editCodigo");
  const editQuantidade = document.getElementById("editQuantidade");
  const editPreco = document.getElementById("editPreco");

  let cardAtual = null;

  const botoesEditar = document.querySelectorAll(".btn-editar");

  botoesEditar.forEach(function (botao) {
    botao.addEventListener("click", function () {
      cardAtual = botao.closest(".peca-card");
      if (!cardAtual) return;

      const nomeEl = cardAtual.querySelector("h3");
      const codigoEl = cardAtual.querySelector(".peca-codigo");
      const quantidadeEl = cardAtual.querySelector(".peca-info span");
      const precoEl = cardAtual.querySelector(".peca-info strong");

      if (!nomeEl || !codigoEl || !quantidadeEl || !precoEl) return;

      const nome = nomeEl.textContent.trim();
      const codigo = codigoEl.textContent.trim();
      const quantidade = quantidadeEl.textContent.replace(/[^\d]/g, "");
      const preco = precoEl.textContent
        .replace("R$", "")
        .replace(/\./g, "")
        .replace(",", ".")
        .trim();

      editNome.value = nome;
      editCodigo.value = codigo;
      editQuantidade.value = quantidade;
      editPreco.value = preco;

      modalEditar.style.display = "flex";
    });
  });

  if (fecharEditar) {
    fecharEditar.addEventListener("click", function () {
      modalEditar.style.display = "none";
    });
  }

  window.addEventListener("click", function (e) {
    if (e.target === modalEditar) {
      modalEditar.style.display = "none";
    }
  });

  if (salvarEdicao) {
    salvarEdicao.addEventListener("click", function () {
      if (!cardAtual) return;

      const novoNome = editNome.value.trim();
      const novoCodigo = editCodigo.value.trim();
      const novaQuantidade = editQuantidade.value.trim();
      const novoPreco = parseFloat(editPreco.value);

      if (!novoNome || !novoCodigo || !novaQuantidade || isNaN(novoPreco)) {
        alert("Preencha todos os campos.");
        return;
      }

      cardAtual.querySelector("h3").textContent = novoNome;
      cardAtual.querySelector(".peca-codigo").textContent = novoCodigo;
      cardAtual.querySelector(".peca-info span").textContent = `📦 ${novaQuantidade} un.`;
      cardAtual.querySelector(".peca-info strong").textContent =
        `R$ ${novoPreco.toLocaleString("pt-BR", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        })}`;

      modalEditar.style.display = "none";
    });
  }
});

function abrirModalTeste() {
  const modal = document.getElementById("modalEditar");
  if (modal) {
    modal.style.display = "flex";
  }
}

document.addEventListener("DOMContentLoaded", function(){

  const botoesExcluir = document.querySelectorAll(".btn-excluir");

  botoesExcluir.forEach(function(botao){

    botao.addEventListener("click", function(){

      const confirmar = confirm("Deseja excluir esta peça?");

      if(confirmar){

        const card = botao.closest(".peca-card");

        card.remove();

      }

    });

  });

});