<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Impressoras - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/produtos.css">
  <link rel="stylesheet" href="styles/impressoras.css">
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
          <a href="/estoque">Estoque</a>
          <a href="/controledeusuarios">Controle de Usuários</a>
          <a href="/impressoras" class="active">Impressoras</a>
          <a href="/fornecedores">Fornecedores</a>
          <a href="/revendedores">Revendedores</a>
          <a href="cadastroimpressora.php">Cadastrar Impressora</a>
        </nav>
      </aside>

      <main class="main">

        <div class="page-header">
          <div>
            <h1 class="page-title">Impressoras <span>·</span></h1>
            <p class="page-subtitle">Gerencie impressoras fiscais e de etiquetas</p>
          </div>
        </div>

        <div class="stats-row">
          <div class="stat-card total">
            <div class="stat-label">Total</div>
            <div class="stat-value" id="stat-total">0</div>
          </div>
          <div class="stat-card nf">
            <div class="stat-label">Nota Fiscal</div>
            <div class="stat-value" id="stat-nf">0</div>
          </div>
          <div class="stat-card et">
            <div class="stat-label">Etiquetas</div>
            <div class="stat-value" id="stat-et">0</div>
          </div>
          <div class="stat-card online">
            <div class="stat-label">Online</div>
            <div class="stat-value" id="stat-online">0</div>
          </div>
        </div>

        <div class="filter-tabs">
          <button class="filter-tab active" data-filter="all"     type="button" onclick="setFilter('all',this)">Todas</button>
          <button class="filter-tab"        data-filter="nf"      type="button" onclick="setFilter('nf',this)">Nota Fiscal</button>
          <button class="filter-tab et"     data-filter="et"      type="button" onclick="setFilter('et',this)">Etiquetas</button>
          <button class="filter-tab"        data-filter="online"  type="button" onclick="setFilter('online',this)">Online</button>
          <button class="filter-tab"        data-filter="offline" type="button" onclick="setFilter('offline',this)">Offline</button>
        </div>

        <div class="printer-grid" id="printer-grid"></div>

        <div class="empty-state" id="empty-state">
          <div class="empty-icon">🖨️</div>
          <p>Nenhuma impressora encontrada</p>
        </div>

      </main>
    </div>
  </div>

  <!-- MODAL -->
  <div class="modal-overlay" id="modal-overlay" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true">
      <div class="modal-title">Nova Impressora</div>
      <div class="modal-sub">Adicione uma impressora fiscal ou de etiquetas</div>

      <div class="form-group">
        <label class="form-label">Tipo de Impressora</label>
        <div class="type-selector">
          <div class="type-opt selected" id="opt-nf" onclick="selectType('nf')" role="button" tabindex="0">
            <div class="type-opt-icon">🧾</div>
            <div class="type-opt-label">Nota Fiscal</div>
            <div class="type-opt-sub">Cupom e NF-e</div>
          </div>
          <div class="type-opt et-opt" id="opt-et" onclick="selectType('et')" role="button" tabindex="0">
            <div class="type-opt-icon">🏷️</div>
            <div class="type-opt-label">Etiquetas</div>
            <div class="type-opt-sub">Nome + Preço</div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Nome da Impressora</label>
        <input class="form-input" id="inp-name" type="text" placeholder="Ex: Caixa 01 - Fiscal"/>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Modelo</label>
          <input class="form-input" id="inp-model" type="text" placeholder="Ex: Elgin i9"/>
        </div>
        <div class="form-group">
          <label class="form-label">Conexão</label>
          <select class="form-select" id="inp-conn">
            <option value="USB">USB</option>
            <option value="Rede (TCP/IP)">Rede (TCP/IP)</option>
            <option value="Bluetooth">Bluetooth</option>
            <option value="Serial">Serial</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">IP</label>
          <input class="form-input" id="inp-ip" type="text" placeholder="192.168.1.100"/>
        </div>
        <div class="form-group">
          <label class="form-label">Setor</label>
          <input class="form-input" id="inp-sector" type="text" placeholder="Ex: Caixa, Estoque"/>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Status inicial</label>
        <select class="form-select" id="inp-status">
          <option value="online">Online</option>
          <option value="offline">Offline</option>
          <option value="standby">Standby</option>
        </select>
      </div>

      <!-- Sem botões (salva com Enter / fecha com ESC ou clique fora) -->
      <div class="modal-hint">Dica: pressione <b>Enter</b> para salvar. <b>Esc</b> para fechar.</div>
    </div>
  </div>

  <div class="toast" id="toast">
    <div class="toast-dot"></div>
    <span id="toast-msg">Ok!</span>
  </div>

  <script src="impressoras.js"></script>
</body>
</html>