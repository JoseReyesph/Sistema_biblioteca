<?php
require_once __DIR__ . '/../negocio/EstudianteNegocio.php';

$estudianteNegocio = new EstudianteNegocio();

$mensaje = '';
$tipoMensaje = 'success';
$estudianteEditar = null;
$busqueda = $_GET['buscar'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($metodo === 'POST') {
    if (isset($_POST['guardar_estudiante'])) {
        $resultado = $estudianteNegocio->guardarEstudiante($_POST);

        if ($resultado === true) {
            $mensaje = 'Estudiante guardado correctamente.';
        } else {
            $mensaje = $resultado;
            $tipoMensaje = 'danger';
        }
    }

    if (isset($_POST['eliminar_estudiante'])) {
        $resultado = $estudianteNegocio->eliminarEstudiante($_POST['id'] ?? 0);

        if ($resultado === true) {
            $mensaje = 'Estudiante eliminado correctamente.';
        } else {
            $mensaje = $resultado;
            $tipoMensaje = 'danger';
        }
    }
}

if (isset($_GET['editar'])) {
    $estudianteEditar = $estudianteNegocio->obtenerEstudiantePorId($_GET['editar']);
}

$estudiantes = $estudianteNegocio->listarEstudiantes($busqueda);

if (!function_exists('texto')) {
    function texto($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Estudiantes</h1>
    </div>
</div>

<?php if ($mensaje !== ''): ?>
    <div class="alert alert-<?php echo $tipoMensaje; ?>">
        <?php echo texto($mensaje); ?>
    </div>
<?php endif; ?>

<div class="card mb-5" style="border-color: #470000;">
    <div class="card-header" style="background-color: #470000; color: white;">
        <?php echo $estudianteEditar ? 'Editar estudiante' : 'Nuevo estudiante'; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="index.php?page=estudiantes">
            <input type="hidden" name="id" value="<?php echo texto($estudianteEditar['id'] ?? ''); ?>">

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Carnet</label>
                    <input style="border-color: #470000" type="text" name="carnet_estudiante" class="form-control" value="<?php echo texto($estudianteEditar['carnet_estudiante'] ?? ''); ?>" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input style="border-color: #470000" type="text" name="nombre" class="form-control" value="<?php echo texto($estudianteEditar['nombre'] ?? ''); ?>" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input style="border-color: #470000" type="text" name="telefono" class="form-control" value="<?php echo texto($estudianteEditar['telefono'] ?? ''); ?>" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input style="border-color: #470000" type="email" name="email" class="form-control" value="<?php echo texto($estudianteEditar['email'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-end">
                    <?php if ($estudianteEditar): ?>
                        <a href="index.php?page=estudiantes" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                    <button type="submit" name="guardar_estudiante" class="btn" style="background-color: #ec3030ff; color: white;">
                        Guardar Estudiante
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card" style="border-color: #470000;">
    <div class="card-header" style="background-color: #470000; color: white;">
        Listado de estudiantes
    </div>

    <div class="card-body">
        <form method="GET" action="index.php" class="row mb-3">
            <input type="hidden" name="page" value="estudiantes">

            <div class="col-md-8">
                <input style="border-color: #470000;" type="text" name="buscar" class="form-control" placeholder="Buscar por carnet, nombre o correo" value="<?php echo texto($busqueda); ?>">
            </div>

            <div class="col-md-4 mt-2 mt-md-0">
                <button type="submit" class="btn btn-outline-primary" style="background-color: #ec3030ff; color: white; border-color: #ff4848ff;">Buscar</button>
                <a href="index.php?page=estudiantes" class="btn btn-outline-secondary" style="background-color: #ec3030ff; color: white; border-color: #ff4848ff;">Limpiar</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Carnet</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($estudiantes) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay estudiantes registrados.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($estudiantes as $est) : ?>
                        <tr>
                            <td><?php echo texto($est['id']); ?></td>
                            <td><?php echo texto($est['carnet_estudiante']); ?></td>
                            <td><?php echo texto($est['nombre']); ?></td>
                            <td><?php echo texto($est['telefono']); ?></td>
                            <td><?php echo texto($est['email']); ?></td>
                            <td><?php echo texto($est['created_at']); ?></td>
                            <td>
                                <a href="index.php?page=estudiantes&editar=<?php echo $est['id']; ?>" class="btn btn-sm" style="background-color: #2c44dbff; color: white; border-color: #2c44dbff;">
                                    Editar
                                </a>

                                <form method="POST" action="index.php?page=estudiantes" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este estudiante?');">
                                    <input type="hidden" name="id" value="<?php echo $est['id']; ?>">
                                    <button type="submit" name="eliminar_estudiante" class="btn btn-sm btn-danger">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>