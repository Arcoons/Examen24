<?php
// Configuración de conexión
$servername = "localhost"; // Cambia esto si no estás en localhost
$username = "root";        // Tu usuario de MySQL
$password = "";            // La contraseña de tu usuario (dejar vacío si es root sin contraseña)
$database = "examen24";    // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Si se conecta correctamente
// echo "Conexión exitosa"; // Puedes usar esto para pruebas.
?>