<?php

$rutaEnv = __DIR__ . '/../.env';

if (file_exists($rutaEnv)) {
    
    $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lineas as $linea) {
        $linea = trim($linea);

        
        if ($linea === '' || strpos($linea, '#') === 0) continue;

        if (strpos($linea, '=')) {
            list($clave, $valor) = explode('=', $linea, 2);
            
            $_ENV[trim($clave)] = trim($valor);
        }
    }
}

else {
    die("No se encontro el archivo .env");
}