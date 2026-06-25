<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../negocio/UsuarioNegocio.php';

$modulos = require __DIR__ . '/../presentacion/includes/modulos.php';

$pagina = $_GET['page'] ?? 'inicio';

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($metodo === 'POST' && isset($_POST['btn_login'])) {

    $auth = new UsuarioNegocio();

    $username = $_POST['user_name'] ?? '';

    $password = $_POST['password'] ?? '';
    
    $resultado = $auth->loguear($username, $password);

    if ($resultado === true) {
        header("Location: index.php?page=inicio");
        exit();
    }

    $error_login = $resultado;
    $pagina = 'login';
}

if ($pagina === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit();
}

if (!isset($_SESSION['usuario_id']) && $pagina !== 'login') {
    header("Location: index.php?page=login");
    exit();
}

if ($pagina === 'usuarios' && ($_SESSION['usuario_rol'] ?? '') !== 'administrador') {
    header("Location: index.php?page=inicio");
    exit();
}

if ($pagina === 'gestion' && ($_SESSION['usuario_rol'] ?? '') === 'usuario') {
    header("Location: index.php?page=inicio");
    exit();
}

if ($pagina === 'libros' && ($_SESSION['usuario_rol'] ?? '') === 'usuario') {
    header("Location: index.php?page=inicio");
    exit();
}

if ($pagina === 'reportes' && ($_SESSION['usuario_rol'] ?? '') === 'usuario') {
    header("Location: index.php?page=inicio");
    exit();
}

if ($pagina === 'libros' && ($_SESSION['usuario_rol'] ?? '') === 'usuario') {
    header("Location: index.php?page=inicio");
    exit();
}

if (isset($_SESSION['usuario_id']) && $pagina === 'login') {
    header("Location: index.php?page=inicio");
    exit();
}

include_once __DIR__ . '/../presentacion/includes/header.php';

if ($pagina === 'login') {

    include_once __DIR__ . '/../presentacion/login.php';

} else {

    echo '<div class="d-flex">';

    include_once __DIR__ . '/../presentacion/includes/navbar.php';

    echo '<main class="flex-grow-1 p-4">';

    if (isset($modulos[$pagina])) {
        $archivoVista = __DIR__ . '/../presentacion/' . $modulos[$pagina]['archivo'];

        if (file_exists($archivoVista)) {
            include_once $archivoVista;
        } else {
            echo "<div class='alert alert-warning'>El archivo del modulo no existe.</div>";
        }
    } else {
        echo "<h1 class='h4'>Pagina no encontrada</h1>";
        echo "<p class='text-muted'>El modulo solicitado no esta registrado.</p>";
    }

    echo '</main>';
    echo '</div>';
}

include_once __DIR__ . '/../presentacion/includes/footer.php';
