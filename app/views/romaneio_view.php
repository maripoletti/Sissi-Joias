<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Sissi Semi Joias e Acessórios</title>

  <link rel="stylesheet" href="styles/global.css">
  <link rel="stylesheet" href="styles/estilo2.css">
  <link rel="stylesheet" href="styles/romaneio.css">
</head>

<body>

<div class="container">
  <div class="card paineldecontrole">

    <aside class="sidebar"></aside>

    <main class="main" id="romaneio">

      <div class="topo">
        <h1>ROMANEIO</h1>
      </div>

      <div class="dados">

        <div class="campo">
          <label>Revendedora</label>
          <input type="text" placeholder="Nome da Revendedora">
        </div>

        <div class="campo">
          <label>Contato</label>
          <input type="text" placeholder="(00) 00000-0000">
        </div>

        <div class="campo">
          <label>Data</label>
          <input type="date">
        </div>

      </div>

      <table>

        <thead>
          <tr>
            <th>Categoria</th>
            <th>Produto</th>
            <th>Qtd</th>
            <th>Valor Unit.</th>
            <th>Total</th>
            <th class="no-print"></th>
          </tr>
        </thead>

        <tbody>

          <!-- BRINCOS -->
          <tr class="categoria" data-categoria="Brincos">
            <td colspan="5">BRINCOS</td>
            <td class="no-print">
              <button class="btn-add" onclick="adicionarProduto(this)">+</button>
            </td>
          </tr>

          <tr>
            <td>Brincos</td>
            <td>Brinco Coração</td>
            <td><input class="qtd" type="number" value="0" min="0"></td>
            <td><input class="valor" type="number" value="35" min="0"></td>
            <td class="total">R$ 0,00</td>
            <td class="no-print">
              <button class="btn-remove" onclick="removerProduto(this)">×</button>
            </td>
          </tr>

          <tr>
            <td>Brincos</td>
            <td>Brinco Argola</td>
            <td><input class="qtd" type="number" value="0" min="0"></td>
            <td><input class="valor" type="number" value="45" min="0"></td>
            <td class="total">R$ 0,00</td>
            <td class="no-print">
              <button class="btn-remove" onclick="removerProduto(this)">×</button>
            </td>
          </tr>

          <!-- COLARES -->
          <tr class="categoria" data-categoria="Colares">
            <td colspan="5">COLARES</td>
            <td class="no-print">
              <button class="btn-add" onclick="adicionarProduto(this)">+</button>
            </td>
          </tr>

          <tr>
            <td>Colares</td>
            <td>Colar Riviera</td>
            <td><input class="qtd" type="number" value="0" min="0"></td>
            <td><input class="valor" type="number" value="89" min="0"></td>
            <td class="total">R$ 0,00</td>
            <td class="no-print">
              <button class="btn-remove" onclick="removerProduto(this)">×</button>
            </td>
          </tr>

          <tr>
            <td>Colares</td>
            <td>Colar Elo Português</td>
            <td><input class="qtd" type="number" value="0" min="0"></td>
            <td><input class="valor" type="number" value="95" min="0"></td>
            <td class="total">R$ 0,00</td>
            <td class="no-print">
              <button class="btn-remove" onclick="removerProduto(this)">×</button>
            </td>
          </tr>

          <!-- PULSEIRAS -->
          <tr class="categoria" data-categoria="Pulseiras">
            <td colspan="5">PULSEIRAS</td>
            <td class="no-print">
              <button class="btn-add" onclick="adicionarProduto(this)">+</button>
            </td>
          </tr>

          <tr>
            <td>Pulseiras</td>
            <td>Pulseira Luxo</td>
            <td><input class="qtd" type="number" value="0" min="0"></td>
            <td><input class="valor" type="number" value="59" min="0"></td>
            <td class="total">R$ 0,00</td>
            <td class="no-print">
              <button class="btn-remove" onclick="removerProduto(this)">×</button>
            </td>
          </tr>

          <!-- ANÉIS -->
          <tr class="categoria" data-categoria="Anéis">
            <td colspan="5">ANÉIS</td>
            <td class="no-print">
              <button class="btn-add" onclick="adicionarProduto(this)">+</button>
            </td>
          </tr>

          <tr>
            <td>Anéis</td>
            <td>Anel Cravejado</td>
            <td><input class="qtd" type="number" value="0" min="0"></td>
            <td><input class="valor" type="number" value="49" min="0"></td>
            <td class="total">R$ 0,00</td>
            <td class="no-print">
              <button class="btn-remove" onclick="removerProduto(this)">×</button>
            </td>
          </tr>

          <!-- CONJUNTOS -->
          <tr class="categoria" data-categoria="Conjuntos">
            <td colspan="5">CONJUNTOS</td>
            <td class="no-print">
              <button class="btn-add" onclick="adicionarProduto(this)">+</button>
            </td>
          </tr>

          <tr>
            <td>Conjuntos</td>
            <td>Conjunto Luxo</td>
            <td><input class="qtd" type="number" value="0" min="0"></td>
            <td><input class="valor" type="number" value="149" min="0"></td>
            <td class="total">R$ 0,00</td>
            <td class="no-print">
              <button class="btn-remove" onclick="removerProduto(this)">×</button>
            </td>
          </tr>

        </tbody>

      </table>

      <div class="resumo">

        <div class="card">
          <span>Total de Peças</span>
          <h2 id="pecas">0</h2>
        </div>

        <div class="card">
          <span>Valor da Maleta</span>
          <h2 id="valorTotal">R$ 0,00</h2>
        </div>

      </div>

      <div class="acoes no-print">

        <button onclick="window.print()">
          Imprimir
        </button>

        <button onclick="gerarPDF()">
          Gerar PDF
        </button>

      </div>

    </main>

  </div>
</div>

<script src="js/romaneio.js"></script>
<script src="js/global.js"></script>

</body>
</html>