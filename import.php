<?php

//DB-Verbindung
include 'db.php';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// JSON-Datei laden
$jsonData = file_get_contents('data.json');
$data = json_decode($jsonData, true);

$inserted = 0;
$skipped = 0;

foreach($data as $item){

    // Pflichtfelder prüfen
    if(empty($item['artikelNr']) || empty($item['beschreibung']) || empty($item['palette']) || !isset($item['kartonsPal'])){
        $skipped++;
        continue;
    }

    // Prüfen, ob Artikel schon existiert
    $check = $conn->prepare("SELECT id FROM artikel WHERE artikel=?");
    $check->bind_param("s", $item['artikelNr']);
    $check->execute();
    $check->store_result();
    if($check->num_rows > 0){
        $skipped++;
        continue;
    }

    // Insert
    $stmt = $conn->prepare("
        INSERT INTO artikel (artikel, beschreibung, palette, kpp, packhoehe, kartonsLage, kartonArt)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $kpp = (int)$item['kartonsPal'];
    $packhoehe = isset($item['packhoehe']) ? (int)$item['packhoehe'] : null;
    $kartonsLage = isset($item['kartonsLage']) ? (int)$item['kartonsLage'] : null;
    $kartonArt = isset($item['kartons']) ? $item['kartons'] : null;

    $stmt->bind_param(
        "sssiiis",
        $item['artikelNr'],
        $item['beschreibung'],
        $item['palette'],
        $kpp,
        $packhoehe,
        $kartonsLage,
        $kartonArt
    );
    $stmt->execute();
    $inserted++;
}

echo "Import abgeschlossen!<br>";
echo "Erfolgreich importiert: $inserted<br>";
echo "Übersprungen (Duplikate oder fehlende Pflichtfelder): $skipped<br>";

$conn->close();
?>
