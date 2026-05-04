<?php
$importados = $_SESSION['importados'] ?? [];
unset($_SESSION['importados']);

foreach ($importados as $p) {
    echo "{$p['name']} - R$ " . number_format($p['price'], 2, ',', '.') . " - {$p['stock']} <br>";
}

if (empty($importados)) {
    echo "Nada para mostrar";
}

?>

<a href="/produtos">
  <button>Voltar</button>
</a>