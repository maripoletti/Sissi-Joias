const custos = document.querySelectorAll(".custo");
const custoTotalInput = document.getElementById("custoTotal");
const valor1Input = document.getElementById("valor1");
const valor2Input = document.getElementById("valor2");
const lucro1Input = document.getElementById("lucro1");
const lucro2Input = document.getElementById("lucro2");

// Converte "R$ 1.234,56" → 1234.56
function limparMoeda(valor) {
  if (!valor) return 0;

  return Number(
    valor
      .replace(/[R$\s.]/g, "")
      .replace(",", ".")
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

  v = (v / 100).toFixed(2) + "";
  v = v.replace(".", ",");
  v = v.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  input.value = "R$ " + v;
}

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

atualizarPrecificacao();