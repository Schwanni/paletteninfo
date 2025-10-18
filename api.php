<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

//DB-Verbindung
include 'db.php';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "DB Connection failed: ".$conn->connect_error]));
}

// Alle Einträge abrufen
$result = $conn->query("SELECT * FROM artikel ORDER BY erstellt_am DESC");

$rows = [];
while($row = $result->fetch_assoc()) {
    $rows[] = [
    "id" => $row['id'],           // <- MUSS da sein!
    "artikelNr" => $row['artikel'],
    "beschreibung" => $row['beschreibung'],
    "palette" => $row['palette'],
    "kpp" => $row['kpp'],
    "packhoehe" => $row['packhoehe'],
    "kartonsLage" => $row['kartonsLage'],
    "kartonArt" => $row['kartonArt']
];
}

echo json_encode($rows);
$conn->close();
?>
