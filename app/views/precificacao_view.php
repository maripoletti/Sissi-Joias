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
                <label><input type="checkbox" checked> Acabado</label>
                <label><input type="checkbox" checked> Ativo</label>
                <label><input type="checkbox" checked> Compartilhar</label>
              </div>

              <div class="field">
                <label>Sincronizar com a Up vendas?</label>
                <select>
                  <option>Não</option>
                  <option>Sim</option>
                </select>
              </div>
            </div>

            <div class="row row-3">
              <div class="field">
                <label>Nome do produto</label>
                <input type="text" id="nome" placeholder="Digite o nome do produto">
              </div>

              <div class="field">
                <label>Marca</label>
                <input type="text" id="marca" placeholder="Digite a marca do produto">
              </div>

              <div class="field">
                <label>Custo Total</label>
                <input id="custoTotal" type="text" readonly>
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
                <label>NCM</label>
                <input type="text" id="ncm" placeholder="Digite o NCM do produto">
              </div>

              <div class="field">
                <label>Un. estoque</label>
                <input type="text" id="unidadeEstoque" placeholder="Digite a unidade de estoque do produto">
              </div>

              <div class="field">
                <label>Categoria Vitrine</label>
                <input type="text" id="categoriaVitrine" placeholder="Digite a categoria de vitrine do produto">
              </div>
            </div>

            <div class="row row-6">
              <div class="field">
                <label>Custo Compra Bruto</label>
                <input type="text" class="money custo">
              </div>

              <div class="field">
                <label>Custo Insumo</label>
                <input type="text" class="money custo">
              </div>

              <div class="field">
                <label>Milésimos</label>
                <input type="text" class="money custo">
              </div>

              <div class="field">
                <label>Banho Ouro</label>
                <input type="text" class="money custo">
              </div>

              <div class="field">
                <label>Banho Prata</label>
                <input type="text" class="money custo">
              </div>

              <div class="field">
                <label>Banho Ródio</label>
                <input type="text" class="money custo">
              </div>
            </div>

            <div class="row row-3">
              <div class="field">
                <label>Tabela Preço 1</label>
                <select>
                  <option>VAREJO</option>
                  <option>ATACADO</option>
                </select>
              </div>

              <div class="field destaque">
                <label>% Lucro Tabela 1</label>
                <input id="lucro1" type="text" readonly>
              </div>

              <div class="field">
                <label>Valor Tabela 1</label>
                <input id="valor1" type="text" class="money">
              </div>
            </div>

            <div class="row row-3">
              <div class="field">
                <label>Tabela Preço 2</label>
                <select>
                  <option>ATACADO</option>
                  <option>VAREJO</option>
                </select>
              </div>

              <div class="field">
                <label>% Lucro Tabela 2</label>
                <input id="lucro2" type="text" readonly>
              </div>

              <div class="field">
                <label>Valor Tabela 2</label>
                <input id="valor2" type="text" class="money">
              </div>
            </div>

            <div class="precificacao-info">
              O cálculo de lucro é baseado no custo total do produto.
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="precificacao.js"></script>
</body>
</html>