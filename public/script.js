const form = document.querySelector("#loginForm");

if (form) {
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = document.querySelector("#email")?.value.trim();
    const senha = document.querySelector("#senha")?.value.trim();

    if (!email || !senha) {
      alert("Preencha todos os campos!");
      return;
    }

    // Aqui depois liga no backend
  });
}

const dataElemento = document.getElementById("data-atual");

if (dataElemento) {
  const hoje = new Date();
  const opcoes = { weekday: "long", day: "2-digit", month: "long" };

  let dataFormatada = hoje.toLocaleDateString("pt-BR", opcoes);
  dataFormatada = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);

  dataElemento.textContent = dataFormatada;
}

const cadastroForm = document.getElementById("cadastroForm");
const inputTelefone = document.querySelector('input[name="telefone"]');

if (inputTelefone) {
  inputTelefone.addEventListener("input", () => {
    inputTelefone.value = inputTelefone.value.replace(/\D/g, "").slice(0, 11);
  });
}

if (cadastroForm) {
  cadastroForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const nome = document.querySelector('input[name="nome"]')?.value.trim();
    const emailCadastro = document.querySelector('input[name="email"]')?.value.trim();
    const senhaCadastro = document.querySelector('input[name="senha"]')?.value.trim();
    const confirmarSenha = document.querySelector('input[name="confirmar_senha"]')?.value.trim();
    const telefoneValor = inputTelefone ? inputTelefone.value.trim() : "";

    if (!nome || !emailCadastro || !senhaCadastro || !confirmarSenha || !telefoneValor) {
      alert("Preencha todos os campos!");
      return;
    }

    if (senhaCadastro.length < 6) {
      alert("Senha muito curta.");
      return;
    }

    if (senhaCadastro !== confirmarSenha) {
      alert("As senhas não coincidem.");
      return;
    }

    if (telefoneValor.length !== 11) {
      alert("Telefone inválido.");
      return;
    }

    window.location.href = "dashboard.php";
  });
}

// =====================
// BUSCA (API) 
// =====================
(function initBuscaProdutos() {
  const inputBuscar = document.getElementById("buscar");
  const lista = document.getElementById("lista-produtos");
  const mensagemVazia = document.getElementById("mensagem-vazia");
  const produtosUrl = document.querySelector("[data-produtos-url]")?.dataset?.produtosUrl;

  if (!inputBuscar || !lista || !mensagemVazia || !produtosUrl) return;

  function renderizarProdutos(produtos) {
    lista.innerHTML = "";

    if (!Array.isArray(produtos) || produtos.length === 0) {
      mensagemVazia.style.display = "block";
      return;
    }

    mensagemVazia.style.display = "none";

    produtos.forEach((p) => {
      const nome = p.nome ?? "Produto";
      const preco = Number(p.preco ?? 0);

      const item = document.createElement("div");
      item.className = "produto-item";
      item.textContent = `${nome} - R$ ${preco.toFixed(2)}`;
      lista.appendChild(item);
    });
  }

  async function buscarProdutos(termo) {
    try {
      const resp = await fetch(`${produtosUrl}?search=${encodeURIComponent(termo)}`);
      if (!resp.ok) throw new Error(`Falha ao buscar produtos (HTTP ${resp.status})`);

      const produtos = await resp.json();
      renderizarProdutos(produtos);
    } catch (e) {
      console.error(e);
      renderizarProdutos([]);
    }
  }

  let timer = null;
  inputBuscar.addEventListener("input", () => {
    clearTimeout(timer);
    const termo = inputBuscar.value.trim();
    timer = setTimeout(() => buscarProdutos(termo), 300);
  });

  buscarProdutos("");
})();