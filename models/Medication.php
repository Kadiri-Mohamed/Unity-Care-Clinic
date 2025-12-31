<?php

class Medication
{
    private ?int $id;
    private int $prescriptionId;
    private string $nom;
    private string $dosage;

    public function __construct(
        ?int $id,
        int $prescriptionId,
        string $nom,
        string $dosage
    ) {
        $this->id = $id;
        $this->prescriptionId = $prescriptionId;
        $this->nom = $nom;
        $this->dosage = $dosage;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getPrescriptionId(): int { return $this->prescriptionId; }
    public function setPrescriptionId(int $prescriptionId): void { $this->prescriptionId = $prescriptionId; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getDosage(): string { return $this->dosage; }
    public function setDosage(string $dosage): void { $this->dosage = $dosage; }
}
