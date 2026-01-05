<?php

class DoctorService
{
    private UserService $userService;
    private DoctorRepository $doctorRepo;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->doctorRepo = new DoctorRepository();
    }

    public function create(array $data): bool
    {
        if (!Validator::required($data['specialite'] ?? null)) {
            throw new Exception("Specialite is required");
        }

        $data['role'] = 'Doctor';

        $userId = $this->userService->create($data);

        return $this->doctorRepo->create([
            'id' => $userId,
            'specialite' => $data['specialite']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        if (!Validator::required($data['specialite'] ?? null)) {
            throw new Exception("Specialite is required");
        }

        $data['role'] = 'Doctor';

        $this->userService->update($id, $data);

        return $this->doctorRepo->update($id, [
            'specialite' => $data['specialite']
        ]);
    }

    public function delete(int $id): bool
    {
        $this->doctorRepo->delete($id);
        return (new UserRepository())->delete($id);
    }
}
