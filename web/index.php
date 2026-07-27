<?php
// index.php - Aplicacion web de gestion de usuarios
// Proyecto 3DSM-G2 - Elvis Ragel Toledo Aleman
//
// La contrasena se lee de la variable de entorno DB_PASS para no exponerla
// en el codigo fuente. Configurar en /etc/httpd/conf.d/app.conf con:
//     SetEnv DB_PASS "tu_contrasena"

$conn = new mysqli("localhost", "root", getenv("DB_PASS"), "app_db");

if ($conn->connect_error) {
    die("Error de conexion a la base de datos: " . $conn->connect_error);
}

$mensaje = "";

// Alta de usuarios (requerimiento FASE 1)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");

    if ($nombre !== "" && $correo !== "") {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $correo);
        if ($stmt->execute()) {
            $mensaje = "Usuario registrado correctamente.";
        } else {
            $mensaje = "Error al registrar: el correo ya existe.";
        }
        $stmt->close();
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion de Usuarios - empresa.local</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0f172a; color: #f1f5f9; margin: 0; padding: 40px 20px; }
        .wrap { max-width: 760px; margin: auto; }
        h1 { color: #22c55e; text-align: center; }
        h2 { color: #38bdf8; border-bottom: 1px solid #334155; padding-bottom: 8px; }
        form { background: #1e293b; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        label { display: block; margin: 12px 0 4px; font-weight: bold; }
        input[type=text], input[type=email] { width: 100%; padding: 10px; border: 1px solid #334155;
            border-radius: 6px; background: #0f172a; color: #f1f5f9; box-sizing: border-box; }
        button { margin-top: 16px; padding: 10px 24px; background: #22c55e; color: #0f172a;
            border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #16a34a; }
        table { border-collapse: collapse; width: 100%; background: #1e293b; border-radius: 10px; overflow: hidden; }
        th, td { border-bottom: 1px solid #334155; padding: 12px 16px; text-align: left; }
        th { background: #22c55e; color: #0f172a; }
        .msg { padding: 12px; border-radius: 6px; background: #334155; margin-bottom: 20px; }
        footer { text-align: center; margin-top: 40px; color: #64748b; font-size: 0.85em; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Gestion de Usuarios</h1>

    <?php if ($mensaje !== ""): ?>
        <div class="msg"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <h2>Alta de usuario</h2>
    <form method="post" action="index.php">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" required>

        <button type="submit">Registrar</button>
    </form>

    <h2>Listado de usuarios</h2>
    <table>
        <tr><th>ID</th><th>Nombre</th><th>Correo</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM usuarios ORDER BY id");
        while ($row = $res->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <footer>
        Servidor: www.empresa.local (192.168.0.10) &middot; CentOS Stream 9<br>
        Proyecto 3DSM-G2 &middot; Elvis Ragel Toledo Aleman
    </footer>
</div>
</body>
</html>
<?php $conn->close(); ?>
