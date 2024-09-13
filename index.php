<?php
// Se conecta a la base de datos de control
$servername_control = "localhost";
$dBUsername_control = "id22114529_thegripisrolling";
$dBPassword_control = "KLMklm4805!";
$dBName_control = "id22114529_for_grip";
$conn_control = mysqli_connect($servername_control, $dBUsername_control, $dBPassword_control, $dBName_control);

if (!$conn_control) {
    die("Connection failed: " . mysqli_connect_error());
}

// Se conecta a la base de datos del sensor de temperatura
$servername_sensor = "localhost";
$dBUsername_sensor = "id22114529_richardatomiccompany";
$dBPassword_sensor = "KLMklm4805!";
$dBName_sensor = "id22114529_dht22database";
$conn_sensor = mysqli_connect($servername_sensor, $dBUsername_sensor, $dBPassword_sensor, $dBName_sensor);

if (!$conn_sensor) {
    die("Connection failed: " . mysqli_connect_error());
}

// Función para alternar el estado
function toggleStatus($conn, $name) {
    $sql = "SELECT status FROM control_status WHERE name = '$name'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $current_status = $row['status'];
        $new_status = $current_status == 1 ? 0 : 1;
        mysqli_query($conn, "UPDATE control_status SET status = $new_status WHERE name = '$name'");
    }
}

// Acciones de los botones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['avanzar'])) {
        toggleStatus($conn_control, 'motor_advance');
    } elseif (isset($_POST['izquierda'])) {
        toggleStatus($conn_control, 'servo0_left');
    } elseif (isset($_POST['derecha'])) {
        toggleStatus($conn_control, 'servo0_right');
    } elseif (isset($_POST['ir_atras'])) {
        toggleStatus($conn_control, 'motor_backward');
    } elseif (isset($_POST['grip_eje_derecha'])) {
        toggleStatus($conn_control, 'servo1_right');
    } elseif (isset($_POST['grip_eje_izquierda'])) {
        toggleStatus($conn_control, 'servo1_left');
    } elseif (isset($_POST['grip_horizontal_abajo'])) {
        toggleStatus($conn_control, 'servo2_down');
    } elseif (isset($_POST['grip_horizontal_arriba'])) {
        toggleStatus($conn_control, 'servo2_up');
    } elseif (isset($_POST['grip_cerrar'])) {
        toggleStatus($conn_control, 'servo3_close');
    } elseif (isset($_POST['grip_abrir'])) {
        toggleStatus($conn_control, 'servo3_open');
    }
}

// Lee los datos del DHT22
$sql_sensor = "SELECT * FROM dht22_readings ORDER BY timestamp DESC LIMIT 1";
$result_sensor = mysqli_query($conn_sensor, $sql_sensor);
$temperature = "N/A";
$humidity = "N/A";

if ($result_sensor && mysqli_num_rows($result_sensor) > 0) {
    $row_sensor = mysqli_fetch_assoc($result_sensor);
    $temperature = $row_sensor['temperature'];
    $humidity = $row_sensor['humidity'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Panel</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #d0e1f9; /* Gris-azul claro */
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #005b96; /* Barra azul */
            padding: 10px;
            text-align: center;
            color: white;
            position: relative;
        }

        .header img {
            position: absolute;
            left: 10px;
            top: 5px;
            width: 50px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .form-advance {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #ff4c4c;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }

        input[type="submit"]:hover {
            background-color: #e60000; /* Rojo oscuro */
        }

        input[type="submit"]:active {
            transform: scale(0.98);
        }

        .sensor-data {
            text-align: center;
            margin-top: 30px;
        }

        .sensor-data h2 {
            margin: 10px;
        }
    </style>
    <script>
        function fetchSensorData() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_sensor_data.php', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    const data = JSON.parse(this.responseText);
                    document.getElementById('temperature').innerText = data.temperature + ' °C';
                    document.getElementById('humidity').innerText = data.humidity + ' %';
                }
            };
            xhr.send();
        }

        setInterval(fetchSensorData, 5000); // Actualiza cada 5 segundos
    </script>
</head>
<body>
    <div class="header">
        <img src="richardatomiccompany.png" alt="Logo">
        <h1>Richard & Atomic Company</h1>
    </div>

    <div class="button-container">
        <div class="form-advance">
            <form method="post">
                <input type="submit" name="avanzar" value="Avanzar">
            </form>
        </div>
        <form method="post">
            <input type="submit" name="izquierda" value="Izquierda">
            <input type="submit" name="ir_atras" value="Ir hacia atrás">
            <input type="submit" name="derecha" value="Derecha">

            <input type="submit" name="grip_eje_izquierda" value="Grip Eje Izquierda">
            <input type="submit" name="grip_eje_derecha" value="Grip Eje Derecha">

            <input type="submit" name="grip_horizontal_abajo" value="Grip Horiz. Abajo">
            <input type="submit" name="grip_horizontal_arriba" value="Grip Horiz. Arriba">

            <input type="submit" name="grip_cerrar" value="Grip Cerrar">
            <input type="submit" name="grip_abrir" value="Grip Abrir">
        </form>
    </div>

    <div class="sensor-data">
        <h2>Temperatura: <span id="temperature"><?php echo $temperature; ?></span> °C</h2>
        <h2>Humedad: <span id="humidity"><?php echo $humidity; ?></span> %</h2>
    </div>
</body>
</html>
