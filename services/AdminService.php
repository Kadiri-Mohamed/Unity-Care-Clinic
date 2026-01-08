<?php

class AdminService
{
    private UserRepository $userRepo;
    private DoctorRepository $doctorRepo;
    private PatientRepository $patientRepo;
    private AppointmentRepository $appointmentRepo;
    private MedicationRepository $medicationRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->patientRepo = new PatientRepository();
        $this->appointmentRepo = new AppointmentRepository();
        $this->medicationRepo = new MedicationRepository();
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

    // ==================== Medication Management ====================

    public function getAllMedications(): array
    {
        $sql = "
            SELECT 
                m.id,
                m.nom,
                m.dosage,
                m.prescription_id,
                CONCAT(pd.nom, ' ', pd.prenom) as patient_name,
                CONCAT(dd.nom, ' ', dd.prenom) as doctor_name,
                p.date_prescription
            FROM medications m
            LEFT JOIN prescriptions p ON m.prescription_id = p.id
            LEFT JOIN patients pat ON p.id = pat.id
            LEFT JOIN users pd ON pat.id = pd.id
            LEFT JOIN doctors doc ON p.id = doc.id
            LEFT JOIN users dd ON doc.id = dd.id
            ORDER BY m.id DESC
        ";

        $database = new Database();
        $conn = $database->dbConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMedicationById(int $id): ?array
    {
        return $this->medicationRepo->getById($id);
    }

    public function createMedication(array $data): array
    {
        try {
            $medicationData = [
                'prescription_id' => $data['prescription_id'],
                'nom' => $data['nom'],
                'dosage' => $data['dosage']
            ];

            $this->medicationRepo->create($medicationData);
            return ['success' => true, 'message' => 'Medication created successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function updateMedication(int $id, array $data): array
    {
        try {
            $medicationData = [
                'nom' => $data['nom'],
                'dosage' => $data['dosage']
            ];

            $this->medicationRepo->update($id, $medicationData);
            return ['success' => true, 'message' => 'Medication updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function deleteMedication(int $id): array
    {
        try {
            $this->medicationRepo->delete($id);
            return ['success' => true, 'message' => 'Medication deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function getAllPrescriptions(): array
    {
        $sql = "
            SELECT 
                p.id,
                p.date_prescription,
                CONCAT(pd.nom, ' ', pd.prenom) as patient_name,
                CONCAT(dd.nom, ' ', dd.prenom) as doctor_name
            FROM prescriptions p
            INNER JOIN patients pat ON p.id = pat.id
            INNER JOIN users pd ON pat.id = pd.id
            INNER JOIN doctors doc ON p.id = doc.id
            INNER JOIN users dd ON doc.id = dd.id
            ORDER BY p.date_prescription DESC
        ";

        $database = new Database();
        $conn = $database->dbConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
