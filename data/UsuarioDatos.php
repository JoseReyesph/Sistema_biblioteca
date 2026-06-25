<?php

require_once __DIR__ . '/../data/connection.php';

class UsuarioDatos {
    private $db;

    public function __construct() {
        $this->db = new Connection();
    }

    public function obtenerUsuarioPorUsername($username) {
        try {
            $pdo = $this->db->new_connection();

            if ($pdo === null) {
                return false;
            }

            $sql = "SELECT id, user_name, password, email, rol, estado
                    FROM usuarios
                    WHERE user_name = :username AND estado = 'activo'
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en UsuarioDatos: " . $e->getMessage());
            return false;
        }
    }

    public function listarUsuarios($busqueda = '') {
        try {
            $pdo = $this->db->new_connection();

            if ($pdo === null) {
                return [];
            }

            $sql = "SELECT id, user_name, email, rol, created_at, estado
                    FROM usuarios
                    WHERE user_name LIKE :busqueda OR email LIKE :busqueda OR rol LIKE :busqueda
                    ORDER BY id DESC";

            $buscar = '%' . $busqueda . '%';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busqueda', $buscar, PDO::PARAM_STR);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->db->close_conection();
            return $usuarios;
            
        } catch (PDOException $e) {
            error_log("Error al listar usuarios: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerUsuarioPorId($id) {
        try {
            $pdo = $this->db->new_connection();

            if ($pdo === null) {
                return false;
            }

            $sql = "SELECT id, user_name, email, password, rol, estado
                    FROM usuarios
                    WHERE id = :id
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->db->close_conection();
            return $usuario;
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }

    public function existeUsername($username, $idIgnorar = 0) {
        try {
            $pdo = $this->db->new_connection();

            if ($pdo === null) {
                return false;
            }

            $sql = "SELECT id FROM usuarios
                    WHERE user_name = :username AND id <> :id
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':id', $idIgnorar, PDO::PARAM_INT);
            $stmt->execute();

            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->db->close_conection();
            return $existe ? true : false;
        } catch (PDOException $e) {
            error_log("Error al validar username: " . $e->getMessage());
            return false;
        }
    }

    public function crearUsuario($username, $email, $password, $rol) {
        try {
            $pdo = $this->db->new_connection();

            if ($pdo === null) {
                return false;
            }

            $sql = "INSERT INTO usuarios (user_name, email, password, rol, created_at, estado)
                    VALUES (:username, :email, :password, :rol, CURDATE(), 'activo')";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
            $resultado = $stmt->execute();

            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarUsuario($id, $username, $email, $rol, $estado, $password = '') {
        try {
            $pdo = $this->db->new_connection();

            if ($pdo === null) {
                return false;
            }

            if ($password !== '') {
                $sql = "UPDATE usuarios
                        SET user_name = :username, email = :email, password = :password, rol = :rol, estado = :estado
                        WHERE id = :id";
            } else {
                $sql = "UPDATE usuarios
                        SET user_name = :username, email = :email, rol = :rol, estado = :estado
                        WHERE id = :id";
            }

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($password !== '') {
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            }

            $resultado = $stmt->execute();

            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function desactivarUsuario($id) {
        try {
            $pdo = $this->db->new_connection();

            if ($pdo === null) {
                return false;
            }

            $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();

            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al desactivar usuario: " . $e->getMessage());
            return false;
        }
    }
}
