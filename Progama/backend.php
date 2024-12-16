<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $codigo = $data['codigo'];
    $nombre = $data['nombre'];
    $notas = $data['notas'];

    // Inserta o actualiza las notas
    $stmt = $conn->prepare("INSERT INTO notas (codigo, nombre, nota1, nota2, nota3) 
                            VALUES (?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE 
                            nombre = VALUES(nombre), 
                            nota1 = VALUES(nota1), 
                            nota2 = VALUES(nota2), 
                            nota3 = VALUES(nota3)");
    $stmt->bind_param("sssdd", $codigo, $nombre, $notas[0], $notas[1], $notas[2]);
    $stmt->execute();
    echo json_encode(["status" => "success"]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM notas");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
}

$conn->close();

?>
