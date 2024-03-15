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
        <?php
        echo "<div style='display: grid;'>";
            for($i = 0; $i < 90; $i+=10){
                for($j = 1; $j <= 10; $j++) {
                    $num = $j+$i;
                    echo "
                    <div style='grid-column: $j; grid-row: ". ($i+10)/10 . "; width:min-content;'>
                        <label for='check-$num'>$num</label>
                        <input type='checkbox' id='check-$num' name='num-$num' value='$num'>
                    </div>
                    ";
                }
            }
        echo "</div>";
        ?>
        <label for="puntata">Puntata: </label>
        <input type="number" name="puntata" id="puntata"><br><br>
        <input type="submit" value="Invia" style="margin: auto; display: block">
    </form>

<?php } else if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    http_response_code(400);
    if (!isset($_POST["puntata"])) {
        exit("I dati inviati sono incompleti");
    } else if ($_POST["puntata"] <= 0) {
        exit("La puntata deve essere maggiore di 0");
    }
    
    $numeri_utente = [];

    for($i = 1; $i<=90; $i++) {
        if(isset($_POST["num-$i"]) and $_POST["num-$i"] == $i) {
            array_push($numeri_utente, $i);
        }
    }
    
    if(count($numeri_utente) != 6) {
        exit("I numeri giocati devono essere 6");
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