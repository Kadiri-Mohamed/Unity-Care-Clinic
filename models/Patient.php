<?php

class Patient
{
    private int $userId;
    private string $dateNaissance;

    public function __construct(int $userId, string $dateNaissance)
    {
        $this->userId = $userId;
        $this->dateNaissance = $dateNaissance;
    }

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function getDateNaissance(): string { return $this->dateNaissance; }
    public function setDateNaissance(string $dateNaissance): void { $this->dateNaissance = $dateNaissance; }
}
