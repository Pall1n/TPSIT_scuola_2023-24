<?php
$env = parse_ini_file("../.env");
$host = $env["MYSQL_HOST"];
$user = $env["MYSQL_USER"];
$password = $env["MYSQL_PASS"];
$dbname = "tpsit_15_05_2024";

/*  Collegamento al database, utilizzo una chiocciola per sopprimere i warning ed evitare che vengano 
    stampati a video (il sito è online, quindi potrebbero mostrare informazioni sensibili)  */

try {
    @$conn = new mysqli($host, $user, $password, $dbname);
} catch (Exception $e) {
    die("Errore di connessione al database");
}

if ($conn->connect_error) {
    die("Errore di connessione al database");
}

// Funzione per verificare che tutti gli attributi necessari siano stati passati correttamente nelle varie richieste POST
$error = "";
function verifica_attributi()
{
    if (((isset($_POST["inserisci_libro"]) and isset($_GET["id_casa_editrice"]) and !empty($_GET["id_casa_editrice"])) or (isset($_POST["aggiorna_libro"]) and isset($_POST["id"]) and !empty($_POST["id"]))) and isset($_POST["titolo"]) and isset($_POST["autore"]) and !empty($_POST["titolo"]) and !empty($_POST["autore"])) {
        return true;
    } else if ((isset($_POST["cancella_libro"]) or isset($_POST["inverti_disponibilita"]) or isset($_POST["cancella_casa_editrice"]) or isset($_POST["cancella_autore"])) and isset($_POST["id"]) and !empty($_POST["id"])) {
        return true;
    } else if ((isset($_POST["inserisci_casa_editrice"]) or isset($_POST["aggiorna_casa_editrice"])) and isset($_POST["nome"]) and isset($_POST["sito_web"]) and !empty($_POST["nome"]) and !empty($_POST["sito_web"])) {
        return true;
    } else if ((isset($_POST["inserisci_autore"]) or isset($_POST["aggiorna_autore"])) and isset($_POST["nome"]) and isset($_POST["cognome"]) and !empty($_POST["nome"]) and !empty($_POST["cognome"])) {
        return true;
    }

    global $error;

    // Se l'utente ha cliccato sul pulsante "Inserisci libro" senza che sia stata creata almeno una casa editrice, allora mostro un errore specifico
    if (isset($_POST["inserisci_libro"]) and !isset($_GET["id_casa_editrice"]))
        $error = "Errore: devi creare una casa editrice prima di inserire un libro!";
    // Altrimenti, mostro un errore generico perché l'utente non ha compilato tutti i campi obbligatori (probabilmente modificando il codice HTML)
    else
        $error = "Errore: alcuni campi obbligatori non sono stati compilati";

    return false;
}

// Funzione per verificare che il risultato di una query non sia vuoto
function verifica_risultato_vuoto($num_rows, $tipo)
{
    if ($num_rows >= 1)
        return;
    else if ($tipo == "libro")
        exit("Errore: nessun libro trovato con l'id specificato");
    else if ($tipo == "casa editrice")
        exit("Errore: nessuna casa editrice trovata con l'id specificato");
    else if ($tipo == "autore")
        exit("Errore: nessun autore trovato con l'id specificato");
}

// Funzione per ottenere l'id della casa editrice collegata a un libro specifico
function get_casa_editrice_da_libro($id)
{
    global $conn;

    $select_query = $conn->prepare("SELECT id_casa_editrice FROM libri WHERE id = ?");
    $select_query->bind_param("i", $id);
    $select_query->execute();
    $id_casa_editrice = $select_query->get_result();
    verifica_risultato_vuoto($id_casa_editrice->num_rows, "libro");
    $id_casa_editrice = $id_casa_editrice->fetch_assoc()["id_casa_editrice"];
    $select_query->close();

    return $id_casa_editrice;
}

// Funzione per verificare che un autore esista per mezzo del suo id
function check_autore($id)
{
    global $conn;

    $select_query = $conn->prepare("SELECT 1 FROM autori WHERE id = ?");
    $select_query->bind_param("i", $id);
    $select_query->execute();
    $autore = $select_query->get_result();
    verifica_risultato_vuoto($autore->num_rows, "autore");
    $select_query->close();
}

// Funzione per verificare che una casa editrice esista per mezzo del suo id
function check_casa_editrice($id)
{
    global $conn;

    $select_query = $conn->prepare("SELECT nome, sito_web FROM case_editrici WHERE id = ?");
    $select_query->bind_param("i", $id);
    $select_query->execute();
    $info_casa_editrice = $select_query->get_result();
    verifica_risultato_vuoto($info_casa_editrice->num_rows, "casa editrice");
    $select_query->close();

    return $info_casa_editrice->fetch_assoc();
}

