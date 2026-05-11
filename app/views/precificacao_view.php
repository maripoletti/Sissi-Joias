<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>
  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/paineldecontrole.css">
  <link rel="stylesheet" href="styles/precificacao.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="container">
    <div class="card paineldecontrole">

      <aside class="sidebar">
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
          <a href="/paineldecontrole">Painel de Controle</a>
          <a href="/produtos">Produtos</a>
          <a href="/vendas">Vendas</a>

          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
            <a href="/impressoras">Impressoras</a>
            <a href="/relatorios">Relatórios</a>
            <a href="/controledeusuarios">Controle de Revendedores</a>
            <a href="/fornecedores">Fornecedores</a>
            <a href="/cadastrarimpressora">Cadastrar Impressora</a>
            <a href="/produtosrevendedores">Produtos dos Revendedores</a>
            <a href="/precificacao" class="active">Precificação</a>
            <a href="/toprevendedoras">Top Revendedoras</a>
          <?php endif; ?>
        </nav>
      </aside>

      <main class="main">
        <section class="precificacao-page">
          <div class="precificacao-header">
            <h1>Precificação</h1>
            <p>Gerencie os custos, preços e margens do produto.</p>
          </div>

          <div class="precificacao-card">

            <div class="status-row">
              <div class="checks">
                <label><input type="checkbox" id="acabado" checked> Acabado</label>
                <label><input type="checkbox" id="ativo" checked> Ativo</label>
                <label><input type="checkbox" id="compartilhar" checked> Compartilhar</label>
              </div>

              <div class="field">
                <label>Sincronizar com a Up vendas?</label>
                <select id="sincronizarUpVendas">
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
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

            <div class="row row-5">
              <div class="field">
                <label>Referência</label>
                <input type="text" id="referencia" placeholder="Digite a referência do produto">
              </div>

              <div class="field">
                <label>Código Externo</label>
                <input type="text" id="codigoExterno" placeholder="Digite o código externo do produto">
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
                  <input type="text" id="novoMetal" placeholder="Ex: Ouro 18k">
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

            <div class="row row-6">
              <div class="field">
                <label>Custo Compra Bruto</label>
                <input type="text" id="custoCompraBruto" class="money custo">
              </div>

              <div class="field">
                <label>Custo Insumo</label>
                <input type="text" id="custoInsumo" class="money custo">
              </div>

              <div class="field">
                <label>Milésimos</label>
                <input type="text" id="milesimos" placeholder="Digite os milésimos do produto">
              </div>

              <div class="field">
                <label>Banho Ouro</label>
                <input type="text" id="banhoOuro" class="money custo">
              </div>

              <div class="field">
                <label>Banho Prata</label>
                <input type="text" id="banhoPrata" class="money custo">
              </div>

              <div class="field">
                <label>Banho Ródio</label>
                <input type="text" id="banhoRodio" class="money custo">
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

            <button onclick="salvarPrecificacao()" class="btn-salvar">
              Salvar
            </button>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="precificacao.js"></script>
</body>
</html>