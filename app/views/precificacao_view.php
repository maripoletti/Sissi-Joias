<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/estilo2.css">
  <link rel="stylesheet" href="styles/precificacao.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="container">
    <div class="card paineldecontrole">

      <aside class="sidebar"></aside>

      <main class="main">
        <section class="precificacao-page">
          <div class="precificacao-header">
            <h1>Precificação</h1>
            <p>Gerencie os custos, preços e margens do produto.</p>
          </div>

          <div class="precificacao-card">

            <div class="status-row">
              <div></div>

              <div class="field">
                <label>Sincronizar com <em>Produtos</em>?</label>
                <select id="sincronizarUpVendas">
                  <option value="sim">Sim</option>
                  <option value="nao">Não</option>
                </select>
              </div>
            </div>

            <div class="row row-3">
              <div class="field field-produto-busca">
                <label>Nome do produto</label>
                <input type="text" id="nomeProduto" placeholder="Digite o nome do produto" autocomplete="off">
                <div id="resultadoBuscaProduto" class="produto-sugestoes"></div>
              </div>

              <div class="field">
                <label>Custo Total</label>
                <input id="custoTotal" type="text" readonly>
              </div>

              <div class="field">
                <label>Metal selecionado</label>
                <select id="metal" name="metal"></select>
              </div>
            </div>

            <div class="row row-6">
              <div class="field">
                <label>Referência</label>
                <input type="text" id="referencia" readonly>
              </div>

              <div class="field">
                <label>Código Externo</label>
                <input type="text" id="codigoExterno" readonly>
              </div>

              <div class="field">
                <label>Un. estoque</label>
                <input type="text" id="unidadeEstoque" placeholder="Digite a unidade de estoque do produto">
              </div>

              <div class="field">
                <label for="peso">Peso em gramas</label>
                <input type="number" id="peso" placeholder="Digite o peso do produto" min="0" step="0.01">
              </div>

              <div class="field">
                <label for="milesimos">Milésimos</label>
                <input type="number" id="milesimos" placeholder="Digite os milésimos do produto" min="0" step="1">
              </div>

              <div class="field">
                <label>Categoria</label>
                <input type="text" id="categoriaVitrine" placeholder="Digite a categoria de vitrine do produto">
              </div>
            </div>

            <div class="metal-card">
              <div class="metal-card-header">
                <div>
                  <h3>Metais e valor por grama</h3>
                  <p>Cadastre, remova e use o valor por grama no custo do produto.</p>
                </div>
              </div>

              <div class="row row-3">
                <div class="field">
                  <label>Novo metal</label>
                  <input type="text" id="novoMetal" placeholder="Ex: Ouro">
                </div>

                <div class="field">
                  <label>Valor por grama</label>
                  <input type="text" id="valorGramaMetal" class="money" placeholder="R$ 0,00">
                </div>

                <button type="button" class="btn-add-metal" onclick="adicionarMetal()">
                  Adicionar metal
                </button>
              </div>

              <div class="row row-3">
                <div class="field">
                  <label>Valor/g do metal selecionado</label>
                  <input id="valorGramaSelecionado" type="text" readonly>
                </div>

                <div class="field destaque">
                  <label>Custo do metal no produto</label>
                  <input id="custoMetal" type="text" class="custo" readonly>
                </div>
              </div>

              <div id="listaMetais" class="lista-metais"></div>
            </div>

            <div class="row row-5">
              <div class="field">
                <label>Custo Compra Bruto</label>
                <input type="text" id="custoCompraBruto" class="money custo">
              </div>

              <div class="field">
                <label>Custo Insumo</label>
                <input type="text" id="custoInsumo" class="money custo">
              </div>

              <div class="field">
                <label>Milésimos de Banho</label>
                <input type="text" id="milesimosBanho" placeholder="Digite os milésimos de banho do produto">
              </div>

              <div class="field">
                <label>Custo do Banho</label>
                <input type="text" id="banhoCusto" class="money custo" readonly>
              </div>

              <div class="field">
                <label>Metal do Banho selecionado</label>
                <select id="metalBanho" name="metalBanho"></select>
              </div>
            </div>

            <div class="lucro-config">
              <div class="precificacao-info">
                Configure o lucro padrão. Quando a tabela for ATACADO, o sistema usa o percentual de atacado. Quando for VAREJO, usa o percentual de varejo.
              </div>

              <div class="row row-2">
                <div class="field destaque">
                  <label>% lucro atacado</label>
                  <input type="number" id="percentualAtacado" value="40" min="0" step="0.01">
                </div>

                <div class="field destaque">
                  <label>% lucro varejo</label>
                  <input type="number" id="percentualVarejo" value="60" min="0" step="0.01">
                </div>
              </div>
            </div>

            <div class="row row-3">
              <div class="field">
                <label>Tabela Preço 1</label>
                <select id="tipoTabela1">
                  <option value="VAREJO">VAREJO</option>
                  <option value="ATACADO">ATACADO</option>
                </select>
              </div>

              <div class="field destaque">
                <label>% Lucro Tabela 1</label>
                <input id="lucro1" type="text" readonly>
              </div>

              <div class="field">
                <label>Valor Tabela 1</label>
                <input id="valor1" type="text" readonly>
              </div>
            </div>

            <div class="row row-3">
              <div class="field">
                <label>Tabela Preço 2</label>
                <select id="tipoTabela2">
                  <option value="ATACADO">ATACADO</option>
                  <option value="VAREJO">VAREJO</option>
                </select>
              </div>

              <div class="field destaque">
                <label>% Lucro Tabela 2</label>
                <input id="lucro2" type="text" readonly>
              </div>

              <div class="field">
                <label>Valor Tabela 2</label>
                <input id="valor2" type="text" readonly>
              </div>
            </div>

            <div class="row row-1">
              <div class="field">
                <label>Preço Final</label>
                <input id="precoFin" type="text" class="money custo" placeholder="Escolha o preço que a peça deve custar">
              </div>
            </div>

            <button id="btnSalvar" onclick="salvarPrecificacao()" class="btn-salvar">
              Salvar
            </button>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="js/precificacao.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>