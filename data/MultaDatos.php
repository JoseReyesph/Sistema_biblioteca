<?php
require_once __DIR__ . '/../data/connection.php';

class MultaDatos {
    private $db;

    public function __construct() {
        $this->db = new Connection();
    }

    public function listarPrestamosActivos() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            
            $sql = "SELECT p.id AS prestamo_id, l.titulo AS nombre_libro, e.nombre AS nombre_estudiante, p.fecha_prestamo 
                    FROM prestamos p
                    INNER JOIN copias_libros c ON p.copia_id = c.id
                    INNER JOIN libros l ON c.libro_id = l.id
                    INNER JOIN estudiantes e ON p.estudiante_id = e.id
                    WHERE p.fecha_devolucion IS NULL
                    ORDER BY p.fecha_prestamo DESC";
                    
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function recibirLibro($prestamo_id, $fecha_devolucion, $calificacion) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $pdo->beginTransaction();

            $sql1 = "UPDATE prestamos SET fecha_devolucion = ? WHERE id = ?";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute([$fecha_devolucion, $prestamo_id]);

            $sql2 = "UPDATE copias_libros SET estado = 'disponible' WHERE id = (SELECT copia_id FROM prestamos WHERE id = ?)";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$prestamo_id]);

            $sql3 = "UPDATE libros SET 
                        calificacion = calificacion + ?, 
                        cantidad_prestamos = cantidad_prestamos + 1 
                     WHERE id = (SELECT c.libro_id FROM prestamos p INNER JOIN copias_libros c ON p.copia_id = c.id WHERE p.id = ?)";
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->execute([$calificacion, $prestamo_id]);

            $pdo->commit();
            $this->db->close_conection();
            return true;
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    public function aplicarMulta($prestamo_id, $monto, $motivo, $fecha_pago, $calificacion) {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return false;

            $pdo->beginTransaction();

            $sql1 = "UPDATE prestamos SET fecha_devolucion = ? WHERE id = ?";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute([$fecha_pago, $prestamo_id]);

            $sql2 = "UPDATE copias_libros SET estado = 'disponible' WHERE id = (SELECT copia_id FROM prestamos WHERE id = ?)";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$prestamo_id]);

            $sql3 = "INSERT INTO multa (prestamo_id, monto, motivo, fecha_pago, estado) VALUES (?, ?, ?, ?, 'pagado')";
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->execute([$prestamo_id, $monto, $motivo, $fecha_pago]);

            $sql4 = "UPDATE libros SET 
                        calificacion = calificacion + ?, 
                        cantidad_prestamos = cantidad_prestamos + 1 
                     WHERE id = (SELECT c.libro_id FROM prestamos p INNER JOIN copias_libros c ON p.copia_id = c.id WHERE p.id = ?)";
            $stmt4 = $pdo->prepare($sql4);
            $stmt4->execute([$calificacion, $prestamo_id]);

            $pdo->commit();
            $this->db->close_conection();
            return true;
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }
}