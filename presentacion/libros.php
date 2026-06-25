<?php

require_once __DIR__ . '/../negocio/LibroNegocio.php';

$libroNegocio = new LibroNegocio();
$mensaje = '';
$tipoMensaje = 'success';

$libroEditar = null;
$libroSeleccionadoCopias = null;
$copias = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_libro'])) {
        $res = $libroNegocio->guardarLibro($_POST);
        if ($res === true) $mensaje = "Libro procesado correctamente.";
        else { $mensaje = $res; $tipoMensaje = 'danger'; }
    }
    if (isset($_POST['action_copia'])) {
        $res = $libroNegocio->guardarCopia($_POST);
        if ($res === true) {
            $mensaje = "Copia añadida con éxito.";
            $_GET['libro_copias'] = $_POST['libro_id'];
        } else { $mensaje = $res; $tipoMensaje = 'danger'; }
    }
}


if (isset($_GET['editar'])) {
    $libroEditar = $libroNegocio->obtenerLibroPorId($_GET['editar']);
}
if (isset($_GET['libro_copias'])) {
    $libroSeleccionadoCopias = $libroNegocio->obtenerLibroPorId($_GET['libro_copias']);
    if ($libroSeleccionadoCopias) {
        $copias = $libroNegocio->listarCopias($_GET['libro_copias']);
    }
}


$libros = $libroNegocio->listarLibros();
$autores = $libroNegocio->buscarAutores();
$editoriales = $libroNegocio->buscarEditoriales();
$categorias = $libroNegocio->buscarCategorias();

