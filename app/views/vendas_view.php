<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vendas - Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/vendas.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>

<body>
  <div class="container">
    <div class="card app">

      <aside class="sidebar">
        <h2>Sissi Semi Joias e Acessórios</h2>

        <nav>
          <a href="/paineldecontrole">Painel de Controle</a>
          <a href="/produtos">Produtos</a>
          <a href="/vendas" class="active">Vendas</a>
          
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
            <a href="/impressoras">Impressoras</a>
            <a href="/relatorios">Relatórios</a>
            <a href="/controledeusuarios">Controle de Revendedores</a>
            <a href="/fornecedores">Fornecedores</a>
            <a href="/cadastrarimpressora">Cadastrar Impressora</a>
            <a href="/produtosrevendedores">Produtos dos Revendedores</a>
          <?php endif; ?>
        </nav>
      </aside>

      <main class="main">
        <section class="wrap">

          <header class="top">
            <div class="top-left">
              <h1>Vendas</h1>
              <p class="subtitle">
                <span id="qtdVendas">1</span> venda(s) registrada(s)
              </p>
            </div>

            <div class="top-right">
              <div class="scanner-box">
                <input 
                  type="text" 
                  id="scanner" 
                  class="scanner-input"
                  placeholder="Digite o código ou escaneie" 
                  autofocus
                >
              </div>

              <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
                <form action="/api/novavenda/xml" method="post" enctype="multipart/form-data" class="xml-form">
                  <label for="xmlfile" class="btn-primary file-btn">Escolher XML</label>
                  <input type="file" id="xmlfile" name="xmlfile" accept=".xml" required>
                  <span id="file-name" class="file-name">Nenhum arquivo escolhido</span>
                  <button type="submit" class="btn-primary">Importar XML</button>
                </form>
              <?php endif; ?>
              
              <button class="btn-primary" id="btnRegistrar">
                + Registrar Venda
              </button>
            </div>
          </header>

          <section class="list" id="listaVendas">
          </section>

        </section>
      </main>

    </div>
  </div>

  <script>
    window.USER = {
      id: <?php echo $_SESSION['user_id'] ?? 'null'; ?>,
      role: <?php echo $_SESSION['role'] ?? 'null'; ?>
    };

    const xmlInput = document.getElementById('xmlfile');
    const fileName = document.getElementById('file-name');

    if (xmlInput && fileName) {
      xmlInput.addEventListener('change', function() {
        fileName.textContent = this.files.length > 0
          ? this.files[0].name
          : 'Nenhum arquivo escolhido';
      });
    }
  </script>

  <script src="script.js"></script>
</body>
</html>