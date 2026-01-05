<?php

class PatientService
{
    private UserService $userService;
    private PatientRepository $patientRepo;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->patientRepo = new PatientRepository();
    }

    public function create(array $data): bool
    {
        if (!Validator::required($data['date_naissance'] ?? null)) {
            throw new Exception("Date de naissance is required");
        }

        $data['role'] = 'Patient';

        $userId = $this->userService->create($data);

        return $this->patientRepo->create([
            'id' => $userId,
            'date_naissance' => $data['date_naissance']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        if (!Validator::required($data['date_naissance'] ?? null)) {
            throw new Exception("Date de naissance is required");
        }

        $data['role'] = 'Patient';

        $this->userService->update($id, $data);

        return $this->patientRepo->update($id, [
            'date_naissance' => $data['date_naissance']
        ]);
    }

    public function delete(int $id): bool
    {
        $this->patientRepo->delete($id);
        return (new UserRepository())->delete($id);
    }
}
