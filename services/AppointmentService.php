<?php

class AppointmentService
{
    private AppointmentRepository $appointmentRepository;

    public function __construct()
    {
        $this->appointmentRepository = new AppointmentRepository();
    }

    public function createAppointment(array $data): bool
    {
        if (!Validator::required($data['date_rdv'])) {
            throw new Exception("Date is required");
        }

        if (!Validator::required($data['heure'])) {
            throw new Exception("Time is required");
        }

        return $this->appointmentRepository->create($data);
    }
}
