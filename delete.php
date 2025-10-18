<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

//DB-Verbindung
include 'db.php';

$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die(json_encode(["error"=>$conn->connect_error]));

$stmt = $conn->prepare("DELETE FROM artikel WHERE id=?");
$stmt->bind_param("i",$data['id']);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(["success"=>true]);
?>
