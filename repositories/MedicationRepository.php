<?php

class MedicationRepository implements BaseReposetry
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM medications");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM medications WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO medications (prescription_id, nom, dosage)
             VALUES (:prescription_id, :nom, :dosage)"
        );

        return $stmt->execute([
            ':prescription_id' => $data['prescription_id'],
            ':nom' => $data['nom'],
            ':dosage' => $data['dosage']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE medications 
             SET nom = :nom, dosage = :dosage
             WHERE id = :id"
        );

        return $stmt->execute([
            ':nom' => $data['nom'],
            ':dosage' => $data['dosage'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM medications WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count()
    {
        return $this->conn
            ->query("SELECT COUNT(*) FROM medications")
            ->fetchColumn();
    }
}
