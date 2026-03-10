<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ width:58mm; font-family: monospace; font-size:12px; }
.ticket{ width:100%; padding:4px; }
.center{ text-align:center; }
.divisor{ border-top:1px dashed black; margin:4px 0; }
.row{ display:flex; justify-content:space-between; }
.total{ font-weight:bold; font-size:13px; }
</style>
</head>
<body onload="window.print()">

<div class="ticket">

<div class="center">
<b>SISSI SEMIJOIAS E ACESSÓRIOS</b><br>
CNPJ: 49.455.057/0001-74
Comprovante de Venda
</div>

<div class="divisor"></div>

<div class="row">
<span>Produto:</span>
<span><?= $venda['ProductName'] ?></span>
</div>

<div class="row">
<span>Qtd:</span>
<span><?= $venda['Quantity'] ?></span>
</div>

<div class="row">
<span>Preço unit:</span>
<span>R$ <?= number_format($venda['Price'],2,",",".") ?></span>
</div>

<div class="divisor"></div>

<div class="row total">
<span>TOTAL</span>
<span>R$ <?= number_format($venda['Sales'],2,",",".") ?></span>
</div>

<div class="divisor"></div>

<div class="center">
Pagamento: <?= $venda['PaymentMethod'] ?><br>
Data: <?= date("d/m/Y H:i", strtotime($venda['OrderDate'])) ?>
</div>

<div class="center" style="margin-top:6px;">
Obrigado pela preferência
</div>

</div>

</body>
</html>