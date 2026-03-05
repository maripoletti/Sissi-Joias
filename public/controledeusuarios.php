<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Revendedoras - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/controledeusuarios.css" />
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
          <a href="estoque.php">Estoque</a>
          <a href="controledeusuarios.php" class="active">Controle de Usuários</a>
          <a href="impressoras.php">Impressoras</a>
          <a href="fornecedores.php">Fornecedores</a>
          <a href="revendedores.php">Revendedores</a>
        </nav>
      </aside>

      <main class="main">

        <header class="page-header">
          <div class="page-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M16 20c0-2.2-1.8-4-4-4H7c-2.2 0-4 1.8-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M9.5 12a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="2"/>
              <path d="M21 20c0-1.6-.9-3-2.3-3.6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M17.5 5.6a3.1 3.1 0 0 1 0 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>

          <div>
            <h1 class="page-title">Controle de Usuários</h1>
            <p class="page-subtitle">Gerencie as solicitações de cadastro.</p>
          </div>
        </header>

        <section class="panel">
          <div class="panel-title">NÍVEIS — INICIANTE AO PRO</div>

          <div class="levels" aria-label="Níveis">
            <div class="level">
              <div class="level-bar ametista"></div>
              <span class="level-name">Ametista</span>
            </div>

            <div class="level">
              <div class="level-bar safira"></div>
              <span class="level-name">Safira</span>
            </div>

            <div class="level">
              <div class="level-bar topazio"></div>
              <span class="level-name">Topázio</span>
            </div>

            <div class="level">
              <div class="level-bar esmeralda"></div>
              <span class="level-name">Esmeralda</span>
            </div>

            <div class="level">
              <div class="level-bar rubi"></div>
              <span class="level-name">Rubi</span>
            </div>
          </div>
        </section>

        <div class="tabs" role="tablist">
          <button class="tab active" type="button" data-tab="pendentes" role="tab" aria-selected="true">Pendentes</button>
          <button class="tab" type="button" data-tab="aprovadas" role="tab" aria-selected="false">Aprovadas</button>
          <button class="tab" type="button" data-tab="recusadas" role="tab" aria-selected="false">Recusadas</button>
        </div>

        <!-- LISTAS -->
        <section class="list" id="list-pendentes">
          <!-- CARD 1 -->
          <article class="user-card" data-id="101" data-status="pendente">
            <div class="user-top">
              <div class="user-left">
                <div class="avatar" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none">
                    <path d="M20 21c0-4.4-3.6-8-8-8s-8 3.6-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>

                <div class="user-info">
                  <h3 class="user-name">Maria Silva</h3>

                  <div class="meta">
                    <span class="meta-item">
                      <span class="ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                          <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" />
                          <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                      </span>
                      maria@email.com
                    </span>

                    <span class="meta-item">
                      <span class="ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                          <path d="M6.5 3.5h3l1.2 5-2 1.2c1.1 2.3 3 4.2 5.3 5.3l1.2-2 5 1.2v3c0 1-1 2-2.1 2C10.4 19.2 4.8 13.6 4.5 5.6 4.5 4.5 5.5 3.5 6.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                      </span>
                      (11) 99999-1234
                    </span>
                  </div>
                </div>
              </div>

              <span class="badge pending" data-badge>Pendente</span>
            </div>

            <div class="user-bottom">
              <div class="level-row">
                <span class="label">Nível:</span>

                <span class="pill ametista-pill" data-pill>
                  <span class="dot ametista-dot" data-dot></span>
                  <span data-pill-text>Ametista</span>
                </span>
              </div>

              <button class="link btn-alterar-nivel" type="button" data-action="nivel" data-nivel="ametista">
                Alterar nível
              </button>
            </div>

            <div class="actions">
              <button class="btn accept" type="button" data-action="accept">
                <span class="btn-ico" aria-hidden="true">✓</span>
                Aceitar
              </button>

              <button class="btn reject" type="button" data-action="reject">
                <span class="btn-ico" aria-hidden="true">✕</span>
                Recusar
              </button>
            </div>
          </article>

          <!-- CARD 2 -->
          <article class="user-card" data-id="102" data-status="pendente">
            <div class="user-top">
              <div class="user-left">
                <div class="avatar" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none">
                    <path d="M20 21c0-4.4-3.6-8-8-8s-8 3.6-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>

                <div class="user-info">
                  <h3 class="user-name">Ana Oliveira</h3>

                  <div class="meta">
                    <span class="meta-item">
                      <span class="ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                          <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" />
                          <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                      </span>
                      ana@email.com
                    </span>

                    <span class="meta-item">
                      <span class="ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                          <path d="M6.5 3.5h3l1.2 5-2 1.2c1.1 2.3 3 4.2 5.3 5.3l1.2-2 5 1.2v3c0 1-1 2-2.1 2C10.4 19.2 4.8 13.6 4.5 5.6 4.5 4.5 5.5 3.5 6.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                      </span>
                      (21) 98888-5678
                    </span>
                  </div>
                </div>
              </div>

              <span class="badge pending" data-badge>Pendente</span>
            </div>

            <div class="user-bottom">
              <div class="level-row">
                <span class="label">Nível:</span>

                <span class="pill ametista-pill" data-pill>
                  <span class="dot ametista-dot" data-dot></span>
                  <span data-pill-text>Ametista</span>
                </span>
              </div>

              <button class="link btn-alterar-nivel" type="button" data-action="nivel" data-nivel="ametista">
                Alterar nível
              </button>
            </div>

            <div class="actions">
              <button class="btn accept" type="button" data-action="accept">
                <span class="btn-ico" aria-hidden="true">✓</span>
                Aceitar
              </button>

              <button class="btn reject" type="button" data-action="reject">
                <span class="btn-ico" aria-hidden="true">✕</span>
                Recusar
              </button>
            </div>
          </article>
        </section>

        <section class="list" id="list-aprovadas" hidden></section>
        <section class="list" id="list-recusadas" hidden></section>

        <!-- MODAL NÍVEL -->
        <div class="modal-bg" id="modalBg" hidden></div>

        <div class="modal" id="modalNivel" hidden>
          <h3>Alterar nível</h3>

          <label for="nivelSelect">Nível</label>
          <select id="nivelSelect">
            <option value="ametista">Ametista</option>
            <option value="safira">Safira</option>
            <option value="topazio">Topázio</option>
            <option value="esmeralda">Esmeralda</option>
            <option value="rubi">Rubi</option>
          </select>

          <div class="modal-actions">
            <button type="button" class="btn secondary" id="cancelarNivel">Cancelar</button>
            <button type="button" class="btn primary" id="salvarNivel">Salvar</button>
          </div>
        </div>

      </main>

    </div>
  </div>

  <script src="controledeusuarios.js"></script>
  
</body>
</html>