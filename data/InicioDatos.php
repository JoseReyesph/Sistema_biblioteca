<?php
require_once __DIR__ . '/../data/connection.php';

class DashboardDatos {
    private $db;
    public function __construct() { $this->db = new Connection(); }

    public function obtenerTopLibros($tipo) {
        
        $pdo = $this->db->new_connection();

        if (!$pdo) return [];
        $columna = ($tipo === 'prestamos') ? 'cantidad_prestamos' : 'calificacion';
        $sql = "SELECT titulo, $columna as valor FROM libros ORDER BY $columna DESC LIMIT 5";
        $res = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close_conection();
        return $res;
    }
}