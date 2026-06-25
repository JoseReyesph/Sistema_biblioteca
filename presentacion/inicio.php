<h1 class="h4 mb-3">Inicio</h1>

<p class="text-muted">
    Bienvenido, <strong><?php echo $_SESSION['usuario_nom']; ?></strong>.
</p>

<?php
require_once __DIR__ . '/../data/InicioDatos.php';

$dash = new DashboardDatos();
$topPrestamos = $dash->obtenerTopLibros('prestamos');
$topCalificados = $dash->obtenerTopLibros('calificacion');
?>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div style="background-color: #470000;" class="card-header text-white">Top 5: Libros más solicitados</div>
            <ul class="list-group list-group-flush">
                <?php foreach($topPrestamos as $l): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <?= htmlspecialchars($l['titulo']) ?>
                    <span class="badge bg-primary rounded-pill"><?= $l['valor'] ?> préstamos</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div style="background-color: #470000;" class="card-header text-white">Top 5: Mejores calificaciones</div>
            <ul class="list-group list-group-flush">
                <?php foreach($topCalificados as $l): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <?= htmlspecialchars($l['titulo']) ?>
                    <span class="badge bg-success rounded-pill"><?= $l['valor'] ?> estrellas</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>