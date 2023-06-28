<?php

// Dirección IP o nombre de dominio del servidor NTP
$servidorNTP = '192.168.1.8';

// Puerto NTP
$puertoNTP = 123;

// Función para refrescar la página cada segundo
function refrescarPagina() {
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
$resultado = socket_recv($socket, $respuestaNTP, 48, 0);

if ($resultado === false) {
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

// Genera el HTML para mostrar la hora actual
$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Hora del servidor NTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
        }

        h1 {
            font-size: 140px;
        }

        h3 {
            font-size: 60px;
        }

        p {
            font-size: 80px;
        }

    </style>
</head>
<body>
    <h1>Hora del servidor NTP</h1>

    <p>{$horaActual}</p>

    <h3>Creado por Yeison Ramirez</h3>
</body>
</html>
HTML;

// Imprime el HTML y refresca la página cada segundo
echo $html;
refrescarPagina();
?>