if (!function_exists('texto')) {
    function texto($valor) { return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8'); }
}
?>

<div class="mb-3">
    <h1 class="h4">Catálogo e Inventario de Libros</h1>
</div>

<?php if ($mensaje !== ''): ?>
    <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show"><?php echo texto($mensaje); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div style="border-color: #470000; background-color: #470000" class="card-header text-white fw-bold">Modificar Libro/Agregar Nuevo Libro</div>
            <div class="card-body">
                <form method="POST" action="index.php?page=libros">
                    <input type="hidden" name="action_libro" value="1">
                    <input type="hidden" name="id" value="<?php echo texto($libroEditar['id'] ?? ''); ?>">

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label mb-1 small">Título del Libro</label>
                            <input style="border-color: #470000;" type="text" name="titulo" class="form-control form-control-sm" value="<?php echo texto($libroEditar['titulo'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">Autor</label>
                            <select style="border-color: #470000;" name="autor_id" class="form-select form-select-sm" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach($autores as $a): ?>
                                    <option value="<?php echo $a['id']; ?>" <?php if(($libroEditar['autor_id']??'') == $a['id']) echo 'selected'; ?>><?php echo texto($a['nombre_completo']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">Editorial</label>
                            <select style="border-color: #470000;" name="editorial_id" class="form-select form-select-sm" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach($editoriales as $e): ?>
                                    <option value="<?php echo $e['id']; ?>" <?php if(($libroEditar['editorial_id']??'') == $e['id']) echo 'selected'; ?>><?php echo texto($e['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">F. Publicación</label>
                            <input style="border-color: #470000;" type="date" name="anio_publicacion" class="form-control form-control-sm" value="<?php echo texto($libroEditar['anio_publicacion'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">Idioma</label>
                            <input style="border-color: #470000;" type="text" name="idioma" class="form-control form-control-sm" value="<?php echo texto($libroEditar['idioma'] ?? 'Español'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">Categoría Principal</label>
                            <select style="border-color: #470000;" name="cat_principal" class="form-select form-select-sm" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach($categorias as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php if(($libroEditar['cat_principal_id']??'') == $c['id']) echo 'selected'; ?>><?php echo texto($c['nombre_categoria']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">Categoría Secundaria</label>
                            <select style="border-color: #470000;" name="cat_secundaria" class="form-select form-select-sm">
                                <option value="0">-- Ninguna --</option>
                                <?php foreach($categorias as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php if(($libroEditar['cat_secundaria_id']??'') == $c['id']) echo 'selected'; ?>><?php echo texto($c['nombre_categoria']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label mb-1 small">Descripción / Sinopsis</label>
                        <input style="border-color: #470000;" type="text" name="descripcion" class="form-control form-control-sm" value="<?php echo texto($libroEditar['descripcion'] ?? ''); ?>">
                    </div>

                    <button style="background-color: #ec3030ff;" type="submit" class="btn btn-sm text-white">Guardar Libro</button>
                    <?php if($libroEditar): ?><a href="index.php?page=libros" style="background-color: #ec3030ff;" class="btn btn-sm text-white">Cancelar</a><?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div style="border-color: #470000; background-color: #470000" class="card-header text-white fw-bold">Libros Disponibles</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover align-middle mb-0 small">
                        <thead>
                            <tr class="table-secondary">
                                <th>Título</th>
                                <th>Autor / Editorial</th>
                                <th>Géneros</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($libros) === 0): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No hay libros registrados.</td></tr>
                            <?php endif; ?>
                            <?php foreach($libros as $l): ?>
                            <tr <?php if(($libroSeleccionadoCopias['id'] ?? 0) == $l['id']) echo 'class="table-info"'; ?>>
                                <td><strong><?php echo texto($l['titulo']); ?></strong></td>
                                <td>
                                    <span class="text-dark"><?php echo texto($l['autor']); ?></span><br>
                                    <small class="text-muted"><?php echo texto($l['editorial']); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo texto($l['cat_principal'] ?? 'Sin categoría'); ?></span>
                                    <?php if(!empty($l['cat_secundaria'])): ?>
                                        <span class="badge bg-secondary"><?php echo texto($l['cat_secundaria']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="index.php?page=libros&editar=<?php echo $l['id']; ?>"  style="background-color: #2c44dbff;" class="btn text-white py-0 px-2">Editar</a>
                                        <a href="index.php?page=libros&libro_copias=<?php echo $l['id']; ?>" class="btn btn-info text-white py-0 px-2">Copias</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php if($libroSeleccionadoCopias): ?>
            <div style="border-color: #470000;" class="card mb-3 ">
                <div  style="background-color: #470000;" class="card-header  text-white fw-bold">Nueva Copia: <?php echo texto($libroSeleccionadoCopias['titulo']); ?></div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=libros&libro_copias=<?php echo $libroSeleccionadoCopias['id']; ?>">
                        <input type="hidden" name="action_copia" value="1">
                        <input type="hidden" name="libro_id" value="<?php echo $libroSeleccionadoCopias['id']; ?>">

                        <div class="mb-2">
                            <label class="form-label mb-1 small">Estado Físico</label>
                            <select name="estado" class="form-select form-select-sm" required>
                                <option value="disponible">Disponible</option>
                                <option value="prestado">Prestado</option>
                                <option value="dañado">Dañado</option>
                                <option value="reservado">Reservado</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1 small">Fecha Adquisición</label>
                            <input type="date" name="fecha_adquisicion" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label mb-1 small">Ubicación (Estante/Sala)</label>
                            <input type="text" name="ubicacion" class="form-control form-control-sm" placeholder="Ej: Estante B-4" required>
                        </div>
                        <button style="background-color: #2c44dbff;" type="submit" class="btn btn-sm text-white w-100">Añadir Copia</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light fw-bold">Inventario de Copias (IDs)</div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 250px;">
                        <table class="table table-sm table-striped align-middle mb-0 small text-center">
                            <thead>
                                <tr class="table-light">
                                    <th>ID Copia</th>
                                    <th>Estado</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($copias) === 0): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No hay copias físicas creadas.</td></tr>
                                <?php endif; ?>
                                <?php foreach($copias as $cop): ?>
                                <tr>
                                    <td><code>#<?php echo $cop['id']; ?></code></td>
                                    <td>
                                        <?php 
                                            $badge = 'bg-success';
                                            if($cop['estado'] === 'prestado') $badge = 'bg-primary';
                                            if($cop['estado'] === 'dañado') $badge = 'bg-danger';
                                            if($cop['estado'] === 'reservado') $badge = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?php echo $badge; ?> font-monospace"><?php echo $cop['estado']; ?></span>
                                    </td>
                                    <td><span class="text-muted"><?php echo texto($cop['ubicacion']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary text-center py-4">
                <i class="text-muted d-block mb-2 small">Panel de Inventario</i>
                <p class="mb-0 small text-muted">Da clic en el botón <strong>"Copias"</strong> de cualquier libro en el listado para ver o registrar sus existencias físicas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>