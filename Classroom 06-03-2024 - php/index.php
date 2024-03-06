<?php
/*
    Creare un form dove inserire 6 numeri e inviarli al server
    Il server estrae 6 numeri casuali e verifica la vincita
*/
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estrazione Numeri</title>
    <style>
        p {
            margin: 0;
            text-align: center;
        }
        
        h1 {
            text-align: center;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body>
<?php if ($_SERVER["REQUEST_METHOD"] == "GET") { ?>

    <h1>Inserisci i numeri per la schedina</h1>

    <form action="." method="post">
        <input type="number" name="numeri[]" id="n1" max=90 min=0>
        <input type="number" name="numeri[]" id="n2" max=90 min=0>
        <input type="number" name="numeri[]" id="n3" max=90 min=0>
        <input type="number" name="numeri[]" id="n4" max=90 min=0>
        <input type="number" name="numeri[]" id="n5" max=90 min=0>
        <input type="number" name="numeri[]" id="n6" max=90 min=0><br><br>
        <label for="puntata">Puntata: </label>
        <input type="number" name="puntata" id="puntata"><br><br>
        <input type="submit" value="Invia" style="margin: auto; display: block">
    </form>

<?php } else if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    http_response_code(400);
    if (!isset($_POST["numeri"]) or !isset($_POST["puntata"])) {
        exit("I dati inviati sono incompleti");
    } else if ($_POST["puntata"] <= 0) {
        exit("La puntata deve essere maggiore di 0");
    } else if(count(array_unique($_POST["numeri"])) != 6) {
        exit("Non devono esistere numeri doppi tra i numeri giocati");
    }

    $numeri_utente = $_POST["numeri"];

    foreach ($numeri_utente as $numero) {
        if ($numero < 1 or $numero > 90) {
            exit("I numeri giocati devono essere tutti compresi tra 1 e 90");
        }
    }

    http_response_code(200);

    $numeri_generati = [rand(1,91), rand(1,91), rand(1,91), rand(1,91), rand(1,91), rand(1,91)];
    while(count(array_unique($numeri_generati)) != 6) {
        $numeri_generati = [rand(1,91), rand(1,91), rand(1,91), rand(1,91), rand(1,91), rand(1,90)];
    }
    $numeri_indovinati = [];

    foreach($numeri_generati as $numero_generato) {
        if (in_array($numero_generato, $numeri_utente)) {
            array_push($numeri_indovinati, $numero_generato);
        }
    }
    ?>

    <h1>Numeri estratti</h1>
    <?php if (count($numeri_indovinati)) { 
        $vincita = $_POST["puntata"] * count($numeri_indovinati);
        ?>
        <p>Hai indovinato <?= count($numeri_indovinati) ?> numero/i</p>
        <p>Essi sono: <?= implode(", ", $numeri_indovinati) ?></p>
        <p>Hai vinto <strong><?= $vincita ?></strong>€</p>
    <?php } else { ?>
        <p>Non hai vinto niente</p>
    <?php } ?>

<?php } else {
    http_response_code(405);
    exit("Metodo non valido");
} ?>
</body>
</html>