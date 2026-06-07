<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Nova Venda</title>
    <link rel="stylesheet" href="styles/novavenda.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
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

                    <div id="mensagem-vazia" class="empty" style="display:none;">
                        Nenhum produto encontrado
                    </div>
                </div>

                <button class="btn-continuar" type="button" onclick="irParaCliente()">
                    Continuar
                </button>
            </div>

            <div class="right">

                <div class="card">
                    <h2>Carrinho</h2>

                    <div id="carrinho-itens">
                        <div class="empty">Carrinho vazio</div>
                    </div>
                </div>

                <div class="card resumo">
                    <h2>Resumo</h2>

                    <div class="linha">
                        <span>Subtotal</span>
                        <span id="subtotal">R$ 0,00</span>
                    </div>

                    <div class="linha total">
                        <span>Total</span>
                        <span id="total">R$ 0,00</span>
                    </div>

                </div>

            </div>

        </div>
    </div>


    <div id="etapa-cliente" style="display:none;">
        <div class="card">

            <h2>Dados do Cliente</h2>

            <input type="text" id="nomeCliente" placeholder="Nome do cliente">

            <input
                type="text"
                id="cpfCliente"
                placeholder="CPF"
                maxlength="14"
                inputmode="numeric"
                pattern="[0-9.]*"
            >

            <button type="button" class="btn-continuar" onclick="irParaFinalizacao()">
                Continuar
            </button>

        </div>
    </div>


    <div id="etapa-finalizacao" style="display:none;">
        <div class="card">

            <h2>Finalização</h2>

            <p style="margin-bottom: 12px;">Escolha forma de pagamento</p>

            <select id="formaPagamento">
                <option value="">Selecione</option>
                <option value="Dinheiro">Dinheiro</option>
                <option value="Crédito">Crédito</option>
                <option value="Débito">Débito</option>
                <option value="Pix">Pix</option>
            </select>

            <button id="finalizarVenda" type="button">
                Finalizar Venda
            </button>

        </div>
    </div>

</div>
<script src="js/novavenda.js"></script>
</body>
</html>