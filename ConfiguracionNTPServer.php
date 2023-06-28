<?php

//Nota si quieres consumir otro servidor NTP, configura $servidorNT y date_default_timezone_set('EL que quieras');
/*
"UTC": Para la Coordinated Universal Time (Tiempo Universal Coordinado).
"Europe/Paris": Para la zona horaria de París en Europa.
"Asia/Shanghai": Para la zona horaria de Shanghái en Asia.
"Australia/Sydney": Para la zona horaria de Sídney en Australia.
"Africa/Johannesburg": Para la zona horaria de Johannesburgo en África.
"America/New_York": Para la zona horaria de Nueva York en América del Este.
"Europe/London": Para la zona horaria de Londres en Europa.
"Asia/Tokyo": Para la zona horaria de Tokio en Asia.
America/Bogota: Para la zona horaria de Bogota en America.
*/

// Dirección IP o nombre de dominio del servidor NTP
$servidorNTP = '0.south-america.pool.ntp.org';

// Puerto NTP
$puertoNTP = 123;

// Función para refrescar la página cada segundo
function refrescarPagina()
{
    header("Refresh: 1");
}

// Crea un socket UDP
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

if ($socket === false) {
    // Error al crear el socket
    die("Error al crear el socket UDP");
}

// Define la dirección IP y el puerto del servidor NTP
$servidorNTP_IP = gethostbyname($servidorNTP);
$servidorNTP_direccion = sprintf("%s:%d", $servidorNTP_IP, $puertoNTP);

// Construye la solicitud NTP manualmente
$solicitudNTP = "\x1B" . str_repeat("\x0", 47);

// Envía la solicitud al servidor NTP
$resultado = socket_sendto($socket, $solicitudNTP, strlen($solicitudNTP), 0, $servidorNTP_IP, $puertoNTP);

if ($resultado === false) {
    // Error al enviar la solicitud
    die("Error al enviar la solicitud NTP");
}

// Espera la respuesta del servidor NTP (timeout de 5 segundos)
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 5, 'usec' => 0));

// Recibe la respuesta del servidor NTP
$respuestaNTP = '';

try {
    $resultado = socket_recv($socket, $respuestaNTP, 48, 0);
} catch (Exception $e) {
    // Error al recibir la respuesta
    die("Error al recibir la respuesta NTP");
}

// Cierra el socket
socket_close($socket);

// Configura la zona horaria a "America/Bogota"
date_default_timezone_set('America/Bogota');

// Procesa la respuesta para obtener la hora
$horaActual = '';

if ($respuestaNTP !== '') {
    // El formato de respuesta NTP es un timestamp de 64 bits que consta de 2 partes: segundos y fracciones de segundos
    $ntpTimestamp = unpack('N12', $respuestaNTP);

    // Obtiene los segundos desde 1900 (epoch de NTP)
    $segundos = $ntpTimestamp[9] - 2208988800;

    // Convierte los segundos a una fecha y hora legible
    $horaActual = date('Y-m-d H:i:s', $segundos);
}

try {
    refrescarPagina();
} catch (Exception $e) {
    // Manejo del error al refrescar la página
    echo "Error al refrescar la página";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Hora del servidor NTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css">
    <style>
        /* Estilos CSS */

        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
            background-color: #F2F2F2;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .display-1 {
            font-size: 60px;
            color: #FF4500;
            margin-bottom: 30px;
        }

        .footer-text {
            font-size: 24px;
            color: #000000;
            margin-top: 50px;
        }

        .widget {
            background-color: #FFA500;
            color: #FFFFFF;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            width: 100%;
        }

        .widget-heading {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .widget-content {
            font-size: 24px;
        }

        .system-image {
            width: 200px;
            height: auto;
            margin-left: 50px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body>
    <div class="container">
        <div>
            <h1 class="display-1">Hora del servidor NTP</h1>

            <div class="widget">
            </div>
            <img src="https://th.bing.com/th/id/OIP.7djNy7VW1M4gDVAXskfBMgHaD5?pid=ImgDet&rs=1" alt="Imagen del sistema">
            <div class="widget">
            </div>

            <p style="font-size: 25px; color: #FF4500;">Fecha y hora: <span style="font-size: 25px; color: #000000;"><?php echo $horaActual; ?></span></p>
            <!-- <p style="font-size: 25px; color: #FF4500;">Hora actual del sistema: <span style="font-size: 25px; color: #000000;"><?php echo date('Y-m-d H:i:s'); ?></span></p> -->
        </div>
        
        <div class="footer">
            <p class="footer-text">® Creado por Yeison Ramirez</p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>