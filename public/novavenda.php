<!DOCTYPE html> 
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Venda</title>
    <link rel="stylesheet" href="styles/novavenda.css">
</head>
<body>

<div class="container">

    <header class="header">
        <div>
            <h1>Nova Venda</h1>
            <p>Sissi Semi Joias e Acessórios</p>
        </div>
    </header>

    <button class="btn-voltar" onclick="history.back()">← Voltar</button>

    <div class="progress">
        <div class="step active">Produtos</div>
        <div class="step">Cliente</div>
        <div class="step">Finalização</div>
    </div>

    <div id="etapa-produtos">
        <div class="content">

            <div class="left">
                <div class="card">
                    <h2>Selecionar Produtos</h2>
                    <input type="text" id="buscar" placeholder="Buscar produtos">
                    <div id="lista-produtos"></div>
                    <div id="mensagem-vazia" class="empty" style="display:none;">Nenhum produto encontrado</div>
                </div>

                <button class="btn-continuar" onclick="irParaCliente()">Continuar</button>
            </div>

            <div class="right">
                <div class="card">
                    <h2>Carrinho</h2>
                    <div class="empty">Carrinho vazio</div>
                </div>

                <div class="card resumo">
                    <h2>Resumo</h2>
                    <div class="linha">
                        <span>Subtotal</span>
                        <span>R$ 0,00</span>
                    </div>
                    <div class="linha total">
                        <span>Total</span>
                        <span>R$ 0,00</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="etapa-cliente" style="display:none;">
        <div class="card">
            <h2>Dados do Cliente</h2>
            <input type="text" placeholder="Nome do cliente">
            <input type="text" placeholder="CPF">
            <button onclick="irParaFinalizacao()">Continuar</button>
        </div>
    </div>

    <div id="etapa-finalizacao" style="display:none;">
        <div class="card">
            <h2>Finalização</h2>
            <p>Escolha forma de pagamento</p>
            <select>
                <option>Dinheiro</option>
                <option>Cartão</option>
                <option>Pix</option>
            </select>
            <button>Finalizar Venda</button>
        </div>
    </div>

</div>

<script>
function atualizarBarra(indice) {
  const steps = document.querySelectorAll(".progress .step");
  steps.forEach((step, i) => {
    step.classList.toggle("active", i === indice);
  });
}

function irParaCliente() {
  document.getElementById("etapa-produtos").style.display = "none";
  document.getElementById("etapa-cliente").style.display = "block";
  document.getElementById("etapa-finalizacao").style.display = "none";
  atualizarBarra(1);
}

function irParaFinalizacao() {
  document.getElementById("etapa-produtos").style.display = "none";
  document.getElementById("etapa-cliente").style.display = "none";
  document.getElementById("etapa-finalizacao").style.display = "block";
  atualizarBarra(2);
}

atualizarBarra(0);
</script>

<script src="scripts/script.js" defer></script>

</body>
</html> 