<?php

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->getByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        Session::regenerate();
        Session::set('user', $user);
        Session::set('user_log_id', );
        Session::set('logged_in', true);
        Session::set('user_role' , $user['role']);
        return true;
    }
}
