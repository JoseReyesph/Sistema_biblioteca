<?php
require_once __DIR__ . '/../data/connection.php';

class PrestamoDatos {
    private $db;

    public function __construct() {
        $this->db = new Connection();
    }

    public function obtenerEstudiantes() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            
            $sql = "SELECT e.id, e.carnet_estudiante, e.nombre 
                    FROM estudiantes e
                    LEFT JOIN prestamos p ON e.id = p.estudiante_id AND p.fecha_devolucion IS NULL
                    GROUP BY e.id, e.carnet_estudiante, e.nombre
                    HAVING COUNT(p.id) < 3
                    ORDER BY e.nombre ASC";
                    
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function buscarLibros($buscar = '') {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];

            if ($buscar !== '') {
                $sql = "SELECT id, titulo FROM libros WHERE titulo LIKE ? ORDER BY titulo ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(["%$buscar%"]);
            } else {
                $sql = "SELECT id, titulo FROM libros ORDER BY titulo ASC LIMIT 10";
                $stmt = $pdo->query($sql);
            }

            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function listarCopiasPorLibro($libro_id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $sql = "SELECT id, estado, ubicacion FROM copias_libros WHERE libro_id = ? ORDER BY id ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$libro_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function obtenerDetalleCopia($copia_id) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;
            $sql = "SELECT c.id AS copia_id, l.titulo FROM copias_libros c 
                    INNER JOIN libros l ON c.libro_id = l.id WHERE c.id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$copia_id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return false; }
    }

    public function registrarPrestamo($estudiante_id, $user_id, $fecha_prestamo, $fecha_limite, $copias_ids) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $pdo->beginTransaction();

            $sqlPrestamo = "INSERT INTO prestamos (copia_id, fecha_prestamo, fecha_limite, estudiante_id, user_id, fecha_devolucion) 
                            VALUES (?, ?, ?, ?, ?, NULL)";
            $stmtPrestamo = $pdo->prepare($sqlPrestamo);

            $sqlCopia = "UPDATE copias_libros SET estado = 'prestado' WHERE id = ?";
            $stmtCopia = $pdo->prepare($sqlCopia);

            foreach ($copias_ids as $copia_id) {
                $stmtPrestamo->execute([$copia_id, $fecha_prestamo, $fecha_limite, $estudiante_id, $user_id]);
                $stmtCopia->execute([$copia_id]);
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
}