<?php

class DoctorRepository implements BaseReposetry
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getAll()
    {
        $sql = "
            SELECT 
                u.id,
                u.nom,
                u.prenom,
                u.email,
                u.telephone,
                d.specialite
            FROM users u
            INNER JOIN doctors d ON u.id = d.id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "
            SELECT 
                u.id,
                u.nom,
                u.prenom,
                u.email,
                u.telephone,
                d.specialite
            FROM users u
            INNER JOIN doctors d ON u.id = d.id
            WHERE u.id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO doctors (id, specialite)
             VALUES (:id, :specialite)"
        );

        return $stmt->execute([
            ':id' => $data['id'], 
            ':specialite' => $data['specialite']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE doctors SET specialite = :specialite WHERE id = :id"
        );

        return $stmt->execute([
            ':specialite' => $data['specialite'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM doctors WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count()
    {
        return $this->conn
            ->query("SELECT COUNT(*) FROM doctors")
            ->fetchColumn();
    }
}
