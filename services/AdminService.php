<?php

class AdminService
{
    private UserRepository $userRepo;
    private DoctorRepository $doctorRepo;
    private PatientRepository $patientRepo;
    private AppointmentRepository $appointmentRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->patientRepo = new PatientRepository();
        $this->appointmentRepo = new AppointmentRepository();
    }

    public function getStatistics(): array
    {
        return [
            'doctors' => $this->doctorRepo->count(),
            'patients' => $this->patientRepo->count(),
            'appointments' => $this->appointmentRepo->count()
        ];
    }

    public function getAllDoctors(): array
    {
        return $this->doctorRepo->getAll();
    }

    public function getAllPatients(): array
    {
        return $this->patientRepo->getAll();
    }

    public function getAllAppointments(): array
    {
        $sql = "
            SELECT 
                a.id,
                a.date_rdv,
                a.heure,
                a.status,
                CONCAT(pd.nom, ' ', pd.prenom) as patient_name,
                CONCAT(dd.nom, ' ', dd.prenom) as doctor_name,
                ds.specialite
            FROM appointments a
            INNER JOIN patients p ON a.patient_id = p.id
            INNER JOIN users pd ON p.id = pd.id
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users dd ON d.id = dd.id
            INNER JOIN doctors ds ON d.id = ds.id
            ORDER BY a.date_rdv DESC, a.heure DESC
        ";

        $database = new Database();
        $conn = $database->dbConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createDoctor(array $data): array
    {
        try {
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Create user
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => $hashedPassword,
                'telephone' => $data['telephone'],
                'role' => 'doctor'
            ];

            $this->userRepo->create($userData);

            // Get the last inserted user ID
            $database = new Database();
            $conn = $database->dbConnection();
            $userId = $conn->lastInsertId();

            // Create doctor record
            $doctorData = [
                'id' => $userId,
                'specialite' => $data['specialite']
            ];

            $this->doctorRepo->create($doctorData);

            return ['success' => true, 'message' => 'Doctor created successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateDoctor(int $id, array $data): array
    {
        try {
            // Update user
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'username' => $data['username'],
                'telephone' => $data['telephone'],
                'role' => 'doctor'
            ];

            $this->userRepo->update($id, $userData);

            // Update doctor record
            $doctorData = [
                'specialite' => $data['specialite']
            ];

            $this->doctorRepo->update($id, $doctorData);

            return ['success' => true, 'message' => 'Doctor updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function deleteDoctor(int $id): array
    {
        try {
            $this->doctorRepo->delete($id);
            $this->userRepo->delete($id);
            return ['success' => true, 'message' => 'Doctor deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function createPatient(array $data): array
    {
        try {
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Create user
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => $hashedPassword,
                'telephone' => $data['telephone'],
                'role' => 'patient'
            ];

            $this->userRepo->create($userData);

            // Get the last inserted user ID
            $database = new Database();
            $conn = $database->dbConnection();
            $userId = $conn->lastInsertId();

            // Create patient record
            $patientData = [
                'id' => $userId,
                'date_naissance' => $data['date_naissance']
            ];

            $this->patientRepo->create($patientData);

            return ['success' => true, 'message' => 'Patient created successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updatePatient(int $id, array $data): array
    {
        try {
            // Update user
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'username' => $data['username'],
                'telephone' => $data['telephone'],
                'role' => 'patient'
            ];

            $this->userRepo->update($id, $userData);

            // Update patient record
            $patientData = [
                'date_naissance' => $data['date_naissance']
            ];

            $this->patientRepo->update($id, $patientData);

            return ['success' => true, 'message' => 'Patient updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function deletePatient(int $id): array
    {
        try {
            $this->patientRepo->delete($id);
            $this->userRepo->delete($id);
            return ['success' => true, 'message' => 'Patient deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function getDoctorById(int $id): ?array
    {
        return $this->doctorRepo->getById($id);
    }

    public function getPatientById(int $id): ?array
    {
        return $this->patientRepo->getById($id);
    }

    public function deleteAppointment(int $id): array
    {
        try {
            $this->appointmentRepo->delete($id);
            return ['success' => true, 'message' => 'Appointment deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateAppointmentStatus(int $id, string $status): array
    {
        try {
            $appointment = $this->appointmentRepo->getById($id);
            if (!$appointment) {
                return ['success' => false, 'message' => 'Appointment not found'];
            }

            $data = [
                'date_rdv' => $appointment['date_rdv'],
                'heure' => $appointment['heure'],
                'status' => $status
            ];

            $this->appointmentRepo->update($id, $data);
            return ['success' => true, 'message' => 'Appointment status updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