// Funzione per verificare se esistono dei libri collegati ad un autore o ad una casa editrice, in modo da prevenire errori da parte di mysql e garantire l'integrità referenziale
function check_libri_collegati($id, $tipo)
{
    global $conn;

    if ($tipo == "autore") {
        $select_query = $conn->prepare("SELECT 1 FROM libri WHERE id_autore = ?");
    } else if ($tipo == "casa editrice") {
        $select_query = $conn->prepare("SELECT 1 FROM libri WHERE id_casa_editrice = ?");
    }

    $select_query->bind_param("i", $id);
    $select_query->execute();
    $libri_collegati = $select_query->get_result();
    $risultato = $libri_collegati->num_rows > 0;
    $select_query->close();

    return $risultato;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Condizione per verificare se esiste almeno una casa editrice nel database

    /*  Ho implementato questa verifica per permettere alla pagina "create_casa_editrice.html" 
        di essere visualizzata solo se non esiste alcuna casa editrice  */
    if (isset($_GET["esiste_casa_editrice"])) {
        $casa_editrice = $conn->query("SELECT 1 FROM case_editrici");
        $esiste = $casa_editrice->num_rows > 0;
        echo $esiste;
        exit;
    }

    /*  Se l'id della casa editrice non è stato passato come parametro GET, allora seleziono la prima casa editrice presente nel database
        Se non esiste alcuna casa editrice, allora reindirizzo l'utente alla pagina "create_casa_editrice.html" */
    if (isset($_GET["id_casa_editrice"]) and !empty($_GET["id_casa_editrice"])) {
        $id_casa_editrice = $_GET["id_casa_editrice"];
        $info_casa_editrice = check_casa_editrice($id_casa_editrice);
    } else {
        $first_casa_editrice = $conn->query("SELECT id FROM case_editrici LIMIT 1");
        if ($first_casa_editrice->num_rows == 1) {
            $id_casa_editrice = $first_casa_editrice->fetch_assoc()["id"];
            header("Location: index.php?id_casa_editrice=" . $id_casa_editrice);
        }
    }

    // Seleziono tutti i libri collegati alla casa editrice specificata, facendo un join con la tabella autori per ottenere il nome e cognome dell'autore di ogni libro
    if (!empty($id_casa_editrice)) {
        $all_libri_query = "SELECT libri.id, titolo, id_autore, autori.nome as nome_autore, autori.cognome as cognome_autore, disponibile, libri.data_inserimento FROM libri, autori, case_editrici WHERE libri.id_autore = autori.id AND libri.id_casa_editrice = case_editrici.id AND libri.id_casa_editrice = ?";

        $all_libri = $conn->prepare($all_libri_query);
        $all_libri->bind_param("i", $id_casa_editrice);
        $all_libri->execute();
        $all_libri = $all_libri->get_result();
    }

    // Seleziono tutti gli autori e le case editrici presenti nel database, in modo da poterli visualizzare nelle varie select presenti nel form e nelle tabelle apposite
    $all_autori = $conn->query("SELECT * FROM autori");
    $all_case_editrici = $conn->query("SELECT * FROM case_editrici");
} else if ($_SERVER["REQUEST_METHOD"] == "POST" and verifica_attributi()) {
    /* Se la richiesta POST è stata effettuata correttamente (con tutti i dati richiesti), allora permetto 
    l'inserimento, l'aggiornamento o la cancellazione di un libro, autore o casa editrice */

    if (isset($_POST["inserisci_libro"]) or isset($_POST["aggiorna_libro"])) {
        $titolo = $_POST["titolo"];
        $autore = $_POST["autore"];
        $disponibile = true ? isset($_POST["disponibile"]) : false;

        check_autore($autore);

        if (isset($_POST["inserisci_libro"])) {
            $id_casa_editrice = $_GET["id_casa_editrice"];
            check_casa_editrice($id_casa_editrice);

            $insert_query = $conn->prepare("INSERT INTO libri (titolo, id_autore, id_casa_editrice, disponibile) VALUES (?, ?, ?, ?)");
            $insert_query->bind_param("siii", $titolo, $autore, $id_casa_editrice, $disponibile);
            $insert_query->execute();
            $insert_query->close();
        } else {
            $id = $_POST["id"];
            $id_casa_editrice = get_casa_editrice_da_libro($id);

            $update_query = $conn->prepare("UPDATE libri SET titolo = ?, id_autore = ? WHERE id = ?");
            $update_query->bind_param("sii", $titolo, $autore, $id);
            $update_query->execute();
            $update_query->close();
        }

        header("Location: index.php?id_casa_editrice=" . $id_casa_editrice);
    } else if (isset($_POST["inserisci_autore"]) or isset($_POST["aggiorna_autore"])) {
        $nome = $_POST["nome"];
        $cognome = $_POST["cognome"];

        if (isset($_POST["inserisci_autore"])) {
            $insert_query = $conn->prepare("INSERT INTO autori (nome, cognome) VALUES (?, ?)");
            $insert_query->bind_param("ss", $nome, $cognome);
            $insert_query->execute();
            $insert_query->close();
        } else {
            $id = $_POST["id"];
            check_autore($id);

            $update_query = $conn->prepare("UPDATE autori SET nome = ?, cognome = ? WHERE id = ?");
            $update_query->bind_param("ssi", $nome, $cognome, $id);
            $update_query->execute();
            $update_query->close();
        }

        header("Location: index.php");
    } else if (isset($_POST["inserisci_casa_editrice"]) or isset($_POST["aggiorna_casa_editrice"])) {
        $nome = $_POST["nome"];
        $sito_web = $_POST["sito_web"];

        if (isset($_POST["inserisci_casa_editrice"])) {
            $insert_query = $conn->prepare("INSERT INTO case_editrici (nome, sito_web) VALUES (?, ?)");
            $insert_query->bind_param("ss", $nome, $sito_web);

            try {
                $insert_query->execute();
            } catch (Exception $e) {
                exit("Errore: inserimento non riuscito, esiste già una casa editrice con lo stesso nome o sito web");
            }

            $insert_query->close();
        } else {
            $id = $_POST["id"];
            check_casa_editrice($id);

            $update_query = $conn->prepare("UPDATE case_editrici SET nome = ?, sito_web = ? WHERE id = ?");
            $update_query->bind_param("ssi", $nome, $sito_web, $id);

            try {
                $update_query->execute();
            } catch (Exception $e) {
                exit("Errore: aggiornamento non riuscito, esiste già una casa editrice con lo stesso nome o sito web");
            }

            $update_query->close();
        }

        header("Location: index.php");
    } else if (isset($_POST["cancella_libro"])) {
        $id = $_POST["id"];
        $id_casa_editrice = get_casa_editrice_da_libro($id);

        $delete_query = $conn->prepare("DELETE FROM libri WHERE id = ?");
        $delete_query->bind_param("i", $id);
        $delete_query->execute();
        verifica_risultato_vuoto($delete_query->affected_rows, "libro");
        $delete_query->close();

        header("Location: index.php?id_casa_editrice=" . $id_casa_editrice);
    } else if (isset($_POST["cancella_autore"])) {
        $id = $_POST["id"];

        if (check_libri_collegati($id, "autore"))
            exit("Errore: impossibile cancellare l'autore, esistono dei libri collegati ad esso");

        $delete_query = $conn->prepare("DELETE FROM autori WHERE id = ?");
        $delete_query->bind_param("i", $id);
        $delete_query->execute();
        verifica_risultato_vuoto($delete_query->affected_rows, "autore");
        $delete_query->close();

        header("Location: index.php");
    } else if (isset($_POST["cancella_casa_editrice"])) {
        $id = $_POST["id"];

        if (check_libri_collegati($id, "casa editrice"))
            exit("Errore: impossibile cancellare la casa editrice, esistono dei libri collegati ad essa");

        $delete_query = $conn->prepare("DELETE FROM case_editrici WHERE id = ?");
        $delete_query->bind_param("i", $id);
        $delete_query->execute();
        verifica_risultato_vuoto($delete_query->affected_rows, "casa editrice");
        $delete_query->close();

        header("Location: index.php");
    } else if (isset($_POST["inverti_disponibilita"])) {
        $id = $_POST["id"];

        $id_casa_editrice = get_casa_editrice_da_libro($id);

        $update_query = $conn->prepare("UPDATE libri SET disponibile = NOT disponibile WHERE id = ?");
        $update_query->bind_param("i", $id);
        $update_query->execute();
        verifica_risultato_vuoto($update_query->affected_rows, "libro");
        $update_query->close();

        header("Location: index.php?id_casa_editrice=" . $id_casa_editrice);
    } else {
        exit("Errore: richiesta non valida");
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    exit($error);
}

$conn->close();

include("frontend.php");
