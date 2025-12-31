<?php

class Doctor
{
    private int $userId;
    private string $specialite;

    public function __construct(int $userId, string $specialite)
    {
        $this->userId = $userId;
        $this->specialite = $specialite;
    }

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function getSpecialite(): string { return $this->specialite; }
    public function setSpecialite(string $specialite): void { $this->specialite = $specialite; }
}
