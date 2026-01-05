<?php

class UserService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function validate(array $data): void
    {
        if (!Validator::required($data['nom'] ?? null)) {
            throw new Exception("Nom is required");
        }

        if (!Validator::required($data['prenom'] ?? null)) {
            throw new Exception("Prenom is required");
        }

        if (!Validator::email($data['email'] ?? null)) {
            throw new Exception("Invalid email");
        }

        if (!Validator::required($data['username'] ?? null)) {
            throw new Exception("Username is required");
        }

        if (!Validator::required($data['role'] ?? null)) {
            throw new Exception("Role is required");
        }
    }

    public function create(array $data): int
    {
        if (!Validator::minLength($data['password'] ?? '', 6)) {
            throw new Exception("Password too short");
        }

        $this->validate($data);

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->userRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $this->validate($data);

        if (!empty($data['password'])) {
            if (!Validator::minLength($data['password'], 6)) {
                throw new Exception("Password too short");
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($id, $data);
    }
}
