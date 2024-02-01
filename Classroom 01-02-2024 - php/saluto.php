<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saluto</title>
</head>
    <body>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST["nome"];
            $surname = $_POST["cognome"];
            if (empty($name) || empty($surname)) {
                echo "<h1>Dati non corretti</h1>";
            } else {
                echo "<h1>Ciao $name $surname!</h1>";
            }
            }
        ?>
    </body>
</html>