<?php
require_once __DIR__ . '/../data/connection.php';

class ReporteDatos {
    private $db;

    public function __construct() {
        $this->db = new Connection();
    }

    public function obtenerPrestamosDevueltos() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $sql = "SELECT p.id, l.titulo, e.nombre, p.fecha_prestamo, p.fecha_devolucion 
                    FROM prestamos p
                    INNER JOIN copias_libros c ON p.copia_id = c.id
                    INNER JOIN libros l ON c.libro_id = l.id
                    INNER JOIN estudiantes e ON p.estudiante_id = e.id
                    WHERE p.fecha_devolucion IS NOT NULL
                    ORDER BY p.fecha_devolucion DESC";
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function obtenerPrestamosPendientes() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $sql = "SELECT p.id, l.titulo, e.nombre, p.fecha_prestamo, p.fecha_limite 
                    FROM prestamos p
                    INNER JOIN copias_libros c ON p.copia_id = c.id
                    INNER JOIN libros l ON c.libro_id = l.id
                    INNER JOIN estudiantes e ON p.estudiante_id = e.id
                    WHERE p.fecha_devolucion IS NULL
                    ORDER BY p.fecha_prestamo ASC";
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }

    public function obtenerMultas() {
        try {
            $pdo = $this->db->new_connection();
            if ($pdo === null) return [];
            $sql = "SELECT m.id, m.monto, m.motivo, m.fecha_pago, l.titulo, e.nombre
                    FROM multa m
                    INNER JOIN prestamos p ON m.prestamo_id = p.id
                    INNER JOIN copias_libros c ON p.copia_id = c.id
                    INNER JOIN libros l ON c.libro_id = l.id
                    INNER JOIN estudiantes e ON p.estudiante_id = e.id
                    ORDER BY m.fecha_pago DESC";
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->close_conection();
            return $res;
        } catch (PDOException $e) { return []; }
    }
}