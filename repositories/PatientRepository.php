<?php

class PatientRepository implements BaseReposetry
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
                p.date_naissance
            FROM users u
            INNER JOIN patients p ON u.id = p.id
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
                p.date_naissance
            FROM users u
            INNER JOIN patients p ON u.id = p.id
            WHERE u.id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO patients (id, date_naissance)
             VALUES (:id, :date_naissance)"
        );

        return $stmt->execute([
            ':id' => $data['id'],
            ':date_naissance' => $data['date_naissance']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE patients 
             SET date_naissance = :date_naissance
             WHERE id = :id"
        );

        return $stmt->execute([
            ':date_naissance' => $data['date_naissance'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM patients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count()
    {
        return $this->conn
            ->query("SELECT COUNT(*) FROM patients")
            ->fetchColumn();
    }
}
