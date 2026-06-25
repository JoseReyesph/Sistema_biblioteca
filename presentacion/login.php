<!--<div class="container d-flex justify-content-center align-items-center vh-100">


    <div class="card shadow-sm p-4" style="width: 24rem;">
            <h3 class="card-title text-center mb-4" style="background-color: ; color: #ff4848ff ;">BEA System</h3>

            <?php if (isset($error_login)): ?>
                <div class="alert alert-danger p-2 text-center">
                    <?php echo $error_login; ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=login" method="POST">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="user_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contrasena</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" name="btn_login" style="background-color: #f84848ff; color: white;" class="btn w-100">
                    Ingresar
                </button>
            </form>
        </div>
    </div>
                #750000
</div>-->

<div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="row w-100 shadow-sm rounded overflow-hidden bg-white" style="max-width: 900px;">
        
        <div class="col-md-6 d-none d-md-block p-0">
            <img src="petsistema.gif" alt="Imagen de fondo" class="w-100 h-100" style="object-fit: cover;">
        </div>

        <div class="col-12 col-md-6 p-4 p-sm-5 d-flex flex-column justify-content-center">
            
            <h3 class="card-title text-center mb-4" style="color: #ff4848ff;">BEA System</h3>

            <?php if (isset($error_login)): ?>
                <div class="alert alert-danger p-2 text-center">
                    <?php echo $error_login; ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=login" method="POST">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="user_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" name="btn_login" style="background-color: #f84848ff; color: white;" class="btn w-100 mt-4">
                    Ingresar
                </button>
            </form>

        </div>
    </div>
</div>