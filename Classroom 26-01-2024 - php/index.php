<!DOCTYPE html>
<html>
<head>
	<title>Esercitazione 26/01/2024</title>
</head>
<body>
<?php
$numeri = array();

for($i=0; $i<10; $i++){
	$numeri[] = rand(0, 100);
}

$max = max($numeri);
$min = min($numeri);
$somma = array_sum($numeri);
$media = $somma/10;

echo "<p>La somma è: $somma</p>";
echo "<p>La media è: $media</p>";
echo "<p>Il valore massimo è: $max</p>";
echo "<p>Il valore minimo è: $min</p>";
?>
</body>
</html>