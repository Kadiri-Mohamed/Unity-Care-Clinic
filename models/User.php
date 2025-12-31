<?php

class User
{
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $username;
    private string $password;
    private string $telephone;
    private string $role;

    public function __construct(
        ?int $id,
        string $nom,
        string $prenom,
        string $email,
        string $username,
        string $password,
        string $telephone,
        string $role
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->telephone = $telephone;
        $this->role = $role;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): void { $this->password = $password; }

    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $telephone): void { $this->telephone = $telephone; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }
}
