<?php

require_once __DIR__ . '/../negocio/PrestamoNegocio.php';

$prestamoNegocio = new PrestamoNegocio();

$mensaje = '';
$tipoMensaje = 'success';

if (!isset($_SESSION['prestamo_carrito'])) {
    $_SESSION['prestamo_carrito'] = [];
}

$busqueda = $_GET['buscar'] ?? '';
$libro_seleccionado_id = intval($_GET['libro_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['quitar_copia_id'])) {
        $id_quitar = intval($_POST['quitar_copia_id']);
        if (($key = array_search($id_quitar, $_SESSION['prestamo_carrito'])) !== false) {
            unset($_SESSION['prestamo_carrito'][$key]);
            $_SESSION['prestamo_carrito'] = array_values($_SESSION['prestamo_carrito']);
        }
    } else {
        
        $resultado = $prestamoNegocio->procesarPrestamo($_POST, $_SESSION['prestamo_carrito']);
        if ($resultado !== true && $resultado !== false) {
            $mensaje = $resultado;
            $tipoMensaje = 'danger';
        } elseif ($resultado === true && ($_POST['accion_prestamo'] ?? '') === 'guardar_prestamo') {
            $mensaje = "¡Préstamo registrado correctamente!";
            $tipoMensaje = 'success';
            $libro_seleccionado_id = 0; 
        }
    }
}

$estudiantes = $prestamoNegocio->obtenerEstudiantes();
$libros = $prestamoNegocio->buscarLibros($busqueda);

$copias = [];
$titulo_libro_actual = "Ninguno";

if ($libro_seleccionado_id > 0) {
    $copias = $prestamoNegocio->listarCopiasPorLibro($libro_seleccionado_id);
    foreach ($libros as $l) {
        if ($l['id'] == $libro_seleccionado_id) {
            $titulo_libro_actual = $l['titulo'];
            break;
        }
    }
}

