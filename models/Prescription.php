<?php

class Prescription
{
    private ?int $id;
    private int $appointmentId;
    private string $date;
    private string $notes;

    public function __construct(
        ?int $id,
        int $appointmentId,
        string $date,
        string $notes
    ) {
        $this->id = $id;
        $this->appointmentId = $appointmentId;
        $this->date = $date;
        $this->notes = $notes;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getAppointmentId(): int { return $this->appointmentId; }
    public function setAppointmentId(int $appointmentId): void { $this->appointmentId = $appointmentId; }

    public function getDate(): string { return $this->date; }
    public function setDate(string $date): void { $this->date = $date; }

    public function getNotes(): string { return $this->notes; }
    public function setNotes(string $notes): void { $this->notes = $notes; }
}
