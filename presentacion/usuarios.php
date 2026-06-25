<?php

$usuarioNegocio = new UsuarioNegocio();

$mensaje = '';

$tipoMensaje = 'success';

$usuarioEditar = null;

$busqueda = $_GET['buscar'] ?? '';

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($metodo === 'POST') {
    if (isset($_POST['guardar_usuario'])) {
        $resultado = $usuarioNegocio->guardarUsuario($_POST);

        if ($resultado === true) {
            $mensaje = 'Usuario guardado correctamente.';
        } else {
            $mensaje = $resultado;
            $tipoMensaje = 'danger';
        }
    }

    if (isset($_POST['desactivar_usuario'])) {
        $resultado = $usuarioNegocio->desactivarUsuario($_POST['id'] ?? 0);

        if ($resultado === true) {
            $mensaje = 'Usuario desactivado correctamente.';
        } else {
            $mensaje = $resultado;
            $tipoMensaje = 'danger';
        }
    }
}

if (isset($_GET['editar'])) {
    $usuarioEditar = $usuarioNegocio->obtenerUsuarioPorId($_GET['editar']);
}

$usuarios = $usuarioNegocio->listarUsuarios($busqueda);

function texto($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Usuarios</h1>
    </div>
</div>

<?php if ($mensaje !== ''): ?>
    <div class="alert alert-<?php echo $tipoMensaje; ?>">
        <?php echo texto($mensaje); ?>
    </div>
<?php endif; ?>

<div class="card mb-4" style="border-color: #470000">
    <div class="card-header" style="background-color: #470000; color: white;">
        <?php echo $usuarioEditar ? 'Editar usuario' : 'Nuevo usuario'; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="index.php?page=usuarios">
            <input type="hidden" name="id" value="<?php echo texto($usuarioEditar['id'] ?? ''); ?>">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Usuario</label>
                    <input style="border-color: #470000" type="text" name="user_name" class="form-control" value="<?php echo texto($usuarioEditar['user_name'] ?? ''); ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Correo</label>
                    <input style="border-color: #470000" type="email" name="email" class="form-control" value="<?php echo texto($usuarioEditar['email'] ?? ''); ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Contrasena</label>
                    <input style="border-color: #470000" type="password" name="password" class="form-control" <?php echo $usuarioEditar ? '' : 'required'; ?>>
                    <?php if ($usuarioEditar): ?>
                        <small class="text-muted">Dejar vacio para mantener la actual.</small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Rol</label>
                    <?php
                    $rolActual = $usuarioEditar['rol'] ?? 'usuario';
                    ?>
                    <select style="border-color: #470000" name="rol" class="form-select" required>
                        <option value="administrador" <?php if($rolActual === 'administrador') echo 'selected'; ?>>Admin</option>
                        <option value="gestion" <?php if($rolActual === 'gestion') echo 'selected'; ?>>Gestion</option>
                        <option value="usuario" <?php if($rolActual === 'usuario') echo 'selected'; ?>>Usuario</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Estado</label>
                    <?php $estadoActual = $usuarioEditar['estado'] ?? 'activo'; ?>
                    <select style="border-color: #470000" name="estado" class="form-select">
                        <option value="activo" <?php echo $estadoActual === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $estadoActual === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3 d-flex align-items-end gap-2">
                    <button  style="background-color: #ec3030ff; color: white; type="submit" name="guardar_usuario" class="btn">
                        Guardar
                    </button>

                    <?php if ($usuarioEditar): ?>
                        <a href="index.php?page=usuarios" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card" style="border-color: #470000">
    <div class="card-header" style="background-color: #470000; color: white;">
        Listado de usuarios
    </div>

    <div class="card-body">
        <form method="GET" action="index.php" class="row mb-3">
            <input type="hidden" name="page" value="usuarios">

            <div class="col-md-8">
                <input style="border-color: #470000" type="text" name="buscar" class="form-control" placeholder="Buscar por usuario, correo o rol" value="<?php echo texto($busqueda); ?>">
            </div>

            <div class="col-md-4 mt-2 mt-md-0">
                <button type="submit" class="btn btn-outline-primary" style="background-color: #ec3030ff; color: white; border-color: #ff4848ff;">Buscar</button>
                <a href="index.php?page=usuarios" class="btn btn-outline-secondary" style="background-color: #ec3030ff; color: white; border-color: #ff4848ff;">Limpiar</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($usuarios) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay usuarios para mostrar.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo texto($usuario['id']); ?></td>
                            <td><?php echo texto($usuario['user_name']); ?></td>
                            <td><?php echo texto($usuario['email']); ?></td>
                            <td><?php echo texto($usuario['rol']); ?></td>
                            <td><?php echo texto($usuario['created_at']); ?></td>
                            <td>
                                <?php if ($usuario['estado'] === 'activo'): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?page=usuarios&editar=<?php echo $usuario['id']; ?>" class="btn btn-sm" style="background-color: #2c44dbff; color: white; border-color: #2c44dbff;">
                                    Editar
                                </a>

                                <?php if ($usuario['estado'] === 'activo'): ?>
                                    <form method="POST" action="index.php?page=usuarios" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                        <button type="submit" name="desactivar_usuario" class="btn btn-sm btn-danger">
                                            Desactivar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
