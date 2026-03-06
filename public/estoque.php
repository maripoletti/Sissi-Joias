<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Revendedoras - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/estoque.css" />
  <script src="js/estoque.js" defer></script>
</head>
<body>

  <div class="container">
    <div class="card app">

      <aside class="sidebar">
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
          <a href="/paineldecontrole">Painel de Controle</a>
          <a href="produtos.php">Produtos</a>
          <a href="vendas.php">Vendas</a>
          <a href="relatorios.php">Relatórios</a>
          <a href="estoque.php" class="active">Estoque</a>
          <a href="controledeusuarios.php">Controle de Usuários</a>
          <a href="impressoras.php">Impressoras</a>
          <a href="fornecedores.php">Fornecedores</a>
          <a href="revendedores.php">Revendedores</a>
          <a href="cadastroimpressora.php">Cadastrar Impressora</a>
        </nav>
      </aside>

      <main class="main estoque-page">
        <header class="estoque-topo">
          <div class="estoque-topo-info">
            <div class="estoque-icon">💎</div>
            <div>
              <h1>Estoque</h1>
              <p>Gerenciamento de joias</p>
            </div>
          </div>

          <button type="button" class="btn-nova-peca" id="btnNovaPeca">
            <span>＋</span> Nova Peça
          </button>
        </header>

        <section class="estoque-stats">
          <div class="stat-card">
            <div class="stat-icon gold">💎</div>
            <div class="stat-value" id="totalPecas">5</div>
            <div class="stat-label">Total de peças</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon lilac">⬡</div>
            <div class="stat-value" id="totalUnidades">17</div>
            <div class="stat-label">Unidades em estoque</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon red">⚠</div>
            <div class="stat-value" id="totalAlertas">2</div>
            <div class="stat-label">Alertas de estoque</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon gold">↗</div>
            <div class="stat-value" id="valorEstoque">R$ 33.770</div>
            <div class="stat-label">Valor em estoque</div>
          </div>
        </section>

        <section class="estoque-filtros">
          <div class="busca-box">
            <input type="text" id="buscaPeca" placeholder="Buscar por nome ou código..." />
          </div>

          <select id="filtroCategoria" class="filtro-select">
            <option value="todas">Todas categorias</option>
            <option value="Anel">Anel</option>
            <option value="Colar">Colar</option>
            <option value="Brinco">Brinco</option>
            <option value="Pulseira">Pulseira</option>
          </select>
        </section>

        <section class="estoque-grid" id="estoqueGrid">
          <article class="peca-card">
            <div class="peca-imagem">💍</div>
            <div class="peca-body">
              <div class="peca-top">
                <div>
                  <h3>Anel Solitário Diamante</h3>
                  <span class="peca-codigo">AN-001</span>
                </div>
                <span class="tag-categoria">Anel</span>
              </div>

              <p class="peca-desc">Ouro 18k · Diamante 0.5ct</p>

              <div class="peca-info">
                <span>📦 3 un.</span>
                <strong>R$ 4.800,00</strong>
              </div>

              <div class="peca-acoes">
                <button type="button" class="btn-editar" data-id="AN-001">Editar</button>
                <button type="button" class="btn-excluir">🗑</button>
              </div>
            </div>
          </article>

          <article class="peca-card">
            <div class="peca-imagem">📿</div>
            <div class="peca-body">
              <div class="peca-top">
                <div>
                  <h3>Colar Coração Prata</h3>
                  <span class="peca-codigo">CO-001</span>
                </div>
                <span class="tag-categoria">Colar</span>
              </div>

              <p class="peca-desc">Prata 925</p>

              <div class="peca-info">
                <span>📦 8 un.</span>
                <strong>R$ 390,00</strong>
              </div>

              <div class="peca-acoes">
                <button type="button" class="btn-editar" onclick="abrirModalTeste()">Editar</button>
                <button type="button" class="btn-excluir">🗑</button>
              </div>
            </div>
          </article>

          <article class="peca-card">
            <div class="peca-imagem">✨</div>
            <div class="peca-body">
              <div class="peca-top">
                <div>
                  <h3>Brinco Argola Ouro</h3>
                  <span class="peca-codigo">BR-001</span>
                </div>
                <span class="tag-categoria">Brinco</span>
              </div>

              <p class="peca-desc">Ouro 18k</p>

              <div class="peca-info">
                <span>📦 5 un.</span>
                <strong>R$ 1.290,00</strong>
              </div>

              <div class="peca-acoes">
                <button type="button" class="btn-editar" data-id="BR-001">Editar</button>
                <button type="button" class="btn-excluir">🗑</button>
              </div>
            </div>
          </article>

          <article class="peca-card alerta">
            <span class="badge-alerta">⚠ Estoque baixo</span>
            <div class="peca-imagem">⌚</div>
            <div class="peca-body">
              <div class="peca-top">
                <div>
                  <h3>Pulseira Tennis</h3>
                  <span class="peca-codigo">PU-001</span>
                </div>
                <span class="tag-categoria">Pulseira</span>
              </div>

              <p class="peca-desc">Ouro Branco · Diamantes</p>

              <div class="peca-info">
                <span>📦 1 un.</span>
                <strong>R$ 9.800,00</strong>
              </div>

              <div class="peca-acoes">
                <button type="button" class="btn-editar" data-id="PU-001">Editar</button>
                <button type="button" class="btn-excluir">🗑</button>
              </div>
            </div>
          </article>
        </section>
      </main>
    </div>
  </div>

  <div class="modal" id="modalEditar">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Editar Peça</h2>
        <span class="fechar-modal" id="fecharEditar">✕</span>
      </div>

      <div class="modal-body">
        <label for="editNome">Nome da peça</label>
        <input type="text" id="editNome">

        <label for="editCodigo">Código</label>
        <input type="text" id="editCodigo">

        <label for="editQuantidade">Quantidade</label>
        <input type="number" id="editQuantidade">

        <label for="editPreco">Preço</label>
        <input type="number" id="editPreco" step="0.01">

        <button type="button" class="btn-salvar" id="salvarEdicao">
          Salvar alterações
        </button>
      </div>
    </div>
  </div>

  <script src="estoque.js"></script>

</body>
</html>