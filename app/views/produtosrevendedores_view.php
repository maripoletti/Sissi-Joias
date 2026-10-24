<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
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

          <div class="acoes-em-massa" aria-label="Ações nos produtos">
            <button type="button" class="btn-primary" id="maletaClick" onclick="ativarMaletaClick()">Mover para maleta</button>
            <button type="button" class="btn-remover" id="excluirEmMassa" onclick="ativarExcluirClick()">Remover em massa</button>
            <button type="button" class="btn-cancelar-selecao" id="cancelarBtn" onclick="cancelarSelecao()" hidden>Sair da seleção</button>
          </div>
        </div>
        <p id="resumoSelecao" class="resumo-selecao" role="status" aria-live="polite" hidden></p>

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

        <div id="modalMaleta" class="modal" hidden>
          <div class="modal-real" role="dialog" aria-modal="true" aria-labelledby="tituloModalMaleta" tabindex="-1">
            <div class="form-cabecalho">
              <h2 id="tituloModalMaleta">Mover produtos</h2>
              <button type="button" class="modal-fechar" onclick="fecharModalMaleta()" aria-label="Fechar">×</button>
            </div>
            <p id="resumoMovimentacao" class="modal-descricao"></p>
            <form id="formMoverMaleta">
              <label for="revendedoraDestino">Revendedora de destino</label>
              <select id="revendedoraDestino" required>
                <option value="">Selecione a revendedora</option>
              </select>

              <label for="selectMaleta">Maleta de destino</label>
              <select id="selectMaleta">
                <option value="outra">Escolher maleta existente</option>
                <option value="nova">Criar nova maleta</option>
              </select>

              <div id="campoMaletaExistente">
                <label for="selectMaletaExistente">Escolha a maleta</label>
                <select id="selectMaletaExistente" required disabled>
                  <option value="">Escolha a revendedora primeiro</option>
                </select>
              </div>
              <div id="campoNovaMaleta" hidden>
                <label for="nomeNovaMaleta">Nome da nova maleta</label>
                <input type="text" id="nomeNovaMaleta" placeholder="Ex.: Maleta de novembro" maxlength="100">
              </div>
              <p class="modal-descricao">Os produtos sairão da origem e passarão para a revendedora e maleta escolhidas, mantendo seus preços e descontos.</p>
              <p class="modal-feedback" id="feedbackMaleta" role="alert"></p>
              <div class="modal-botoes">
                <button type="button" class="modal-cancelar" onclick="fecharModalMaleta()">Cancelar</button>
                <button type="submit" class="modal-confirmar" id="confirmarMovimentacao">Mover produtos</button>
              </div>
            </form>
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