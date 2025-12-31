<?php

class Appointment
{
    private ?int $id;
    private int $patientId;
    private int $doctorId;
    private string $date;
    private string $heure;
    private string $status;

    public function __construct(
        ?int $id,
        int $patientId,
        int $doctorId,
        string $date,
        string $heure,
        string $status
    ) {
        $this->id = $id;
        $this->patientId = $patientId;
        $this->doctorId = $doctorId;
        $this->date = $date;
        $this->heure = $heure;
        $this->status = $status;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getPatientId(): int { return $this->patientId; }
    public function setPatientId(int $patientId): void { $this->patientId = $patientId; }

    public function getDoctorId(): int { return $this->doctorId; }
    public function setDoctorId(int $doctorId): void { $this->doctorId = $doctorId; }

    public function getDate(): string { return $this->date; }
    public function setDate(string $date): void { $this->date = $date; }

    public function getHeure(): string { return $this->heure; }
    public function setHeure(string $heure): void { $this->heure = $heure; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }
}
