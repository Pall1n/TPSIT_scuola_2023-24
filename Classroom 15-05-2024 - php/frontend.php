<!DOCTYPE html>
<html>

<head>
    <title>Libreria</title>

    <!-- Qualche stile per rendere la pagina almeno un minimo facile da utilizzare -->
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
            margin: 10px 0 20px 0;
        }

        #titolo_inserimento {
            margin: 0 auto 10px auto;
            display: block;
            width: fit-content;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px 10px;
        }

        th {
            background-color: #f2f2f2;
            font-size: 20px;
        }

        .inserisci input[type="text"],
        .inserisci input[type="submit"],
        .inserisci select {
            display: block;
            margin: 5px auto;
        }

        #autore {
            width: 100%;
        }

        #inserimenti {
            display: flex;
            justify-content: center;
            gap: 20px;
            width: 100%;
            margin-bottom: 20px;
        }

        #titolo_tabella {
            margin: 10px 0 10px 0;
        }
    </style>
</head>

<body>
    <select name="id_casa_editrice" id="id_casa_editrice" onchange="window.location.href = 'index.php?id_casa_editrice=' + this.value">
        <?php
        // Inserisco tutte le case editrici nel select, così da permettere all'utente di selezionare quella di cui vuole vedere i libri
        if (isset($all_case_editrici) and $all_case_editrici->num_rows > 0) {
            while ($row = $all_case_editrici->fetch_assoc()) {
                if (isset($_GET["id_casa_editrice"]) and $_GET["id_casa_editrice"] == $row["id"]) {
                    echo "<option value='{$row["id"]}' selected>{$row["nome"]}</option>";
                } else {
                    echo "<option value='{$row["id"]}'>{$row["nome"]}</option>";
                }
            }
        } else {
            echo "<option value=''>Nessuna casa editrice trovata</option>";
        }
        ?>
    </select>

    <?php
    if (isset($info_casa_editrice)) {
        echo "<p>Sito web: {$info_casa_editrice["sito_web"]}</p>";
    }
    ?>

    <h1>Inserimenti</h1>

    <div id="inserimenti">
        <form method="post" class="inserisci">
            <h3 id="titolo_inserimento">Casa editrice</h3>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" placeholder="Mondadori" id="nome" required>
            <label for="sito_web">Sito web:</label>
            <input type="text" name="sito_web" placeholder="www.mondadori.it" id="sito_web" required>
            <input type="submit" name="inserisci_casa_editrice" value="Inserisci">
        </form>

        <form method="post" class="inserisci">
            <h3 id="titolo_inserimento">Autore</h3>
            <label for="titolo">Nome:</label>
            <input type="text" name="nome" placeholder="Mario" id="nome" required>
            <label for="autore">Cognome:</label>
            <input type="text" name="cognome" placeholder="Rossi" id="cognome" required>
            <input type="submit" name="inserisci_autore" value="Inserisci">
        </form>

        <form method="post" class="inserisci">
            <h3 id="titolo_inserimento">Libro</h3>
            <label for="titolo">Titolo:</label>
            <input type="text" name="titolo" placeholder="Come scrivere in PHP" id="titolo" required>
            <label for="autore">Autore:</label>
            <select name="autore" id="autore" required>
                <?php
                if ($all_autori->num_rows > 0) {
                    while ($row = $all_autori->fetch_assoc()) {
                        echo "<option value='{$row["id"]}'>{$row["nome"]} {$row["cognome"]}</option>";
                    }
                }
                ?>
            </select>
            <label for="disponibile">Disponibile:</label><input type="checkbox" name="disponibile" id="disponibile">
            <input type="submit" name="inserisci_libro" value="Inserisci">
        </form>
    </div>

    <hr style="width: 50%">

    <?php
    if (isset($all_libri) and $all_libri->num_rows > 0) {
        echo "<h2 id='titolo_tabella'>Libri</h2>";

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

        while ($row = $all_libri->fetch_assoc()) {
            $autore = $row["nome_autore"] . " " . $row["cognome_autore"];
            $disponibile = $row["disponibile"] ? "Si" : "No";

            echo <<<HTML
                        <tr>
                            <form method="post">
                                <td><input type="text" name="titolo" value="{$row["titolo"]}" required disabled></td>
                                <!--    Cerco di ottimizzare il sito, inserendo solo l'autore del libro e non tutti gli autori presenti sul database
                                        Con lo script implementato sotto, l'utente può comunque cambiare l'autore del libro in fase di modifica -->
                                <td><select name="autore" required disabled><option value="{$row["id_autore"]}">{$autore}</option></select></td>
                                <td>{$disponibile}</td>
                                <td>{$row["data_inserimento"]}</td>
                                <td>
                                    <input type="hidden" name="id" value="{$row["id"]}">
                                    <input type="submit" id="aggiorna_libro" name="aggiorna_libro" value="Aggiorna">
                                    <input type="submit" name="cancella_libro" value="Cancella">
                                    <input type="submit" name="inverti_disponibilita" value="Inverti disponibilità">
                                </td>
                            </form>
                        </tr>
                    HTML;
        }

        echo "</table>";
    } else {
        echo "<p>Nessun libro trovato</p>";
    }

    if ($all_autori->num_rows > 0) {
        echo "<h2 id='titolo_tabella'>Autori</h2>";

        echo <<<HTML
                    <table>
                        <tr>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Operazioni</th>
                        </tr>
                HTML;

        $all_autori->data_seek(0);
        while ($row = $all_autori->fetch_assoc()) {
            echo <<<HTML
                        <tr>
                            <form method="post">
                                <td><input type="text" name="nome" value="{$row["nome"]}" required disabled></td>
                                <td><input type="text" name="cognome" value="{$row["cognome"]}" required disabled></td>
                                <td>
                                    <input type="hidden" name="id" value="{$row["id"]}">
                                    <input type="submit" id="aggiorna_autore" name="aggiorna_autore" value="Aggiorna">
                                    <input type="submit" name="cancella_autore" value="Cancella">
                                </td>
                            </form>
                        </tr>
                    HTML;
        }

        echo "</table>";
    } else {
        echo "<p>Nessun autore trovato</p>";
    }

    if ($all_case_editrici->num_rows > 0) {
        echo "<h2 id='titolo_tabella'>Case editrici</h2>";

        echo <<<HTML
                    <table>
                        <tr>
                            <th>Nome</th>
                            <th>Sito web</th>
                            <th>Operazioni</th>
                        </tr>
                HTML;

        $all_case_editrici->data_seek(0);
        while ($row = $all_case_editrici->fetch_assoc()) {
            echo <<<HTML
                        <tr>
                            <form method="post">
                                <td><input type="text" name="nome" value="{$row["nome"]}" required disabled></td>
                                <td><input type="text" name="sito_web" value="{$row["sito_web"]}" required disabled></td>
                                <td>
                                    <input type="hidden" name="id" value="{$row["id"]}">
                                    <input type="submit" id="aggiorna_casa_editrice" name="aggiorna_casa_editrice" value="Aggiorna">
                                    <input type="submit" name="cancella_casa_editrice" value="Cancella">
                                </td>
                            </form>
                        </tr>
                    HTML;
        }

        echo "</table>";
    } else {
        echo "<p>Nessuna casa editrice trovata</p>";
    }
    ?>

    <script>
        // Uno script che mi permette di dare la possibilità all'utente di modificare i campi delle varie tabelle mostrate

        let autori = {};
        document.querySelectorAll("#autore option").forEach((option) => {
            autori[option.value] = option.innerText;
        });

        document.querySelectorAll("#aggiorna_libro").forEach((aggiorna_libro) => {
            aggiorna_libro.addEventListener("click", (e) => {
                let titolo = e.target.parentElement.parentElement.querySelector("input[name='titolo']");
                let autore = e.target.parentElement.parentElement.querySelector("select[name='autore']");

                if (e.target.parentElement.parentElement.querySelector("input[name='titolo']").disabled) {
                    let autore_selected = [autore.value, autore.options[0].innerText];

                    e.preventDefault();
                    titolo.disabled = false;

                    // Aggiungo, al select dell'autore del libro che si vuole modificare, tutti gli autori presenti nel database, così da permettere all'utente di cambiarlo
                    for (let id in autori) {
                        if (id == autore.value) continue;
                        let option = document.createElement("option");
                        option.value = id;
                        option.innerText = autori[id];
                        autore.appendChild(option);
                    }
                    autore.disabled = false;

                    document.querySelectorAll("input[type='submit']").forEach((submit) => {
                        if (submit !== e.target) {
                            submit.disabled = true;
                        }
                    });

                    const annulla = document.createElement("input");
                    annulla.type = "submit";
                    annulla.value = "Annulla";
                    annulla.addEventListener("click", () => {
                        titolo.disabled = true;
                        autore.disabled = true;

                        /*  Svuoto il select dell'autore del libro di cui si è annullata la modifica e lascio solo 
                            l'autore selezionato inizialmente, così da non appesantire troppo la pagina */
                        autore.innerHTML = "";
                        let option = document.createElement("option");
                        option.value = autore_selected[0];
                        option.innerText = autore_selected[1];
                        autore.appendChild(option);

                        document.querySelectorAll("input[type='submit']").forEach((submit) => {
                            submit.disabled = false;
                        });

                        annulla.remove();
                    });

                    e.target.parentElement.appendChild(annulla);
                }
            });
        });

        // Script simile a quello sopra, ma per autori e case editrici (più semplice, dato che non c'è un select da popolare)
        document.querySelectorAll("#aggiorna_autore, #aggiorna_casa_editrice").forEach((aggiorna_autore) => {
            aggiorna_autore.addEventListener("click", (e) => {
                let all_inputs = e.target.parentElement.parentElement.querySelectorAll("input[type='text']");

                if (all_inputs[0].disabled) {
                    e.preventDefault();
                    all_inputs.forEach((input) => {
                        input.disabled = false;
                    });

                    document.querySelectorAll("input[type='submit']").forEach((submit) => {
                        if (submit !== e.target) {
                            submit.disabled = true;
                        }
                    });

                    const annulla = document.createElement("input");
                    annulla.type = "submit";
                    annulla.value = "Annulla";
                    annulla.addEventListener("click", () => {
                        all_inputs.forEach((input) => {
                            input.disabled = true;
                        });

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
</body>

</html>