if (!function_exists('texto')) {
    function texto($valor) { return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8'); }
}
?>

<div class="container-fluid">
    
    <?php if ($mensaje !== ''): ?>
        <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
            <?= texto($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        
        <div class="col-md-8">
            
            <div class="card mb-4 border-0 shadow-sm">
                <div style="background-color: #470000;" class="card-header text-white py-3">
                    <h6 class="card-title mb-0"><i class="bi bi-journal-bookmark-fill"></i> Registrar Nuevo Préstamo</h6>
                </div>
                <div class="card-body bg-white">
                    <form id="formRegistrarPrestamo" action="index.php?page=prestamos&buscar=<?= urlencode($busqueda) ?>&libro_id=<?= $libro_seleccionado_id ?>" method="POST">
                        <input type="hidden" name="accion_prestamo" value="guardar_prestamo">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">Seleccionar Estudiante</label>
                                <select style="border-color: #470000;" name="estudiante_id" class="form-select form-select-sm" required>
                                    <option value="">-- Seleccione --</option>
                                    <?php foreach ($estudiantes as $est): ?>
                                        <option value="<?= $est['id'] ?>"><?= texto($est['nombre']) ?> (<?= texto($est['carnet_estudiante']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-secondary">Fecha Préstamo</label>
                                <input style="border-color: #470000;" type="date" name="fecha_prestamo" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-secondary">Fecha Límite</label>
                                <input style="border-color: #470000;" type="date" name="fecha_limite" class="form-control form-control-sm" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body bg-white">
                            <h6  class="card-title small fw-bold text-dark mb-3"><i class="bi bi-search"></i> 1. Buscar Libro</h6>
                            
                            <form action="index.php" method="GET" class="input-group input-group-sm mb-3">
                                <input type="hidden" name="page" value="prestamos">
                                <input style="border-color: #470000;" type="text" name="buscar" class="form-control" placeholder="Escriba el título y presione Enter..." value="<?= texto($busqueda) ?>">
            
                            </form>

                            <div class="table-responsive" style="max-height: 240px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle small">
                                    <tbody>
                                        <?php if (empty($libros)): ?>
                                            <tr><td class="text-muted text-center py-3">No se encontraron libros.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($libros as $libro): ?>
                                                <tr class="<?= ($libro['id'] == $libro_seleccionado_id) ? 'table-primary' : '' ?>">
                                                    <td class="d-flex justify-content-between align-items-center py-2">
                                                        <span class="fw-semibold"><?= texto($libro['titulo']) ?></span>
                                                        <a href="index.php?page=prestamos&buscar=<?= urlencode($busqueda) ?>&libro_id=<?= $libro['id'] ?>" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.75rem;">
                                                            Copias <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body bg-white">
                            <h6 class="card-title small fw-bold text-dark mb-3"><i class="bi bi-layers"></i> 2. Copias: <span class="text-primary"><?= texto($titulo_libro_actual) ?></span></h6>
                            
                            <div class="table-responsive" style="max-height: 290px; overflow-y: auto;">
                                <table class="table table-sm align-middle small">
                                    <thead style="border-color: #470000;"  class="table-light">
                                        <tr>
                                            <th>Estado</th>
                                            <th>Ubicación</th>
                                            <th class="text-end">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($libro_seleccionado_id <= 0): ?>
                                            <tr><td colspan="3" class="text-muted text-center py-3">Seleccione un libro de la lista.</td></tr>
                                        <?php elseif (empty($copias)): ?>
                                            <tr><td colspan="3" class="text-muted text-center py-3">No hay copias registradas.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($copias as $copia): 
                                                $en_carrito = in_array($copia['id'], $_SESSION['prestamo_carrito']);
                                                $disponible = ($copia['estado'] === 'disponible' && !$en_carrito);
                                                $badgeColor = ($copia['estado'] === 'disponible') ? 'success' : 'danger';
                                            ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-<?= $en_carrito ? 'warning text-dark' : $badgeColor ?>" style="font-size: 0.7rem;">
                                                            <?= $en_carrito ? 'En Carrito' : ucfirst($copia['estado']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= texto($copia['ubicacion'] ?? 'No asignada') ?></td>
                                                    <td class="text-end">
                                                        <form action="index.php?page=prestamos&buscar=<?= urlencode($busqueda) ?>&libro_id=<?= $libro_seleccionado_id ?>" method="POST">
                                                            <input type="hidden" name="accion_prestamo" value="grid_agregar"> <input type="hidden" name="accion_prestamo" value="agregar_item">
                                                            <input type="hidden" name="copia_id" value="<?= $copia['id'] ?>">
                                                            <button type="submit" class="btn btn-xs btn-success py-0 px-1" style="font-size: 0.75rem;" <?= !$disponible ? 'disabled' : '' ?>>
                                                                + Agregar
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div style="background-color: #470000;" class="card-header text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0"><i class="bi bi-cart4"></i> Lista de Préstamo</h6>
                    <span class="badge bg-white text-primary rounded-pill small fw-bold"><?= count($_SESSION['prestamo_carrito']) ?></span>
                </div>
                <div class="card-body bg-white d-flex flex-column justify-content-between" style="min-height: 380px;">
                    
                    <div class="table-responsive">
                        <table class="table table-sm align-middle small">
                            <thead style="border-color: #470000;" class="table-light">
                                <tr>
                                    <th>Libro / Copia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($_SESSION['prestamo_carrito'])): ?>
                                    <tr><td colspan="2" class="text-muted text-center py-4">No hay elementos agregados.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($_SESSION['prestamo_carrito'] as $id_copia_c): 
                                        $det = $prestamoNegocio->obtenerDetalleCopia($id_copia_c);
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark"><?= texto($det['titulo'] ?? 'Desconocido') ?></span>
                                                <br><small class="text-muted">Copia ID: #<?= $id_copia_c ?></small>
                                            </td>
                                            <td class="text-end">
                                                <form action="index.php?page=prestamos&buscar=<?= urlencode($busqueda) ?>&libro_id=<?= $libro_seleccionado_id ?>" method="POST">
                                                    <input type="hidden" name="quitar_copia_id" value="<?= $id_copia_c ?>">
                                                    <button type="submit" class="btn btn-sm text-danger p-0 border-0 bg-transparent">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-grid mt-3">
                        <button style="background-color: #ec3030ff; type="submit" form="formRegistrarPrestamo" class="btn btn-sm py-2 text-white fw-bold" <?= empty($_SESSION['prestamo_carrito']) ? 'disabled' : '' ?>>
                            <i  class="bi bi-check-circle-fill"></i> Confirmar Préstamo
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>