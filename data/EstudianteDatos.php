<?php
require_once __DIR__ . '/../data/connection.php';

class EstudianteDatos {
    private $db;

    public function __construct() {
        $this->db = new Connection();
    }

    public function listarEstudiantes($busqueda = '') {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];

            if ($busqueda !== '') {
                $sql = "SELECT id, carnet_estudiante, nombre, telefono, email, created_at 
                        FROM estudiantes 
                        WHERE carnet_estudiante LIKE ? OR nombre LIKE ? OR email LIKE ?
                        ORDER BY id ASC";
                $stmt = $pdo->prepare($sql);
                $buscar = '%' . $busqueda . '%';
                $stmt->execute([$buscar, $buscar, $buscar]);
            } else {
                $sql = "SELECT id, carnet_estudiante, nombre, telefono, email, created_at 
                        FROM estudiantes 
                        ORDER BY id ASC";
                $stmt = $pdo->query($sql);
            }

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerEstudiantePorId($id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $sql = "SELECT id, carnet_estudiante, nombre, telefono, email FROM estudiantes WHERE id = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function existeCarnet($carnet, $idIgnorar = 0) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $sql = "SELECT id FROM estudiantes WHERE carnet_estudiante = ? AND id <> ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$carnet, $idIgnorar]);

            $existe = $stmt->fetch();
            $this->db->close_conection();
            return $existe ? true : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function crearEstudiante($carnet, $nombre, $telefono, $email) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $sql = "INSERT INTO estudiantes (carnet_estudiante, nombre, telefono, email, created_at) 
                    VALUES (?, ?, ?, ?, CURDATE())";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$carnet, $nombre, $telefono, $email]);

            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizarEstudiante($id, $carnet, $nombre, $telefono, $email) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $sql = "UPDATE estudiantes SET carnet_estudiante = ?, nombre = ?, telefono = ?, email = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$carnet, $nombre, $telefono, $email, $id]);

            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminarEstudiante($id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $sql = "DELETE FROM estudiantes WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$id]);

            $this->db->close_conection();
            return $resultado;
        } catch (PDOException $e) {
            return false;
        }
    }
}