<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//DB-Verbindung
include 'db.php';

// Verbindung herstellen
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST-Daten abholen
$artikel = $_POST['Artikel'] ?? null;
$beschreibung = $_POST['Beschreibung'] ?? null;
$palette = $_POST['palette'] ?? null;
$kpp = $_POST['kpp'] ?? null;
$packhoehe = $_POST['packhoehe'] ?? null;
$kartonsLage = $_POST['kartonsLage'] ?? null;
$kartonArt = $_POST['kartonArt'] ?? null;

// Pflichtfelder prüfen
if (!$artikel || !$beschreibung || !$palette || !$kpp) {
    echo "Fehler: Pflichtfelder fehlen!";
    exit;
}

// Prüfen, ob Artikelnummer bereits existiert
$stmtCheck = $conn->prepare("SELECT id FROM artikel WHERE artikel = ?");
$stmtCheck->bind_param("s", $artikel);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    echo "Fehler: Artikelnummer existiert bereits!";
    $stmtCheck->close();
    $conn->close();
    exit;
}
$stmtCheck->close();

// INSERT vorbereiten
$stmt = $conn->prepare("
  INSERT INTO artikel (artikel, beschreibung, palette, kpp, packhoehe, kartonsLage, kartonArt)
  VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sssiiis", $artikel, $beschreibung, $palette, $kpp, $packhoehe, $kartonsLage, $kartonArt);

// Ausführen
if ($stmt->execute()) {
    echo "success";
} else {
    echo "Fehler beim Speichern: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
