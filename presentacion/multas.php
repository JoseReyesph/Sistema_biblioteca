<?php
require_once __DIR__ . '/../negocio/MultaNegocio.php';

$multaNegocio = new MultaNegocio();
$mensaje = '';
$tipoMensaje = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $multaNegocio->procesarAccion($_POST);
    if ($resultado !== true && $resultado !== false) {
        $mensaje = $resultado;
        $tipoMensaje = 'danger';
    } elseif ($resultado === true) {
        $mensaje = "¡Devolución y estadísticas del libro actualizadas con éxito!";
        $tipoMensaje = 'success';
    }
}

$prestamos = $multaNegocio->listarPrestamosActivos();

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

    <div class="card border-0 shadow-sm">
        <div style="background-color: #470000;"class="card-header  text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Control de Devoluciones, Multas y Valoraciones</h6>
            <span class="badge "><?= count($prestamos) ?> Pendientes</span>
        </div>
        <div class="card-body bg-white">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle small">
                    <thead style="border-color: #470000;" class="table-light">
                        <tr>
                            <th>ID Préstamo</th>
                            <th>Libro Prestado</th>
                            <th>Estudiante</th>
                            <th>Fecha Préstamo</th>
                            <th class="text-center" style="width: 220px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($prestamos)): ?>
                            <tr><td colspan="5" class="text-muted text-center py-4">No hay devoluciones pendientes en este momento.</td></tr>
                        <?php else:  ?>
                            <?php foreach ($prestamos as $p): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $p['prestamo_id'] ?></td>
                                    <td><span class="fw-semibold text-dark"><?= texto($p['nombre_libro']) ?></span></td>
                                    <td><?= texto($p['nombre_estudiante']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            
                                            <button type="button" class="btn btn-xs btn-success py-1 px-2" style="font-size: 0.75rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalRecibir" 
                                                    data-id="<?= $p['prestamo_id'] ?>" 
                                                    data-libro="<?= texto($p['nombre_libro']) ?>">
                                                <i class="bi bi-check2-circle"></i> Recibido
                                            </button>

                                            <button type="button" class="btn btn-xs btn-danger py-1 px-2" style="font-size: 0.75rem;" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalMulta" 
                                                    data-id="<?= $p['prestamo_id'] ?>" 
                                                    data-libro="<?= texto($p['nombre_libro']) ?>" 
                                                    data-estudiante="<?= texto($p['nombre_estudiante']) ?>">
                                                <i class="bi bi-cash-coin"></i> Multar
                                            </button>
                                        </div>
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

<div class="modal fade" id="modalRecibir" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-success text-white py-2">
        <h6 class="modal-title small"><i class="bi bi-star-fill"></i> Valorar Libro</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST">
          <input type="hidden" name="accion_multa" value="recibir_normal">
          <input type="hidden" name="prestamo_id" id="recibir_prestamo_id">
          <div class="modal-body py-3">
                <p class="text-muted mb-2 text-center" style="font-size:0.8rem;" id="recibir_txt_libro"></p>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-secondary">¿Qué nota le da el estudiante? (1-10)</label>
                    <select name="calificacion" class="form-select form-select-sm" required>
                        <?php for($i=10; $i>=1; $i--): ?>
                            <option value="<?= $i ?>"><?= $i ?> <?= $i == 10 ? ' - ¡Excelente!' : '' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
          </div>
          <div class="modal-footer bg-light py-1">
            <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-success btn-xs fw-bold">Confirmar Devolución</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalMulta" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white py-2">
        <h6 class="modal-title small"><i class="bi bi-calculator"></i> Registrar Multa por Retraso</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST">
          <div class="modal-body">
                <input type="hidden" name="accion_multa" value="aplicar_multa">
                <input type="hidden" name="prestamo_id" id="modal_prestamo_id">
                
                <div class="mb-3 bg-light p-2 rounded small">
                    <strong>Estudiante:</strong> <span id="modal_txt_estudiante"></span><br>
                    <strong>Libro:</strong> <span id="modal_txt_libro"></span>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Días de Retraso</label>
                        <input type="number" name="dias_retraso" id="input_dias" class="form-control form-control-sm" min="1" placeholder="Ej. 4" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Calificación del Libro</label>
                        <select name="calificacion" class="form-select form-select-sm" required>
                            <?php for($i=10; $i>=1; $i--): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="fs-6 fw-bold text-end text-danger mb-0">
                    Total de Multa: <span id="lbl_total">$0.00</span>
                </div>
          </div>
          <div class="modal-footer bg-light py-2">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger btn-sm fw-bold"><i class="bi bi-wallet2"></i> Registrar Multa y Devolución</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    var modalRecibir = document.getElementById('modalRecibir');
    modalRecibir.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        document.getElementById('recibir_prestamo_id').value = btn.getAttribute('data-id');
        document.getElementById('recibir_txt_libro').textContent = "Libro: " + btn.getAttribute('data-libro');
    });

    var modalMulta = document.getElementById('modalMulta');
    var inputDias = document.getElementById('input_dias');
    var lblTotal = document.getElementById('lbl_total');

    modalMulta.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('modal_prestamo_id').value = button.getAttribute('data-id');
        document.getElementById('modal_txt_estudiante').textContent = button.getAttribute('data-estudiante');
        document.getElementById('modal_txt_libro').textContent = button.getAttribute('data-libro');
        inputDias.value = '';
        lblTotal.textContent = '$0.00';
    });

    inputDias.addEventListener('input', function() {
        var dias = parseInt(this.value) || 0;
        if(dias < 0) dias = 0;
        lblTotal.textContent = '$' + (dias * 1.00).toFixed(2);
    });
});
</script>