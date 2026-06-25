<?php
require_once __DIR__ . '/../negocio/ReporteNegocio.php';

$reporteNegocio = new ReporteNegocio();

// Control de navegación: por defecto muestra 'prestamos' si no hay nada en la URL
$tipo_reporte = $_GET['tipo'] ?? 'prestamos';

if (!function_exists('texto')) {
    function texto($valor) { return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8'); }
}

// Dependiendo de lo que elija, cargamos la info para no saturar la base de datos
if ($tipo_reporte === 'prestamos') {
    $prestamosPendientes = $reporteNegocio->obtenerPrestamosPendientes();
    $prestamosDevueltos  = $reporteNegocio->obtenerPrestamosDevueltos();
} else {
    $multas = $reporteNegocio->obtenerMultas();
}
?>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0"><i class="bi bi-file-earmark-bar-graph"></i> Reportes Generales</h1>
    </div>

    <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm">
        <li class="nav-item">
            <a class="nav-link <?= $tipo_reporte === 'prestamos' ? 'active bg-danger' : 'text-dark' ?> fw-bold" href="index.php?page=reportes&tipo=prestamos">
                <i class="bi bi-book"></i> Reporte de Préstamos
            </a>
        </li>
        <li class="nav-item ms-2">
            <a class="nav-link <?= $tipo_reporte === 'multas' ? 'active bg-danger' : 'text-dark' ?> fw-bold" href="index.php?page=reportes&tipo=multas">
                <i class="bi bi-cash-coin"></i> Reporte de Multas
            </a>
        </li>
    </ul>

    <?php if ($tipo_reporte === 'prestamos'): ?>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div style="background-color: #470000;" class="card-header  text-white fw-bold">
                        <i class="bi bi-hourglass-split"></i> Préstamos Pendientes
                        <span class="badge bg-dark float-end"><?= count($prestamosPendientes) ?></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Libro</th>
                                        <th>Estudiante</th>
                                        <th>F. Límite</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($prestamosPendientes)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-3">No hay libros pendientes de devolución.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($prestamosPendientes as $p): ?>
                                            <tr>
                                                <td class="ps-3 fw-bold">#<?= $p['id'] ?></td>
                                                <td class="small fw-semibold"><?= texto($p['titulo']) ?></td>
                                                <td class="small"><?= texto($p['nombre']) ?></td>
                                                <td class="small text-danger fw-bold"><?= date('d/m/Y', strtotime($p['fecha_limite'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div  style="background-color: #470000;" class="card-header text-white fw-bold">
                        <i class="bi bi-check-circle"></i> Historial de Devueltos
                        <span class="badge bg-white text-success float-end"><?= count($prestamosDevueltos) ?></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Libro</th>
                                        <th>Estudiante</th>
                                        <th>F. Devolución</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($prestamosDevueltos)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-3">No hay devoluciones registradas.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($prestamosDevueltos as $p): ?>
                                            <tr>
                                                <td class="ps-3 fw-bold">#<?= $p['id'] ?></td>
                                                <td class="small fw-semibold"><?= texto($p['titulo']) ?></td>
                                                <td class="small"><?= texto($p['nombre']) ?></td>
                                                <td class="small text-success fw-bold"><?= date('d/m/Y', strtotime($p['fecha_devolucion'])) ?></td>
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

    <?php elseif ($tipo_reporte === 'multas'): ?>
        <div class="card border-0 shadow-sm">
            <div  style="background-color: #470000;" class="card-header text-white fw-bold">
                <i class="bi bi-wallet2"></i> Registro de Multas Cobradas
                <span class="badge bg-white text-danger float-end">Total: <?= count($multas) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Ticket</th>
                                <th>Libro Atrasado</th>
                                <th>Estudiante</th>
                                <th>Motivo</th>
                                <th>F. Pago</th>
                                <th class="text-end pe-3">Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($multas)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Excelente, no hay multas registradas en el sistema.</td></tr>
                            <?php else: 
                                $totalAcumulado = 0;
                            ?>
                                <?php foreach($multas as $m): 
                                    $totalAcumulado += $m['monto'];
                                ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">#<?= $m['id'] ?></td>
                                        <td class="small fw-semibold"><?= texto($m['titulo']) ?></td>
                                        <td class="small"><?= texto($m['nombre']) ?></td>
                                        <td class="small text-muted"><?= texto($m['motivo']) ?></td>
                                        <td class="small"><?= date('d/m/Y', strtotime($m['fecha_pago'])) ?></td>
                                        <td class="text-end pe-3 text-dark fw-bold">$<?= number_format($m['monto'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr  style="background-color: #470000;" class="table">
                                    <td colspan="5" class="text-end fw-bold">TOTAL RECAUDADO:</td>
                                    <td class="text-end pe-3 text-success fw-bold fs-5">$<?= number_format($totalAcumulado, 2) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>