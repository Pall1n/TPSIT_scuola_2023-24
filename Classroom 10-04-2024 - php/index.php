<?php
    $env = parse_ini_file("../.env");
    $host = $env["MYSQL_HOST"];
    $user = $env["MYSQL_USER"];
    $password = $env["MYSQL_PASS"];
    $dbname = "tpsit_10_04_2024";

    try {
        @$conn = new mysqli($host, $user, $password, $dbname);
    } catch (Exception $e) {
        die("Errore di connessione al database");
    }

    if ($conn->connect_error) {
        die("Errore di connessione al database");
    }

    $all_libri_query = "SELECT * FROM libri";
    $all_libri = $conn->query($all_libri_query);

    function verifica_attributi() {
        if ((isset($_POST["inserisci"]) or (isset($_POST["aggiorna"]) and isset($_POST["id"]) and !empty($_POST["id"]))) and isset($_POST["titolo"]) and isset($_POST["autore"]) and !empty($_POST["titolo"]) and !empty($_POST["autore"])){
            return true;
        } else if ((isset($_POST["cancella"]) or isset($_POST["inverti_disponibilita"])) and isset($_POST["id"]) and !empty($_POST["id"])) {
            return true;
        }
        return false;
    }

    function verifica_risultato_vuoto($num_rows) {
        if ($num_rows == 0) {
            exit("Errore: nessun libro trovato con l'id specificato");
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" and verifica_attributi()) {
        if (isset($_POST["inserisci"]) or isset($_POST["aggiorna"])) {
            $titolo = $_POST["titolo"];
            $autore = $_POST["autore"];
            $disponibile = true ? isset($_POST["disponibile"]) : false;

            if(isset($_POST["inserisci"])) {
                $insert_query = $conn->prepare("INSERT INTO libri (titolo, autore, disponibile) VALUES (?, ?, ?)");
                $insert_query->bind_param("ssi", $titolo, $autore, $disponibile);
                $insert_query->execute();
                $insert_query->close();
            } else {
                $id = $_POST["id"];

                $select_query = $conn->prepare("SELECT 1 FROM libri WHERE id = ?");
                $select_query->bind_param("i", $id);
                $select_query->execute();
                $result = $select_query->get_result();
                verifica_risultato_vuoto($result->num_rows);
                $select_query->close();

                $update_query = $conn->prepare("UPDATE libri SET titolo = ?, autore = ? WHERE id = ?");
                $update_query->bind_param("ssi", $titolo, $autore, $id);
                $update_query->execute();
                $update_query->close();
            }
            
            header("Location: index.php");
        } else if(isset($_POST["cancella"])) {
            $id = $_POST["id"];

            $delete_query = $conn->prepare("DELETE FROM libri WHERE id = ?");
            $delete_query->bind_param("i", $id);
            $delete_query->execute();
            verifica_risultato_vuoto($delete_query->affected_rows);
            $delete_query->close();

            header("Location: index.php");
        } else if(isset($_POST["inverti_disponibilita"])) {
            $id = $_POST["id"];

            $update_query = $conn->prepare("UPDATE libri SET disponibile = NOT disponibile WHERE id = ?");
            $update_query->bind_param("i", $id);
            $update_query->execute();
            verifica_risultato_vuoto($update_query->affected_rows);
            $update_query->close();

            header("Location: index.php");
        } else {
            exit("Errore: richiesta non valida");
        }
    } else if($_SERVER["REQUEST_METHOD"] == "POST") {
        exit("Errore: richiesta non valida");
    }

    $conn->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Libreria</title>
        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                margin: 0;
                padding: 20px;
            }

            h1 {
                margin: 0 0 20px 0;
            }

            #inserisci {
                margin-bottom: 10px;
            }

            table {
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid black;
                padding: 5px 10px;
            }

            th {
                background-color: #f2f2f2;
                font-size: 20px;
            }

            #inserisci input[type="text"], #inserisci input[type="submit"] {
                display: block;
                margin: 5px auto;
            }
        </style>
    </head>
    <body>
        <h1>Libreria</h1>
        <form method="post" id="inserisci">
            <label for="titolo">Inserisci un libro:</label>
            <input type="text" name="titolo" placeholder="Come scrivere in PHP" id="titolo" required>
            <label for="autore">Autore:</label>
            <input type="text" name="autore" placeholder="Mario Rossi" id="autore" required>
            <label for="disponibile">Disponibile:</label><input type="checkbox" name="disponibile" id="disponibile">
            <input type="submit" name="inserisci" value="Inserisci">
        </form>

        <?php
            if($all_libri->num_rows > 0) {
                echo <<<HTML
                    <table>
                        <tr>
                            <th>Titolo</th>
                            <th>Autore</th>
                            <th>Disponibile</th>
                            <th>Data e ora di inserimento</th>
                            <th>Operazioni</th>
                        </tr>
                HTML;

                while($row = $all_libri->fetch_assoc()) {
                    $disponibile = $row["disponibile"] ? "Si" : "No";

                    echo <<<HTML
                        <tr>
                            <form method="post">
                                <td><input type="text" name="titolo" value="{$row["titolo"]}" required disabled></td>
                                <td><input type="text" name="autore" value="{$row["autore"]}" required disabled></td>
                                <td>{$disponibile}</td>
                                <td>{$row["data_inserimento"]}</td>
                                <td>
                                    <input type="hidden" name="id" value="{$row["id"]}">
                                    <input type="submit" id="aggiorna" name="aggiorna" value="Aggiorna">
                                    <input type="submit" name="cancella" value="Cancella">
                                    <input type="submit" name="inverti_disponibilita" value="Inverti disponibilità">
                                </td>
                            </form>
                        </tr>
                    HTML;
                }

                echo <<<HTML
                    </table>

                    <script>
                        document.querySelectorAll("#aggiorna").forEach((aggiorna) => {
                            aggiorna.addEventListener("click", (e) => {
                                if(e.target.parentElement.parentElement.querySelector("input[name='titolo']").disabled) {
                                    e.preventDefault();
                                    e.target.parentElement.parentElement.querySelector("input[name='titolo']").disabled = false;
                                    e.target.parentElement.parentElement.querySelector("input[name='autore']").disabled = false;

                                    document.querySelectorAll("input[type='submit']").forEach((submit) => {
                                        if(submit !== e.target) {
                                            submit.disabled = true;
                                        }
                                    });

                                    const annulla = document.createElement("input");
                                    annulla.type = "submit";
                                    annulla.value = "Annulla";
                                    annulla.addEventListener("click", () => {
                                        e.target.parentElement.parentElement.querySelector("input[name='titolo']").disabled = true;
                                        e.target.parentElement.parentElement.querySelector("input[name='autore']").disabled = true;

                                        document.querySelectorAll("input[type='submit']").forEach((submit) => {
                                            submit.disabled = false;
                                        });
                                        
                                        annulla.remove();
                                    });

                                    e.target.parentElement.appendChild(annulla);
                                }
                            });
                        });
                    </script>
                HTML;
            }
        ?>
    </body>
</html>