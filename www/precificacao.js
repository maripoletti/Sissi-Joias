const API_BUSCAR_PRODUTO = "/api/precificacao/buscarproduto";
const API_SALVAR_PRECIFICACAO = "/api/precificacao/salvarproduto";

const API_LISTAR_METAIS = "/api/metais/get";
const API_CRIAR_METAL = "/api/metais/add";
const API_DELETAR_METAL = "/api/metais/del";

// ================== ELEMENTOS ==================
let modoEdicao = false;
let produtoIdAtual = null;

const precoFinalInput = document.getElementById("precoFin");
const custoTotalInput = document.getElementById("custoTotal");
const valor1Input = document.getElementById("valor1");
const valor2Input = document.getElementById("valor2");
const lucro1Input = document.getElementById("lucro1");
const lucro2Input = document.getElementById("lucro2");
const nomeProdutoInput = document.getElementById("nomeProduto");
const resultadoBuscaProduto = document.getElementById("resultadoBuscaProduto");

const referenciaInput = document.getElementById("referencia");
const codigoExternoInput = document.getElementById("codigoExterno");
const unidadeEstoqueInput = document.getElementById("unidadeEstoque");
const pesoInput = document.getElementById("peso");
const categoriaVitrineInput = document.getElementById("categoriaVitrine");

const metalSelect = document.getElementById("metal");
const metalBanhoSelect = document.getElementById("metalBanho");
const novoMetalInput = document.getElementById("novoMetal");
const valorGramaMetalInput = document.getElementById("valorGramaMetal");
const valorGramaSelecionadoInput = document.getElementById("valorGramaSelecionado");
const custoMetalInput = document.getElementById("custoMetal");
const listaMetais = document.getElementById("listaMetais");

const percentualAtacadoInput = document.getElementById("percentualAtacado");
const percentualVarejoInput = document.getElementById("percentualVarejo");
const tipoTabela1Select = document.getElementById("tipoTabela1");
const tipoTabela2Select = document.getElementById("tipoTabela2");

const custoCompraBrutoInput = document.getElementById("custoCompraBruto");
const custoInsumoInput = document.getElementById("custoInsumo");
const banhoCustoInput = document.getElementById("banhoCusto");
const milesimosInput = document.getElementById("milesimos");
const milesimosBanhoInput = document.getElementById("milesimosBanho");

function setModoEdicao(valor, id = null) {
  modoEdicao = valor;
  produtoIdAtual = id;
  atualizarTextoBotao();
}

// ================== MOEDA ==================

function limparMoeda(valor) {
  if (!valor) return 0;

  return Number(
    String(valor).replace(/[R$\s.]/g, "").replace(",", ".")
  ) || 0;
}

function formatarMoedaBR(valor) {
  const numero = Number(valor) || 0;

  return numero.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL"
  });
}

function formatarInputMoeda(input) {
  let v = input.value.replace(/\D/g, "");

  if (!v) {
    input.value = "";
    return;
  }

  v = (v / 100).toFixed(2);
  v = v.replace(".", ",");
  v = v.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  input.value = "R$ " + v;
}

// ================== NÚMEROS ==================

function limparNumero(valor) {
  if (valor === null || valor === undefined || valor === "") return 0;
  if (typeof valor === "number") return valor;

  const texto = String(valor).trim();

  if (texto.includes(",")) {
    return Number(texto.replace(/\./g, "").replace(",", ".").replace(/[^\d.-]/g, "")) || 0;
  }

  return Number(texto.replace(/[^\d.-]/g, "")) || 0;
}

function limparPercentual(valor) {
  return limparNumero(valor);
}

function pegarValor(objeto, ...chaves) {
  for (const chave of chaves) {
    if (objeto && objeto[chave] !== undefined && objeto[chave] !== null) {
      return objeto[chave];
    }
  }

  return "";
}

