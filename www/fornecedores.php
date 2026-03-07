<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fornecedores - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/produtos.css">
  <link rel="stylesheet" href="styles/fornecedores.css">
  <link rel="shortcut icon" href=".ico" type="image/x-icon">
</head>
<body>

  <div class="container">
    <div class="card app">

        <aside class="sidebar">
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
            <a href="/paineldecontrole">Painel de Controle</a>
            <a href="/produtos">Produtos</a>
            <a href="/vendas">Vendas</a>
            <a href="/relatorios">Relatórios</a>
            <a href="/impressoras">Impressoras</a>

            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
            <a href="/controledeusuarios">Controle de Usuários</a>
            <a href="/fornecedores" class="active">Fornecedores</a>
            <a href="/cadastrarimpressora">Cadastrar Impressora</a>
            <?php endif; ?>

        </nav>
        </aside>

      <main class="main fornecedores-page">
        <div class="page-header">
          <div>
            <h1>Fornecedores</h1>
            <p>Gerencie seus fornecedores e parceiros</p>
          </div>

          <button class="btn-novo" id="btnNovoFornecedor">
            + Novo Fornecedor
          </button>
        </div>

        <div class="toolbar">
          <div class="search-box">
            <input type="text" id="buscaFornecedor" placeholder="Buscar fornecedor..." />
          </div>
        </div>

        <section class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon purple">👥</div>
            <div>
              <strong id="totalFornecedores">5</strong>
              <span>Total de fornecedores</span>
            </div>
          </div>
        </section>

        <section class="fornecedores-grid" id="listaFornecedores">

          <article class="fornecedor-card">
            <div class="card-top">
              <div>
                <h3>TechSupply Ltda</h3>
                <small>12.345.678/0001-90</small>
              </div>
            </div>

            <div class="card-info">
              <p>✉ contato@techsupply.com.br</p>
              <p>☎ (11) 98765-4321</p>
              <p>📍 Rua da Tecnologia, 100 - São Paulo, SP</p>
            </div>

            <div class="card-footer">
              <div class="acoes">
                <button class="btn-acao editar">Editar</button>
                <button class="btn-acao excluir">Excluir</button>
              </div>
            </div>
          </article>

          <article class="fornecedor-card">
            <div class="card-top">
              <div>
                <h3>LogiMax Transportes</h3>
                <small>23.456.789/0001-01</small>
              </div>
            </div>

            <div class="card-info">
              <p>✉ comercial@logimax.com.br</p>
              <p>☎ (21) 97654-3210</p>
              <p>📍 Av. Brasil, 500 - Rio de Janeiro, RJ</p>
            </div>

            <div class="card-footer">
              <div class="acoes">
                <button class="btn-acao editar">Editar</button>
                <button class="btn-acao excluir">Excluir</button>
              </div>
            </div>
          </article>

          <article class="fornecedor-card">
            <div class="card-top">
              <div>
                <h3>Material Express</h3>
                <small>34.567.890/0001-12</small>
              </div>
            </div>

            <div class="card-info">
              <p>✉ vendas@materialexpress.com.br</p>
              <p>☎ (31) 96543-2100</p>
              <p>📍 Rua dos Materiais, 250 - Belo Horizonte, MG</p>
            </div>

            <div class="card-footer">
              <div class="acoes">
                <button class="btn-acao editar">Editar</button>
                <button class="btn-acao excluir">Excluir</button>
              </div>
            </div>
          </article>

          <article class="fornecedor-card">
            <div class="card-top">
              <div>
                <h3>Sabor & Cia Alimentos</h3>
                <small>45.678.901/0001-23</small>
              </div>
            </div>

            <div class="card-info">
              <p>✉ pedidos@saborcia.com.br</p>
              <p>☎ (41) 95432-1000</p>
              <p>📍 Rua Gastronômica, 80 - Curitiba, PR</p>
            </div>

            <div class="card-footer">
              <div class="acoes">
                <button class="btn-acao editar">Editar</button>
                <button class="btn-acao excluir">Excluir</button>
              </div>
            </div>
          </article>

          <article class="fornecedor-card">
            <div class="card-top">
              <div>
                <h3>ServiPro Consultoria</h3>
                <small>56.789.012/0001-34</small>
              </div>
            </div>

            <div class="card-info">
              <p>✉ contato@servipro.com.br</p>
              <p>☎ (51) 94321-0000</p>
              <p>📍 Av. Consultores, 300 - Porto Alegre, RS</p>
            </div>

            <div class="card-footer">
              <div class="acoes">
                <button class="btn-acao editar">Editar</button>
                <button class="btn-acao excluir">Excluir</button>
              </div>
            </div>
          </article>

        </section>
      </main>
    </div>
  </div>

  <div class="modal-overlay" id="modalFornecedor">
    <div class="modal-fornecedor">
      <div class="modal-header">
        <h2>Novo Fornecedor</h2>
        <button type="button" class="modal-close" id="fecharModalFornecedor">&times;</button>
      </div>

      <form id="formFornecedor">
        <div class="form-grid">
          <div class="form-group">
            <label for="nomeFornecedor">Nome *</label>
            <input type="text" id="nomeFornecedor" placeholder="Nome do fornecedor" required>
          </div>

          <div class="form-group">
            <label for="cnpjFornecedor">CNPJ</label>
            <input type="text" id="cnpjFornecedor" placeholder="00.000.000/0000-00">
          </div>

          <div class="form-group">
            <label for="emailFornecedor">Email</label>
            <input type="email" id="emailFornecedor" placeholder="contato@empresa.com">
          </div>

          <div class="form-group">
            <label for="telefoneFornecedor">Telefone</label>
            <input type="text" id="telefoneFornecedor" placeholder="(00) 00000-0000">
          </div>

          <div class="form-group full">
            <label for="enderecoFornecedor">Endereço</label>
            <input type="text" id="enderecoFornecedor" placeholder="Endereço completo">
          </div>

          <div class="form-group full">
            <label for="obsFornecedor">Observações</label>
            <textarea id="obsFornecedor" rows="5" placeholder="Notas adicionais..."></textarea>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancelar" id="cancelarModalFornecedor">Cancelar</button>
          <button type="submit" class="btn-salvar" id="btnSalvarFornecedor">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

  <script src="fornecedores.js"></script>
</body>
</html>