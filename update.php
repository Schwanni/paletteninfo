<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

//DB-Verbindung
include 'db.php';

$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die(json_encode(["error"=>$conn->connect_error]));

$stmt = $conn->prepare("
    UPDATE artikel 
    SET artikel=?, beschreibung=?, palette=?, kpp=?, packhoehe=?, kartonsLage=?, kartonArt=? 
    WHERE id=?
");
$stmt->bind_param(
    "sssiiisi",
    $data['artikel'],
    $data['beschreibung'],
    $data['palette'],
    $data['kpp'],
    $data['packhoehe'],
    $data['kartonsLage'],
    $data['kartonArt'],
    $data['id']
);

$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(["success"=>true]);
?>