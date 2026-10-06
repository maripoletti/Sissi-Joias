<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Produtos dos Revendedores</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/produtosrevendedores.css">
</head>
<body>
  <div class="container">
    <div class="card app">
      <aside class="sidebar"></aside>

      <main class="main">
        <header class="top">
          <h1>Produtos dos Revendedores</h1>
        </header>

        <div class="faixa-topo">
          <div class="filtros">
            <input type="text" id="filtroProduto" placeholder="Buscar produto...">
            <input type="text" id="filtroRevendedor" placeholder="Buscar revendedor...">
          </div>

          <div>
            <button class="btn-primary" id="prosseguirBtn" style="display: none;">Prosseguir</button>
            <button type="button" class="btn-secondary" id="cancelarBtn" onclick="cancelarSelecao()" style="display: none;">Cancelar</button>
          </div>

          <div class="emMassa">
            <button type="button" class="btn-primary" id="maletaClick" onclick="ativarMaletaClick()">Adicionar em Maleta</button>
            <button type="button" class="btn-primary" id="excluirEmMassa" onclick="ativarExcluirClick()">Remover em Massa</button>
          </div>
        </div>

        <div class="tabela-container">
          <table id="tabelaRevendedores">
            <thead>
              <tr>
                <th class="col-selecao" style="display:none"></th>
                <th>Produto</th>
                <th>Revendedor</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>

          <div id="sentinela"></div>
        </div>

        <div id="modalMaleta" class="modal" style="display:none;">
          <div class="modal-real">
              <div class="form-cabecalho">
                  <h2>Adicionar em Maleta</h2>

                  <button
                      class="modal-fechar"
                      onclick="fecharModalMaleta()">
                      ×
                  </button>
              </div>

              <select id="selectMaleta">
                  <option value="nova">Criar nova maleta</option>
                  <option value="outra">Adicionar em maleta já existente</option>
              </select>

              <input type="text" id="nomeNovaMaleta" placeholder="Nome da nova maleta">
              <select id="selectMaletaExistente" style="display:none;">
                
              </select>

              <div class="modal-botoes">
                  <button
                      class="modal-cancelar"
                      onclick="fecharModalMaleta()">
                      Cancelar
                  </button>

                  <button
                      class="modal-confirmar"
                      onclick="confirmarMaleta()">
                      Confirmar
                  </button>
              </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="js/produtosrevendedores.js"></script>
  <script>
    window.userData = {
        nome: <?= json_encode($_SESSION['usuario_nome'] ?? 'Usuário') ?>,
        role: <?= json_encode($_SESSION['role'] ?? 0) ?>
    };
  </script>
  <script src="js/global.js"></script>
</body>
</html>