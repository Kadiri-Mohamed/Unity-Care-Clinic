<?php

class PrescriptionRepository implements BaseReposetry
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM prescriptions");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM prescriptions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO prescriptions (appointment_id, date_prescription, notes)
             VALUES (:appointment_id, :date_prescription, :notes)"
        );

        return $stmt->execute([
            ':appointment_id' => $data['appointment_id'],
            ':date_prescription' => $data['date_prescription'],
            ':notes' => $data['notes']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE prescriptions 
             SET date_prescription = :date_prescription, notes = :notes
             WHERE id = :id"
        );

        return $stmt->execute([
            ':date_prescription' => $data['date_prescription'],
            ':notes' => $data['notes'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM prescriptions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count()
    {
        return $this->conn
            ->query("SELECT COUNT(*) FROM prescriptions")
            ->fetchColumn();
    }
}
