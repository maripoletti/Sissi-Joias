const custos = document.querySelectorAll(".custo");
const custoTotalInput = document.getElementById("custoTotal");
const valor1Input = document.getElementById("valor1");
const valor2Input = document.getElementById("valor2");
const lucro1Input = document.getElementById("lucro1");
const lucro2Input = document.getElementById("lucro2");
const nomeProdutoInput = document.getElementById("nomeProduto");
const metalInput = document.getElementById("metal");


// ================== MOEDA ==================

function limparMoeda(valor) {
  if (!valor) return 0;

  return Number(
    valor.replace(/[R$\s.]/g, "").replace(",", ".")
  ) || 0;
}

function formatarMoedaBR(valor) {
  return valor.toLocaleString("pt-BR", {
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


// ================== GRAMAS ==================

function limparGramas(valor) {
  if (!valor) return 0;

  return Number(
    valor.replace(/[^\d,]/g, "").replace(",", ".")
  ) || 0;
}

function formatarGramas(valor) {
  return valor.toLocaleString("pt-BR", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }) + " g";
}

function formatarInputGramas(input) {
  let v = input.value.replace(/[^\d]/g, "");

  if (!v) {
    input.value = "";
    return;
  }

  v = (v / 100).toFixed(2);
  v = v.replace(".", ",");

  input.value = v + " g";
}

if (metalInput) {
  metalInput.addEventListener("input", function () {
    formatarInputGramas(this);
  });

  metalInput.addEventListener("blur", function () {
    const numero = limparGramas(this.value);

    if (numero > 0) {
      this.value = formatarGramas(numero);
    }
  });
}


// ================== CÁLCULO ==================

function calcularLucro(custo, valorVenda) {
  if (!custo || custo <= 0) return 0;
  return ((valorVenda - custo) / custo) * 100;
}

function atualizarPrecificacao() {
  let total = 0;

  custos.forEach(input => {
    total += limparMoeda(input.value);
  });

  custoTotalInput.value = formatarMoedaBR(total);

  const valor1 = limparMoeda(valor1Input.value);
  const valor2 = limparMoeda(valor2Input.value);

  const lucro1 = calcularLucro(total, valor1);
  const lucro2 = calcularLucro(total, valor2);

  lucro1Input.value = lucro1.toFixed(2) + " %";
  lucro2Input.value = lucro2.toFixed(2) + " %";
}

document.querySelectorAll(".money").forEach(input => {
  input.addEventListener("input", function () {
    formatarInputMoeda(this);
    atualizarPrecificacao();
  });
});


// ================== FETCH - BUSCAR PRODUTO ==================

let timeoutBusca;

if (nomeProdutoInput) {
  nomeProdutoInput.addEventListener("input", () => {
    clearTimeout(timeoutBusca);

    timeoutBusca = setTimeout(() => {
      buscarProduto(nomeProdutoInput.value);
    }, 500);
  });
}

async function buscarProduto(nome) {
  if (!nome) return;

  try {
    const response = await fetch("/api/buscarProduto", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ nome })
    });

    const produto = await response.json();

    if (!produto) return;

    // preenche campos
    metalInput.value = formatarGramas(produto.metal || 0);
    custoTotalInput.value = formatarMoedaBR(produto.custo_total || 0);
    valor1Input.value = formatarMoedaBR(produto.valor1 || 0);
    valor2Input.value = formatarMoedaBR(produto.valor2 || 0);

    atualizarPrecificacao();

  } catch (erro) {
    console.error("Erro ao buscar produto:", erro);
  }
}


// ================== FETCH - SALVAR ==================

async function salvarPrecificacao() {
  const nome = nomeProdutoInput.value;

  const custoTotal = limparMoeda(custoTotalInput.value);
  const valor1 = limparMoeda(valor1Input.value);
  const valor2 = limparMoeda(valor2Input.value);

  const lucro1 = parseFloat(lucro1Input.value);
  const lucro2 = parseFloat(lucro2Input.value);

  const metal = limparGramas(metalInput.value);

  try {
    const response = await fetch("/api/salvarPrecificacao", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        nome,
        metal,
        custoTotal,
        valor1,
        valor2,
        lucro1,
        lucro2
      })
    });

    const result = await response.json();

    if (result.success) {
      alert("Salvo com sucesso!");
    } else {
      alert("Erro ao salvar");
    }

  } catch (erro) {
    console.error(erro);
    alert("Erro na requisição");
  }
}

atualizarPrecificacao();