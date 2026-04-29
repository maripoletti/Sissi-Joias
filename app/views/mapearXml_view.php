<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>

<?php
$labels = [
    "cProd"   => "Código do produto",
    "cEAN"    => "Código de barras (EAN)",
    "xProd"   => "Nome do produto",
    "NCM"     => "NCM",
    "CEST"    => "CEST",
    "CFOP"    => "CFOP",
    "uCom"    => "Unidade",
    "qCom"    => "Quantidade",
    "vUnCom"  => "Preço unitário",
    "vProd"   => "Valor total",
    "cEANTrib"=> "EAN tributável",
    "uTrib"   => "Unidade tributável",
    "qTrib"   => "Quantidade tributável",
    "vUnTrib" => "Preço unitário tributável",
    "indTot"  => "Compõe total"
];
?>

<form method="POST">
  <input type="hidden" name="xmltemp" value="<?= $xmlEncoded ?>">

  Código:
  <select name="map[code]">
    <?php foreach ($colunasXml as $col): ?>
      <option value="<?= $col ?>" <?= $col == 'cProd' ? 'selected' : '' ?>>
        <?= $col ?> - <?= $labels[$col] ?? '' ?>
      </option>
    <?php endforeach; ?>
  </select><br>

  Nome:
  <select name="map[name]">
    <?php foreach ($colunasXml as $col): ?>
      <option value="<?= $col ?>" <?= $col == 'xProd' ? 'selected' : '' ?>>
        <?= $col ?> - <?= $labels[$col] ?? '' ?>
      </option>
    <?php endforeach; ?>
  </select><br>

  Estoque:
  <select name="map[stock]">
    <?php foreach ($colunasXml as $col): ?>
      <option value="<?= $col ?>" <?= $col == 'qCom' ? 'selected' : '' ?>>
        <?= $col ?> - <?= $labels[$col] ?? '' ?>
      </option>
    <?php endforeach; ?>
  </select><br>

  Preço:
  <select name="map[price]">
    <?php foreach ($colunasXml as $col): ?>
      <option value="<?= $col ?>" <?= $col == 'vUnCom' ? 'selected' : '' ?>>
        <?= $col ?> - <?= $labels[$col] ?? '' ?>
      </option>
    <?php endforeach; ?>
  </select><br>

  <button type="submit" name="importar">Importar</button>
</form>

</body>
</html>