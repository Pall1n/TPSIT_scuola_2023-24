<?php
    $auto_disponibili = [
        "Fiat Panda" => [
            "prezzo_base" => 10000,
            "motore" => ["1.0 benzina" => 0, "1.2 diesel" => 1500],
            "optionals" => ["aria condizionata" => 300, "bluetooth" => 100, "cambio automatico" => 500, "quinto posto" => 400]
        ],
        "Fiat 500" => [
            "prezzo_base" => 11500,
            "motore" => ["1.2 benzina" => 0, "1.4 ibrida" => 2000],
            "optionals" => ["aria condizionata" => 300, "sensori di parcheggio" => 400, "finestrini elettrici" => 200, "bluetooth" => 100]
        ],
        "Wolkswagen Golf" => [
            "prezzo_base" => 25000,
            "motore" => ["1.5 benzina" => 0, "2.0 diesel" => 4000, "2.0 metano" => 3000],
            "optionals" => ["aria condizionata" => 300, "tetto apribile" => 1000, "sensori di parcheggio" => 550, "sedile ergonomico" => 400]
        ],
        "Audi A3" => [
            "prezzo_base" => 30000,
            "motore" => ["1.5 ibrida" => 0, "2.0 diesel" => 5000, "2.2 diesel" => 7000],
            "optionals" => ["aria condizionata" => 300, "cambio automatico" => 500, "tetto apribile" => 1000, "controllo di crociera" => 300, "sensori di parcheggio" => 600]
        ]
    ];

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        echo json_encode($auto_disponibili);
        exit;
    } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST["nome"]) || !isset($_POST["cognome"]) || !isset($_POST["modello"]) || !isset($_POST["motore"])) {
            http_response_code(400);
            exit("Compila tutti i campi obbligatori");
        }

        $nome = $_POST["nome"];
        $cognome = $_POST["cognome"];
        $modello = $_POST["modello"];
        $motore = $_POST["motore"];
        $optionals = $_POST["optionals"] ?? [];
        $annotazioni = $_POST["annotazioni"] ?? "";

        if (!array_key_exists($modello, $auto_disponibili)) {
            http_response_code(400);
            exit("Modello non valido");
        }

        if (!array_key_exists($motore, $auto_disponibili[$modello]["motore"])) {
            http_response_code(400);
            exit("Motore non valido");
        }

        foreach ($optionals as $optional) {
            if (!array_key_exists($optional, $auto_disponibili[$modello]["optionals"])) {
                http_response_code(400);
                exit("Uno o più optionals non sono validi");
            }
        }

        $costo_finale = $auto_disponibili[$modello]["prezzo_base"];
        $costo_finale += $auto_disponibili[$modello]["motore"][$motore];

        foreach ($optionals as $optional) {
            $costo_finale += $auto_disponibili[$modello]["optionals"][$optional];
        }
    } else {
        http_response_code(405);
        exit("Metodo non valido");
    }
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preventivo</title>
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
    <h1>Preventivo per <?php echo $nome . " " . $cognome ?></h1>
    <p>Modello: <strong><?php echo $modello ?></strong></p>
    <p>Motore: <strong><?php echo $motore ?></strong></p><br>
    <p style="width: 300px;">Optionals: <strong><?php echo empty($optionals) ? "nessuno" : implode(", ", $optionals) ?></strong></p><br>
    <p style="width: 300px;">Annotazioni: <strong><?php echo empty($annotazioni) ? "nessuna annotazione" : $annotazioni ?></strong></p><br>
    <p>Costo finale: <strong><?php echo $costo_finale ?>€</strong></p>
</body>

</html>