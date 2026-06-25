<?php

require_once __DIR__ . '/../negocio/GestionNegocio.php';

$gestionNegocio = new GestionNegocio();
$mensaje = '';
$tipoMensaje = 'success';

$catEditar = null;
$editEditar = null;
$autEditar = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $gestionNegocio->procesarFormulario($_POST);
    if ($resultado === true) {
        $mensaje = "Datos guardados correctamente.";
    } else {
        $mensaje = $resultado;
        $tipoMensaje = 'danger';
    }
}

// CAPTURAR EDICIONES INDEPENDIENTES
if (isset($_GET['edit_cat'])) $catEditar = $gestionNegocio->obtenerPorId('categoria', $_GET['edit_cat']);
if (isset($_GET['edit_edit'])) $editEditar = $gestionNegocio->obtenerPorId('editorial', $_GET['edit_edit']);
if (isset($_GET['edit_aut'])) $autEditar = $gestionNegocio->obtenerPorId('autor', $_GET['edit_aut']);

$categorias = $gestionNegocio->listarCategorias();
$editoriales = $gestionNegocio->listarEditoriales();
$autores = $gestionNegocio->listarAutores();

if (!function_exists('texto')) {
    function texto($valor) { return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8'); }
}
?>

<div class="mb-3">
    <h1 class="h4">Gestión General</h1
</div>

<?php if ($mensaje !== ''): ?>
    <div class="alert alert-<?php echo $tipoMensaje; ?>"><?php echo texto($mensaje); ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div style="border-color: #470000; background-color: #470000" class="card-header text-white fw-bold">Gestión de Categorías (Géneros)</div>
    <div  class="card-body">
        <div class="row">
            <div class="col-md-5 border-end">
                <form method="POST" action="index.php?page=gestion">
                    <input type="hidden" name="tipo_formulario" value="categoria">
                    <input style="border-color: #470000;" type="hidden" name="id" value="<?php echo texto($catEditar['id'] ?? ''); ?>">
                    
                    <div class="mb-2">
                        <label class="form-label mb-1">Nombre Categoría</label>
                        <input style="border-color: #470000;" type="text" name="nombre_categoria" class="form-control form-control-sm" value="<?php echo texto($catEditar['nombre_categoria'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1">Descripción</label>
                        <input style="border-color: #470000;" type="text" name="descripcion" class="form-control form-control-sm" value="<?php echo texto($catEditar['descripcion'] ?? ''); ?>">
                    </div>
                    <button style="background-color: #ec3030ff;"  type="submit" class="btn btn-sm text-white">Guardar</button>
                    <?php if($catEditar): ?><a href="index.php?page=gestion" class="btn btn-sm btn-secondary">X</a><?php endif; ?>
                </form>
            </div>
            <div class="col-md-7">
                <div class="table-responsive" style="max-height: 180px;">
                    <table class="table table-sm table-striped align-middle small">
                        <thead><tr><th>Categoría</th><th class="text-end">Acción</th></tr></thead>
                        <tbody>
                            <?php foreach($categorias as $c): ?>
                            <tr>
                                <td><strong><?php echo texto($c['nombre_categoria']); ?></strong> <br><small class="text-muted"><?php echo texto($c['descripcion']); ?></small></td>
                                <td class="text-end"><a href="index.php?page=gestion&edit_cat=<?php echo $c['id']; ?>" style="background-color: #2c44dbff;" class="btn btn-xs text-white py-0 px-1 small">Editar</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light fw-bold">Gestión de Editoriales</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 border-end">
                <form method="POST" action="index.php?page=gestion">
                    <input type="hidden" name="tipo_formulario" value="editorial">
                    <input type="hidden" name="id" value="<?php echo texto($editEditar['id'] ?? ''); ?>">
                    
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label mb-1">Nombre</label>
                            <input style="border-color: #470000;" type="text" name="nombre" class="form-control form-control-sm" value="<?php echo texto($editEditar['nombre'] ?? ''); ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1">País</label>
                            <input style="border-color: #470000;" type="text" name="pais" class="form-control form-control-sm" value="<?php echo texto($editEditar['pais'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label mb-1">Email</label>
                            <input style="border-color: #470000;" type="email" name="email" class="form-control form-control-sm" value="<?php echo texto($editEditar['email'] ?? ''); ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1">Teléfono</label>
                            <input style="border-color: #470000;" type="text" name="telefono" class="form-control form-control-sm" value="<?php echo texto($editEditar['telefono'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <?php if($editEditar): ?>
                    <div class="mb-2">
                        <label class="form-label mb-1">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="activo" <?php if(($editEditar['estado']??'')==='activo') echo 'selected'; ?>>Activo</option>
                            <option value="inactivo" <?php if(($editEditar['estado']??'')==='inactivo') echo 'selected'; ?>>Inactivo</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <button style="background-color: #ec3030ff;" type="submit" class="btn text-white btn-sm ">Guardar</button>
                    <?php if($editEditar): ?><a href="index.php?page=gestion"  class="btn btn-sm btn-secondary">X</a><?php endif; ?>
                </form>
            </div>
            <div class="col-md-7">
                <div class="table-responsive" style="max-height: 200px;">
                    <table class="table table-sm table-striped align-middle small">
                        <thead><tr><th>Editorial</th><th>País</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                        <tbody>
                            <?php foreach($editoriales as $e): ?>
                            <tr>
                                <td><?php echo texto($e['nombre']); ?></td>
                                <td><?php echo texto($e['pais']); ?></td>
                                <td><span class="badge <?php echo $e['estado']==='activo'?'bg-success':'bg-secondary'; ?> small"><?php echo $e['estado']; ?></span></td>
                                <td class="text-end"><a href="index.php?page=gestion&edit_edit=<?php echo $e['id']; ?>" style="background-color: #2c44dbff;" class="btn btn-xs text-white py-0 px-1">Editar</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light fw-bold">Gestión de Autores</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 border-end">
                <form method="POST" action="index.php?page=gestion">
                    <input type="hidden" name="tipo_formulario" value="autor">
                    <input type="hidden" name="id" value="<?php echo texto($autEditar['id'] ?? ''); ?>">
                    
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label mb-1">Alias</label>
                            <input style="border-color: #470000;" type="text" name="alias" class="form-control form-control-sm" value="<?php echo texto($autEditar['alias'] ?? ''); ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1">Nacionalidad</label>
                            <input style="border-color: #470000;" type="text" name="nacionalidad" class="form-control form-control-sm" value="<?php echo texto($autEditar['nacionalidad'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label mb-1">Nombre Completo</label>
                            <input style="border-color: #470000;" type="text" name="nombre_completo" class="form-control form-control-sm" value="<?php echo texto($autEditar['nombre_completo'] ?? ''); ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1">F. Nacimiento</label>
                            <input style="border-color: #470000;" type="date" name="fecha_nacimiento" class="form-control form-control-sm" value="<?php echo texto($autEditar['fecha_nacimiento'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <?php if($autEditar): ?>
                    <div class="mb-2">
                        <label class="form-label mb-1">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="activo" <?php if(($autEditar['estado']??'')==='activo') echo 'selected'; ?>>Activo</option>
                            <option value="inactivo" <?php if(($autEditar['estado']??'')==='inactivo') echo 'selected'; ?>>Inactivo</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <button style="background-color: #ec3030ff;" type="submit" class="btn btn-sm text-white">Guardar</button>
                    <?php if($autEditar): ?><a href="index.php?page=gestion" class="btn btn-sm btn-secondary">X</a><?php endif; ?>
                </form>
            </div>
            <div class="col-md-7">
                <div class="table-responsive" style="max-height: 200px;">
                    <table class="table table-sm table-striped align-middle small">
                        <thead><tr><th>Nombre Autor</th><th>Alias</th><th class="text-end">Acción</th></tr></thead>
                        <tbody>
                            <?php foreach($autores as $a): ?>
                            <tr>
                                <td><?php echo texto($a['nombre_completo']); ?></td>
                                <td><span class="text-muted"><?php echo texto($a['alias']); ?></span></td>
                                <td class="text-end"><a href="index.php?page=gestion&edit_aut=<?php echo $a['id']; ?>" style="background-color: #2c44dbff;" class="btn btn-xs text-white py-0 px-1">Editar</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>