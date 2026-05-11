const API_BUSCAR_PRODUTO = "/api/buscarProduto";
const API_SALVAR_PRECIFICACAO = "/api/salvarPrecificacao";

// ================== ELEMENTOS ==================

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
const banhoOuroInput = document.getElementById("banhoOuro");
const banhoPrataInput = document.getElementById("banhoPrata");
const banhoRodioInput = document.getElementById("banhoRodio");
const milesimosInput = document.getElementById("milesimos");

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

let metais = carregarMetais();

function carregarMetais() {
  try {
    const salvos = JSON.parse(localStorage.getItem(METAIS_STORAGE_KEY));

    if (Array.isArray(salvos) && salvos.length > 0) {
      return salvos;
    }
  } catch (erro) {
    console.warn("Não foi possível carregar os metais do navegador.", erro);
  }

  return [...METAIS_PADRAO];
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

function renderizarMetais(metalSelecionadoAtual = metalSelect ? metalSelect.value : "") {
  if (!metalSelect || !listaMetais) return;

  metalSelect.innerHTML = "";

  if (metais.length === 0) {
    metalSelect.innerHTML = '<option value="">Nenhum metal cadastrado</option>';
    listaMetais.innerHTML = '<div class="produto-sugestao-vazio">Nenhum metal cadastrado.</div>';
    recalcularPrecificacao();
    return;
  }

  metais.forEach(metal => {
    const option = document.createElement("option");
    option.value = metal.nome;
    option.textContent = `${metal.nome} - ${formatarMoedaBR(metal.valorGrama)}/g`;
    metalSelect.appendChild(option);
  });

  const metalAindaExiste = metais.some(metal => metal.nome === metalSelecionadoAtual);
  metalSelect.value = metalAindaExiste ? metalSelecionadoAtual : metais[0].nome;

  listaMetais.innerHTML = metais.map((metal, index) => `
    <div class="metal-item">
      <div>
        <strong>${escapeHTML(metal.nome)}</strong>
        <span>${formatarMoedaBR(metal.valorGrama)} por grama</span>
      </div>

      <button type="button" class="btn-remover-metal" onclick="removerMetal(${index})">
        Remover
      </button>
    </div>
  `).join("");

  recalcularPrecificacao();
}

function adicionarMetal() {
  const nome = novoMetalInput.value.trim();
  const valorGrama = limparMoeda(valorGramaMetalInput.value);

  if (!nome) {
    alert("Digite o nome do metal.");
    return;
  }

  if (valorGrama <= 0) {
    alert("Digite o valor por grama do metal.");
    return;
  }

  const existe = metais.some(
    metal => metal.nome.toLowerCase() === nome.toLowerCase()
  );

  if (existe) {
    alert("Esse metal já existe. Remova ou altere o nome antes de cadastrar novamente.");
    return;
  }

  metais.push({ nome, valorGrama });
  salvarMetaisLocalmente();

  novoMetalInput.value = "";
  valorGramaMetalInput.value = "";

  renderizarMetais(nome);
}

function removerMetal(index) {
  if (!metais[index]) return;

  const confirmar = confirm(`Remover o metal "${metais[index].nome}"?`);
  if (!confirmar) return;

  metais.splice(index, 1);
  salvarMetaisLocalmente();
  renderizarMetais();
}

function atualizarCustoMetal() {
  const metal = getMetalSelecionado();
  const peso = limparNumero(pesoInput ? pesoInput.value : 0);
  const valorGrama = metal ? limparNumero(metal.valorGrama) : 0;
  const custoMetal = peso * valorGrama;

  if (valorGramaSelecionadoInput) {
    valorGramaSelecionadoInput.value = metal
      ? `${formatarMoedaBR(valorGrama)}/g`
      : formatarMoedaBR(0);
  }

  if (custoMetalInput) {
    custoMetalInput.value = formatarMoedaBR(custoMetal);
  }
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
    }, 500);
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

  if (nomeTratado.length < 2) {
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
    const referencia = pegarValor(produto, "referencia", "referência", "ref") || "";
    const complemento = referencia ? ` - Ref: ${referencia}` : "";

    return `
      <button type="button" class="produto-sugestao-item" onclick="selecionarProdutoEncontrado(${index})">
        ${escapeHTML(nome + complemento)}
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
  const custosProduto = produto.custos || produto;
  const tabelasProduto = produto.tabelas || produto;

  setTextoInput(nomeProdutoInput, pegarValor(produto, "nome", "nome_produto", "produto"));
  setTextoInput(referenciaInput, pegarValor(produto, "referencia", "referência", "ref"));
  setTextoInput(codigoExternoInput, pegarValor(produto, "codigo_externo", "codigoExterno", "código_externo"));
  setTextoInput(unidadeEstoqueInput, pegarValor(produto, "unidade_estoque", "unidadeEstoque", "un_estoque"));
  setTextoInput(pesoInput, pegarValor(produto, "peso", "peso_gramas", "gramas"));
  setTextoInput(categoriaVitrineInput, pegarValor(produto, "categoria", "categoria_vitrine", "categoriaVitrine"));
  setTextoInput(milesimosInput, pegarValor(produto, "milesimos", "milésimos", "milesimo"));

  setMoedaInput(custoCompraBrutoInput, pegarValor(custosProduto, "custo_compra_bruto", "custoCompraBruto", "compra_bruto"));
  setMoedaInput(custoInsumoInput, pegarValor(custosProduto, "custo_insumo", "custoInsumo", "insumo"));
  setMoedaInput(banhoOuroInput, pegarValor(custosProduto, "banho_ouro", "banhoOuro"));
  setMoedaInput(banhoPrataInput, pegarValor(custosProduto, "banho_prata", "banhoPrata"));
  setMoedaInput(banhoRodioInput, pegarValor(custosProduto, "banho_rodio", "banhoRódio", "banhoRodio"));

  const nomeMetal = pegarValor(produto, "metal", "nome_metal", "nomeMetal");
  const valorGramaMetal = limparNumero(
    pegarValor(produto, "valor_grama_metal", "valorGramaMetal", "valorGrama")
  );

  if (nomeMetal) {
    garantirMetal(nomeMetal, valorGramaMetal);
    metalSelect.value = nomeMetal;
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
  const metalSelecionado = getMetalSelecionado();

  const custoTotal = limparMoeda(custoTotalInput.value);
  const percentualTabela1 = limparPercentual(lucro1Input.value);
  const percentualTabela2 = limparPercentual(lucro2Input.value);

  const payload = {
    status: {
      acabado: document.getElementById("acabado")?.checked || false,
      ativo: document.getElementById("ativo")?.checked || false,
      compartilhar: document.getElementById("compartilhar")?.checked || false,
      sincronizarUpVendas: document.getElementById("sincronizarUpVendas")?.value || "nao"
    },

    produto: {
      nome: nomeProdutoInput.value.trim(),
      referencia: referenciaInput.value.trim(),
      codigoExterno: codigoExternoInput.value.trim(),
      unidadeEstoque: unidadeEstoqueInput.value.trim(),
      pesoGramas: limparNumero(pesoInput.value),
      categoriaVitrine: categoriaVitrineInput.value.trim(),
      milesimos: milesimosInput.value.trim()
    },

    metaisCadastrados: metais,

    metalSelecionado,

    custos: {
      custoCompraBruto: limparMoeda(custoCompraBrutoInput.value),
      custoInsumo: limparMoeda(custoInsumoInput.value),
      banhoOuro: limparMoeda(banhoOuroInput.value),
      banhoPrata: limparMoeda(banhoPrataInput.value),
      banhoRodio: limparMoeda(banhoRodioInput.value),
      custoMetal: limparMoeda(custoMetalInput.value),
      custoTotal
    },

    lucroPadrao: {
      atacado: limparPercentual(percentualAtacadoInput.value),
      varejo: limparPercentual(percentualVarejoInput.value)
    },

    tabelas: {
      tabela1: {
        tipo: tipoTabela1Select.value,
        percentualLucro: percentualTabela1,
        valorVenda: limparMoeda(valor1Input.value)
      },

      tabela2: {
        tipo: tipoTabela2Select.value,
        percentualLucro: percentualTabela2,
        valorVenda: limparMoeda(valor2Input.value)
      }
    }
  };

  if (!payload.produto.nome) {
    alert("Digite o nome do produto antes de salvar.");
    return;
  }

  try {
    const response = await fetch(API_SALVAR_PRECIFICACAO, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify(payload)
    });

    if (!response.ok) {
      throw new Error("Erro ao salvar precificação.");
    }

    const result = await response.json();

    if (result.success || result.sucesso) {
      alert("Salvo com sucesso!");
    } else {
      alert(result.message || result.mensagem || "O backend respondeu, mas não confirmou o salvamento.");
    }
  } catch (erro) {
    console.error(erro);
    alert("Erro na requisição de salvar. Confirme o endpoint com o back.");
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
  banhoOuroInput,
  banhoPrataInput,
  banhoRodioInput
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
  metalSelect
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

renderizarMetais();
recalcularPrecificacao();