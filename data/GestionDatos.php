<?php
require_once __DIR__ . '/../data/connection.php';

class GestionDatos {
    private $db;

    public function __construct() {
        $this->db = new Connection();
    }

    // CATEGORIAS
    public function listarCategorias() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $stmt = $pdo->query("SELECT id, nombre_categoria, descripcion FROM categorias ORDER BY id DESC");
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function obtenerCategoriaPorId($id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            $stmt = $pdo->prepare("SELECT id, nombre_categoria, descripcion FROM categorias WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }

    public function guardarCategoria($id, $nombre, $descripcion) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            if ($id > 0) {
                $sql = "UPDATE categorias SET nombre_categoria = ?, descripcion = ?, updated_at = CURDATE() WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $res = $stmt->execute([$nombre, $descripcion, $id]);
            } else {
                $sql = "INSERT INTO categorias (nombre_categoria, descripcion, created_at, updated_at) VALUES (?, ?, CURDATE(), CURDATE())";
                $stmt = $pdo->prepare($sql);
                $res = $stmt->execute([$nombre, $descripcion]);
            }
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }

    // EDITORIALES
    public function listarEditoriales() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $stmt = $pdo->query("SELECT id, nombre, pais, email, telefono, estado FROM editoriales ORDER BY id DESC");
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function obtenerEditorialPorId($id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            $stmt = $pdo->prepare("SELECT id, nombre, pais, email, telefono, estado FROM editoriales WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }

    public function guardarEditorial($id, $nombre, $pais, $email, $telefono, $estado) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            if ($id > 0) {
                $sql = "UPDATE editoriales SET nombre = ?, pais = ?, email = ?, telefono = ?, estado = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $res = $stmt->execute([$nombre, $pais, $email, $telefono, $estado, $id]);
            } else {
                $sql = "INSERT INTO editoriales (nombre, pais, email, telefono, estado) VALUES (?, ?, ?, ?, 'activo')";
                $stmt = $pdo->prepare($sql);
                $res = $stmt->execute([$nombre, $pais, $email, $telefono]);
            }
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }

    // AUTORES
    public function listarAutores() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $stmt = $pdo->query("SELECT id, alias, nombre_completo, nacionalidad, fecha_nacimiento, estado FROM autores ORDER BY id DESC");
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function obtenerAutorPorId($id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            $stmt = $pdo->prepare("SELECT id, alias, nombre_completo, nacionalidad, fecha_nacimiento, estado FROM autores WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }

    public function guardarAutor($id, $alias, $nombre, $nacionalidad, $fecha_nac, $estado) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            if ($id > 0) {
                $sql = "UPDATE autores SET alias = ?, nombre_completo = ?, nacionalidad = ?, fecha_nacimiento = ?, estado = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $res = $stmt->execute([$alias, $nombre, $nacionalidad, $fecha_nac, $estado, $id]);
            } else {
                $sql = "INSERT INTO autores (alias, nombre_completo, nacionalidad, fecha_nacimiento, created_at, estado) VALUES (?, ?, ?, ?, CURDATE(), 'activo')";
                $stmt = $pdo->prepare($sql);
                $res = $stmt->execute([$alias, $nombre, $nacionalidad, $fecha_nac]);
            }
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }
}