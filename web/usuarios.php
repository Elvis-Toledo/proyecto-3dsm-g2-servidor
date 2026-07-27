<?php
// usuarios.php - Consulta y muestra los usuarios de la tabla app_db.usuarios
// Proyecto 3DSM-G2 - Elvis

// La contrasena se lee de la variable de entorno DB_PASS para no exponerla en el codigo.
// Configurar en /etc/php-fpm.d/www.conf con:  env[DB_PASS] = tu_contrasena
$db_pass = getenv("DB_PASS");
if ($db_pass === false || $db_pass === "") {
    $db_pass = $_SERVER["DB_PASS"] ?? "";
}

$conn = new mysqli("localhost", "root", $db_pass, "app_db");
if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

echo "<!DOCTYPE html><html><head><title>Usuarios</title><style>
body { font-family: Arial; background: #0f172a; color: #f1f5f9; margin: 40px; }
h1 { color: #22c55e; }
table { border-collapse: collapse; max-width: 700px; margin: auto; background: #1e293b; }
th, td { border: 1px solid #334155; padding: 12px 20px; text-align: left; }
th { background: #22c55e; color: #0f172a; }
</style></head><body>";

echo "<h1 style='text-align:center'>Usuarios en la Base de Datos</h1>";
echo "<table><tr><th>ID</th><th>Nombre</th><th>Correo</th></tr>";

$res = $conn->query("SELECT * FROM usuarios");
while ($row = $res->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['nombre']}</td><td>{$row['correo']}</td></tr>";
}

echo "</table></body></html>";
$conn->close();
?>
