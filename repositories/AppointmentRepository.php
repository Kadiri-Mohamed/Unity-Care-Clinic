<?php

class AppointmentRepository implements BaseReposetry
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM appointments");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO appointments (patient_id, doctor_id, date_rdv, heure, status)
             VALUES (:patient_id, :doctor_id, :date_rdv, :heure, :status)"
        );

        return $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':doctor_id' => $data['doctor_id'],
            ':date_rdv' => $data['date_rdv'],
            ':heure' => $data['heure'],
            ':status' => $data['status']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE appointments 
             SET date_rdv = :date_rdv, heure = :heure, status = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            ':date_rdv' => $data['date_rdv'],
            ':heure' => $data['heure'],
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM appointments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM appointments");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function findByDoctorId($doctorId)
{
    $sql = "SELECT * FROM appointments WHERE doctor_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$doctorId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function findByIdAndDoctor($id, $doctorId)
{
    $sql = "SELECT * FROM appointments WHERE id = ? AND doctor_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id, $doctorId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updateStatusByDoctor($id, $status, $doctorId)
{
    $sql = "UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$status, $id, $doctorId]);
}

}
