<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }

@media print {
  @page {
    size: 58mm auto;
    margin: 0;
    }

  body {
    margin: 0;
  }
}

body{
  width:58mm;
  font-family: monospace;
  font-size:10px;
}

.ticket{
  width:100%;
  padding:4px;
}

.center{ text-align:center; }

.divisor{
  border-top:1px dashed black;
  margin:4px 0;
}

.row{
  display:flex;
  justify-content:space-between;
  gap:4px;
}

.total{
  font-weight:bold;
  font-size:13px;
}
</style>
</head>
<body onload="window.print()">

<div class="ticket">

<div class="center">
<b>SISSI SEMIJOIAS E ACESSÓRIOS</b><br>
CNPJ: 49.455.057/0001-74
Comprovante de Venda
</div>

<?php
$total = 0;
?>

<div class="divisor"></div>

<?php foreach ($venda as $item): 
  $subtotal = $item['Price'] * $item['Quantity'];
  $total += $subtotal;
?>

<div>
  <div><?= $item['ProductName'] ?></div>

  <div class="row">
    <span><?= $item['Quantity'] ?> x <?= number_format($item['Price'],2,",",".") ?></span>
    <span>R$ <?= number_format($subtotal,2,",",".") ?></span>
  </div>
</div>

<?php endforeach; ?>

<div class="divisor"></div>

<div class="row total">
  <span>TOTAL</span>
  <span>R$ <?= number_format($total,2,",",".") ?></span>
</div>

<div class="divisor"></div>

<div class="center">
Pagamento: <?= $venda[0]['PaymentMethod'] ?><br>
Data: <?= date("d/m/Y H:i", strtotime($venda[0]['OrderDate'])) ?>
</div>

<div class="center" style="margin-top:6px;">
Obrigado pela preferência
</div>

</div>

</body>
</html>