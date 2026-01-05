<?php

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function createUser(array $data): bool
    {
        if (!Validator::required($data['nom'])) {
            throw new Exception("Nom is required");
        }

        if (!Validator::email($data['email'])) {
            throw new Exception("Invalid email");
        }

        if (!Validator::minLength($data['password'], 6)) {
            throw new Exception("Password too short");
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->userRepository->create($data);
    }
}
