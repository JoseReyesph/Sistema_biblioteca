<?php
require_once __DIR__ . '/../data/connection.php';

class LibroDatos {
    private $db;

    public function __construct() {
        $this->db = new Connection();
    }

    public function listarLibros() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            
            $sql = "SELECT l.*, a.nombre_completo AS autor, e.nombre AS editorial,
                    (SELECT c.nombre_categoria FROM libros_categorias lc 
                     LEFT JOIN categorias c ON lc.categoria_id = c.id 
                     WHERE lc.libro_id = l.id AND lc.tipo = 'principal' LIMIT 1) AS cat_principal,
                    (SELECT c.nombre_categoria FROM libros_categorias lc 
                     LEFT JOIN categorias c ON lc.categoria_id = c.id 
                     WHERE lc.libro_id = l.id AND lc.tipo = 'secundaria' LIMIT 1) AS cat_secundaria
                    FROM libros l
                    LEFT JOIN autores a ON l.autor_id = a.id
                    LEFT JOIN editoriales e ON l.editorial_id = e.id
                    ORDER BY l.id DESC";
                    
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function obtenerLibroPorId($id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $sql = "SELECT l.*, 
                    (SELECT categoria_id FROM libros_categorias WHERE libro_id = l.id AND tipo = 'principal' LIMIT 1) AS cat_principal_id,
                    (SELECT categoria_id FROM libros_categorias WHERE libro_id = l.id AND tipo = 'secundaria' LIMIT 1) AS cat_secundaria_id
                    FROM libros l WHERE l.id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }

    public function guardarLibro($id, $titulo, $autor_id, $editorial_id, $anio, $idioma, $desc, $cat_p, $cat_s) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $pdo->beginTransaction();

            if ($id > 0) {
                
                $sql = "UPDATE libros SET titulo = ?, autor_id = ?, editorial_id = ?, anio_publicacion = ?, idioma = ?, descripcion = ?, updated_at = CURDATE() WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo, $autor_id, $editorial_id, $anio, $idioma, $desc, $id]);
                $libro_id = $id;

                $pdo->prepare("DELETE FROM libros_categorias WHERE libro_id = ?")->execute([$libro_id]);
            } else {

                $sql = "INSERT INTO libros (titulo, autor_id, editorial_id, anio_publicacion, idioma, descripcion, calificacion, cantidad_prestamos, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, 0, 0, CURDATE(), CURDATE())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo, $autor_id, $editorial_id, !empty($anio) ? $anio : null, $idioma, $desc]);
                $libro_id = $pdo->lastInsertId();
            }

            if ($cat_p > 0) {
                $sqlCat = "INSERT INTO libros_categorias (libro_id, categoria_id, tipo) VALUES (?, ?, 'principal')";
                $pdo->prepare($sqlCat)->execute([$libro_id, $cat_p]);
            }

            if ($cat_s > 0 && $cat_s !== $cat_p) {
                $sqlCat = "INSERT INTO libros_categorias (libro_id, categoria_id, tipo) VALUES (?, ?, 'secundaria')";
                $pdo->prepare($sqlCat)->execute([$libro_id, $cat_s]);
            }

            $pdo->commit();
            $this->db->close_conection();
            return true;
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }

    public function listarCopiasPorLibro($libro_id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $stmt = $pdo->prepare("SELECT * FROM copias_libros WHERE libro_id = ? ORDER BY id DESC");
            $stmt->execute([$libro_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function guardarCopia($libro_id, $estado, $fecha_adq, $ubicacion) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            $sql = "INSERT INTO copias_libros (libro_id, estado, fecha_adquisicion, ubicacion) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $res = $stmt->execute([$libro_id, $estado, $fecha_adq, $ubicacion]);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }
}