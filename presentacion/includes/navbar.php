<?php
$paginaActual = $_GET['page'] ?? 'inicio';

?>

<aside style="border-right: 5px solid #000000ff; background-color: #470000; padding-bottom: 10px;" class="sidebar   text-white  d-flex flex-column ">
    <h3 class="mb-0 p-2">BEA System</h3>
    <h7 class="text-white p-2" style="margin-bottom: 10px; ">Menu principal</h7>

    <ul class="nav nav-pills flex-column mb-auto p-0 m-0">
        <?php foreach ($modulos as $clave => $modulo): ?>
            <?php if (!empty($modulo['menu'])): ?>
                <li class="nav-item w-100 m-0 p-0">
                    <a href="index.php?page=<?php echo $clave; ?>" style=" padding: 12px 20px; background-color: #470000; border: 1px solid #ffffffff; border-top: 2px solid #ffffffff; border-bottom: 2px solid #ffffffff; border-left: none; border-right: none; border-radius: 0;" class="nav-link text-white  <?php echo $paginaActual === $clave ? 'active' : ''; ?>">
                        <?php echo $modulo['titulo']; ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <hr>

    <div>
    <div  class=" d-flex" style="margin-bottom: 10px; margin-left: 15px;" >
        <div>
            <img src="ale.jpg"style="border-radius: 30px;" width="50" height="50" alt="perfil">
        </div>
        
        <div style="margin-left: 20px; " class="mb-2">
            <small class="text-white-50 d-block"></small>
            <strong style="font-size: 25px"><?php echo $_SESSION['usuario_nom'] ?? 'Usuario'; ?></strong>
        </div>
    </div> 
        <a style="background-color: #f1000067; border: none" href="index.php?page=logout" class="btn btn-sm btn-danger w-100">
            Cerrar sesion
        </a>
    </div>
</aside>