function escapeHTML(texto) {
  return String(texto)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function setTextoInput(input, valor) {
  if (!input || valor === undefined || valor === null) return;
  input.value = valor;
}

function normalizarValorMoeda(valor) {
  if (valor === undefined || valor === null || valor === "") return 0;
  if (typeof valor === "number") return valor;

  const texto = String(valor).trim();

  if (texto.includes("R$") || texto.includes(",")) {
    return limparMoeda(texto);
  }

  return Number(texto.replace(/[^\d.-]/g, "")) || 0;
}

function setMoedaInput(input, valor) {
  if (!input || valor === undefined || valor === null || valor === "") return;
  input.value = formatarMoedaBR(normalizarValorMoeda(valor));
}

// ================== METAIS ==================

const METAIS_STORAGE_KEY = "precificacao_metais";

const METAIS_PADRAO = [
  { nome: "Ouro", valorGrama: 0 },
  { nome: "Prata", valorGrama: 0 },
  { nome: "Ródio", valorGrama: 0 },
  { nome: "Aço inox", valorGrama: 0 },
  { nome: "Níquel", valorGrama: 0 },
  { nome: "Tungstênio", valorGrama: 0 }
];

let metais = [];

async function carregarMetais() {

  try {

    const response = await fetch(API_LISTAR_METAIS);

    if (!response.ok) {
      throw new Error("Erro ao carregar metais.");
    }

    metais = await response.json();

    renderizarMetais();

  } catch (erro) {

    console.error(erro);

    metais = [...METAIS_PADRAO];

    renderizarMetais();
  }
}

function salvarMetaisLocalmente() {
  localStorage.setItem(METAIS_STORAGE_KEY, JSON.stringify(metais));
}

function getMetalSelecionado() {
  if (!metalSelect) return null;
  return metais.find(metal => metal.nome === metalSelect.value) || null;
}

function garantirMetal(nome, valorGrama = 0) {
  const nomeTratado = String(nome || "").trim();
  if (!nomeTratado) return;

  const metalExistente = metais.find(
    metal => metal.nome.toLowerCase() === nomeTratado.toLowerCase()
  );

  if (metalExistente) {
    if (valorGrama > 0) {
      metalExistente.valorGrama = valorGrama;
    }
  } else {
    metais.push({ nome: nomeTratado, valorGrama });
  }

  salvarMetaisLocalmente();
  renderizarMetais(nomeTratado);
}

function renderizarMetais(
  metalSelecionadoAtual = metalSelect ? metalSelect.value : "",
  metalBanhoSelecionadoAtual = metalBanhoSelect ? metalBanhoSelect.value : ""
) {

  if (!metalSelect || !metalBanhoSelect || !listaMetais) return;

  metalSelect.innerHTML = "";
  metalBanhoSelect.innerHTML = "";

  if (metais.length === 0) {
    metalSelect.innerHTML = '<option value="">Nenhum metal cadastrado</option>';
    metalBanhoSelect.innerHTML = '<option value="">Nenhum metal cadastrado</option>';

    listaMetais.innerHTML =
      '<div class="produto-sugestao-vazio">Nenhum metal cadastrado.</div>';

    recalcularPrecificacao();
    return;
  }

  metais.forEach(metal => {

    const option1 = document.createElement("option");
    option1.value = metal.nome;
    option1.textContent =
      `${metal.nome} - ${formatarMoedaBR(metal.valorGrama)}/g`;

    metalSelect.appendChild(option1);

    const option2 = document.createElement("option");
    option2.value = metal.nome;
    option2.textContent =
      `${metal.nome} - ${formatarMoedaBR(metal.valorGrama)}/g`;

    metalBanhoSelect.appendChild(option2);
  });

  const metalExiste = metais.some(
    metal => metal.nome === metalSelecionadoAtual
  );

  const metalBanhoExiste = metais.some(
    metal => metal.nome === metalBanhoSelecionadoAtual
  );

  metalSelect.value =
    metalExiste ? metalSelecionadoAtual : metais[0].nome;

  metalBanhoSelect.value =
    metalBanhoExiste ? metalBanhoSelecionadoAtual : metais[0].nome;

  listaMetais.innerHTML = metais.map((metal, index) => `
    <div class="metal-item">
      <div>
        <strong>${escapeHTML(metal.nome)}</strong>
        <span>${formatarMoedaBR(metal.valorGrama)} por grama</span>
      </div>

      <button
        type="button"
        class="btn-remover-metal"
        onclick="removerMetal(${index})"
      >
        Remover
      </button>
    </div>
  `).join("");

  recalcularPrecificacao();
}

async function adicionarMetal() {

  const nome = novoMetalInput.value.trim();

  const valorGrama =
    limparMoeda(valorGramaMetalInput.value);

  if (!nome) {
    alert("Digite o nome do metal.");
    return;
  }

  if (valorGrama <= 0) {
    alert("Digite o valor por grama.");
    return;
  }

  try {

    const response = await fetch(API_CRIAR_METAL, {

      method: "POST",

      headers: {
        "Content-Type": "application/json"
      },

      body: JSON.stringify({
        nome,
        valorGrama
      })
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message || "Erro.");
      return;
    }

    metais.push({
      id: result.id,
      nome,
      valorGrama
    });

    novoMetalInput.value = "";
    valorGramaMetalInput.value = "";

    renderizarMetais(nome);

  } catch (erro) {

    console.error(erro);

    alert("Erro ao adicionar metal.");
  }
}

async function removerMetal(index) {

  const metal = metais[index];

  if (!metal) return;

  const confirmar = confirm(
    `Remover "${metal.nome}"?`
  );

  if (!confirmar) return;

  try {

    const response = await fetch(
      API_DELETAR_METAL,
      {
        method: "POST",

        headers: {
          "Content-Type": "application/json"
        },

        body: JSON.stringify({
          id: metal.id
        })
      }
    );

    const result = await response.json();

    if (!result.success) {
      alert("Erro ao remover.");
      return;
    }

    metais.splice(index, 1);

    renderizarMetais();

  } catch (erro) {

    console.error(erro);

    alert("Erro ao remover metal.");
  }
}

function atualizarCustoMetal() {

  const metal = getMetalSelecionado();

  const metalBanho = metais.find(
    item => item.nome === metalBanhoSelect.value
  );

  const peso = limparNumero(pesoInput.value);

  const milesimos = limparNumero(milesimosInput.value);

  const milesimosBanho = limparNumero(milesimosBanhoInput.value);

  const valorGramaMetal =
    metal ? limparNumero(metal.valorGrama) : 0;

  const valorGramaBanho =
    metalBanho ? limparNumero(metalBanho.valorGrama) : 0;

  // metal principal
  const custoMetal =
    peso *
    valorGramaMetal *
    (milesimos / 1000);

  // banho
  const custoBanho =
    peso *
    valorGramaBanho *
    (milesimosBanho / 1000);

  if (valorGramaSelecionadoInput) {

    valorGramaSelecionadoInput.value = metal
      ? `${formatarMoedaBR(valorGramaMetal)}/g`
      : formatarMoedaBR(0);
  }

  custoMetalInput.value =
    formatarMoedaBR(custoMetal);

  banhoCustoInput.value =
    formatarMoedaBR(custoBanho);
}

function atualizarTextoBotao() {
  const btn = document.getElementById("btnSalvar");
  if (!btn) return;

  const sincronizar = document.getElementById("sincronizarUpVendas").value;

  if (sincronizar === "nao") {
    btn.textContent = "Criar";
  } else {
    btn.textContent = "Salvar";
  }
}

document.getElementById("sincronizarUpVendas").addEventListener("change", (e) => {
  if (e.target.value === "nao") {
    setModoEdicao(false, null);
  }

  atualizarTextoBotao();
});

function resetarFormulario() {
  setModoEdicao(false, null);
}

// ================== CÁLCULO ==================

function getCustosInputs() {
  return document.querySelectorAll(".custo");
}

function calcularTotalCustos() {
  let total = 0;

  getCustosInputs().forEach(input => {
    total += limparMoeda(input.value);
  });

  return total;
}

function getPercentualPorTipo(tipo) {
  if (tipo === "ATACADO") {
    return limparPercentual(percentualAtacadoInput.value);
  }

  return limparPercentual(percentualVarejoInput.value);
}

function calcularValorVenda(custo, percentualLucro) {
  return custo * (1 + percentualLucro / 100);
}

function recalcularPrecificacao() {
  atualizarCustoMetal();

  const custoTotal = calcularTotalCustos();

  const tipoTabela1 = tipoTabela1Select.value;
  const tipoTabela2 = tipoTabela2Select.value;

  const percentualTabela1 = getPercentualPorTipo(tipoTabela1);
  const percentualTabela2 = getPercentualPorTipo(tipoTabela2);

  custoTotalInput.value = formatarMoedaBR(custoTotal);

  lucro1Input.value = percentualTabela1.toFixed(2) + " %";
  lucro2Input.value = percentualTabela2.toFixed(2) + " %";

  valor1Input.value = formatarMoedaBR(calcularValorVenda(custoTotal, percentualTabela1));
  valor2Input.value = formatarMoedaBR(calcularValorVenda(custoTotal, percentualTabela2));
}

// ================== FETCH - BUSCAR PRODUTO PELO NOME ==================

let timeoutBusca;

if (nomeProdutoInput) {
  nomeProdutoInput.addEventListener("input", () => {
    clearTimeout(timeoutBusca);

    timeoutBusca = setTimeout(() => {
      buscarProduto(nomeProdutoInput.value);
    }, 20);
  });
}

function extrairListaProdutos(dados) {
  if (Array.isArray(dados)) return dados;
  if (Array.isArray(dados.produtos)) return dados.produtos;
  if (Array.isArray(dados.results)) return dados.results;
  if (Array.isArray(dados.data)) return dados.data;
  if (dados.produto) return [dados.produto];

  if (dados.id || dados.nome || dados.nome_produto) {
    return [dados];
  }

  return [];
}

async function buscarProduto(nome) {
  const nomeTratado = nome.trim();

  if (nomeTratado.length < 1) {
    esconderSugestoesProduto();
    return;
  }

  try {
    const response = await fetch(`${API_BUSCAR_PRODUTO}?nome=${encodeURIComponent(nomeTratado)}`, {
      method: "GET",
      headers: {
        "Accept": "application/json"
      }
    });

    if (!response.ok) {
      throw new Error("Erro ao buscar produto.");
    }

    const dados = await response.json();
    const produtos = extrairListaProdutos(dados);

    if (
      dados.produto ||
      (
        produtos.length === 1 &&
        !Array.isArray(dados) &&
        !dados.produtos &&
        !dados.results &&
        !dados.data
      )
    ) {
      preencherFormularioProduto(produtos[0]);
      esconderSugestoesProduto();
      return;
    }

    mostrarSugestoesProduto(produtos);
  } catch (erro) {
    console.error("Erro ao buscar produto:", erro);
    mostrarSugestoesProduto([], "Não foi possível buscar o produto agora.");
  }
}

function mostrarSugestoesProduto(produtos, mensagemVazia = "Nenhum produto encontrado.") {
  if (!resultadoBuscaProduto) return;

  if (!produtos || produtos.length === 0) {
    resultadoBuscaProduto.innerHTML = `
      <div class="produto-sugestao-vazio">
        ${escapeHTML(mensagemVazia)}
      </div>
    `;
    resultadoBuscaProduto.classList.add("ativo");
    return;
  }

  window.__produtosEncontrados = produtos;

  resultadoBuscaProduto.innerHTML = produtos.map((produto, index) => {
    const nome = pegarValor(produto, "nome", "nome_produto", "produto") || "Produto sem nome";

    return `
      <button type="button" class="produto-sugestao-item" onclick="selecionarProdutoEncontrado(${index})">
        ${escapeHTML(nome)}
      </button>
    `;
  }).join("");

  resultadoBuscaProduto.classList.add("ativo");
}

function esconderSugestoesProduto() {
  if (!resultadoBuscaProduto) return;
  resultadoBuscaProduto.classList.remove("ativo");
  resultadoBuscaProduto.innerHTML = "";
}

function selecionarProdutoEncontrado(index) {
  const produto = window.__produtosEncontrados && window.__produtosEncontrados[index];
  if (!produto) return;

  preencherFormularioProduto(produto);
  esconderSugestoesProduto();
}

function preencherFormularioProduto(produto) {
  setModoEdicao(true, pegarValor(produto, "ref", "id", "produto_id")); 
  atualizarTextoBotao();
  
  const custosProduto = produto.custos || produto;
  const tabelasProduto = produto.tabelas || produto;

  setTextoInput(nomeProdutoInput, pegarValor(produto, "nome", "nome_produto", "produto"));
  setTextoInput(referenciaInput, pegarValor(produto, "referencia", "referência", "ref"));
  setTextoInput(codigoExternoInput, pegarValor(produto, "codigo_externo", "codigoExterno", "código_externo"));
  setTextoInput(unidadeEstoqueInput, pegarValor(produto, "unidade_estoque", "estoque", "un_estoque"));
  setTextoInput(pesoInput, pegarValor(produto, "peso", "peso_gramas", "gramas"));
  setTextoInput(categoriaVitrineInput, pegarValor(produto, "categoria", "categoria_vitrine", "categoriaVitrine"));
  setTextoInput(milesimosInput, pegarValor(produto, "milesimos", "milésimos", "milesimo"));
  setTextoInput(milesimosBanhoInput, pegarValor(produto, "milesimosBanho", "milésimosBanho", "milesimoBanho"));

  setMoedaInput(precoFinalInput, pegarValor(produto, "preco", "preço", "precoReal"));

  setMoedaInput(custoCompraBrutoInput, pegarValor(custosProduto, "custo_compra_bruto", "custoBruto", "compra_bruto"));
  setMoedaInput(custoInsumoInput, pegarValor(custosProduto, "custo_insumo", "custoInsumo", "insumo"));
  setMoedaInput(banhoCustoInput, pegarValor(custosProduto, "banho_custo", "banhoCusto"));


  const valorGramaMetal = limparNumero(
    pegarValor(produto, "valor_grama_metal", "valorGramaMetal", "valorGrama")
  );
  const idMetal = pegarValor(produto, "metal", "metal_id", "idMetal");
  const idMetalBanho = pegarValor(produto, "metalBanho", "metal_banho", "idMetalBanho");

  if (idMetal) {
    const metalObj = metais.find(m => m.id === Number(idMetal));
    if (metalObj) {
      metalSelect.value = metalObj.nome; // seleciona pelo nome correspondente ao ID
    }
  }

  if (idMetalBanho) {
    const metalBanhoObj = metais.find(m => m.id === Number(idMetalBanho));
    if (metalBanhoObj) {
      metalBanhoSelect.value = metalBanhoObj.nome;
    }
  }

  const percentualAtacado = pegarValor(
    tabelasProduto,
    "percentual_atacado",
    "lucro_atacado",
    "percentualAtacado"
  );

  const percentualVarejo = pegarValor(
    tabelasProduto,
    "percentual_varejo",
    "lucro_varejo",
    "percentualVarejo"
  );

  if (percentualAtacado !== "") {
    percentualAtacadoInput.value = limparPercentual(percentualAtacado);
  }

  if (percentualVarejo !== "") {
    percentualVarejoInput.value = limparPercentual(percentualVarejo);
  }

  const tipoTabela1 = pegarValor(tabelasProduto, "tipo_tabela_1", "tipoTabela1", "tabela1");
  const tipoTabela2 = pegarValor(tabelasProduto, "tipo_tabela_2", "tipoTabela2", "tabela2");

  if (tipoTabela1) {
    tipoTabela1Select.value = String(tipoTabela1).toUpperCase();
  }

  if (tipoTabela2) {
    tipoTabela2Select.value = String(tipoTabela2).toUpperCase();
  }

  recalcularPrecificacao();
}

// ================== FETCH - SALVAR PRECIFICAÇÃO ==================

async function salvarPrecificacao() {
  const sincronizar = document.getElementById("sincronizarUpVendas").value;

  const payload = {
    produto: {
      id: produtoIdAtual,
      preco: limparMoeda(precoFinalInput.value),
      nome: nomeProdutoInput.value.trim(),
      unidadeEstoque: unidadeEstoqueInput.value.trim(),
      peso: limparNumero(pesoInput.value),
      milesimos: limparNumero(milesimosInput.value),
      milesimosBanho: limparNumero(milesimosBanhoInput.value),

      custoCompraBruto: limparMoeda(custoCompraBrutoInput.value),
      custoInsumo: limparMoeda(custoInsumoInput.value),

      metal: getMetalSelecionado()?.id || null,
      metalBanho: metais.find(m => m.nome === metalBanhoSelect.value)?.id || null,

      categoria: categoriaVitrineInput.value.trim()
    }
  };

  try {
    let url;

    if (sincronizar === "sim") {
      url = "/api/precificacao/update";
    } else {
      url = "/api/precificacao/add";
    }

    const response = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (!response.ok) throw new Error(result.message || "Erro");

    alert(result.message || "Sucesso");

    if (sincronizar === "nao") {
      modoEdicao = true;
      produtoIdAtual = result.id;
    }

    atualizarTextoBotao();

  } catch (err) {
    console.error(err);
    alert("Falha ao salvar");
  }
}

// ================== EVENTOS ==================

document.querySelectorAll(".money").forEach(input => {
  input.addEventListener("input", function () {
    formatarInputMoeda(this);
    recalcularPrecificacao();
  });
});

[
  custoCompraBrutoInput,
  custoInsumoInput,
  banhoCustoInput
].forEach(input => {
  if (input) {
    input.addEventListener("blur", recalcularPrecificacao);
  }
});

[
  percentualAtacadoInput,
  percentualVarejoInput,
  tipoTabela1Select,
  tipoTabela2Select,
  pesoInput,
  metalSelect,
  metalBanhoSelect,
  milesimosInput,
  milesimosBanhoInput
].forEach(input => {
  if (input) {
    input.addEventListener("input", recalcularPrecificacao);
    input.addEventListener("change", recalcularPrecificacao);
  }
});

document.addEventListener("click", event => {
  if (!resultadoBuscaProduto || !nomeProdutoInput) return;

  const clicouNaBusca =
    resultadoBuscaProduto.contains(event.target) ||
    nomeProdutoInput.contains(event.target);

  if (!clicouNaBusca) {
    esconderSugestoesProduto();
  }
});

// ================== INICIALIZAÇÃO ==================

carregarMetais();
recalcularPrecificacao();