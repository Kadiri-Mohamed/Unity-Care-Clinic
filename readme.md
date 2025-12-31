# 🏥 Unity Care Clinic – PHP Web Application

## 📌 Présentation

**Unity Care Clinic** est une application web développée en **PHP orienté objet** destinée à la gestion d’une clinique médicale.  
Elle permet l’authentification des utilisateurs et la gestion des médecins, patients, rendez-vous, prescriptions et médicaments.

Le projet suit une architecture **MVC simplifiée** et applique le **Repository Pattern** pour séparer la logique métier de l’accès aux données.

---

## 🎯 Fonctionnalités

### 🔐 Authentification
- Connexion des utilisateurs (Admin, Doctor, Patient)
- Déconnexion sécurisée
- Accès aux fonctionnalités uniquement après connexion

### 👨‍💼 Administrateur
- Gestion des médecins
- Gestion des patients
- Consultation de tous les rendez-vous

### 👨‍⚕️ Médecin
- Consultation de ses rendez-vous
- Consultation du dossier patient
- Création de prescriptions
- Ajout de médicaments

### 🧍 Patient
- Prise de rendez-vous
- Consultation de ses rendez-vous
- Consultation de ses prescriptions

---

## 🧱 Architecture du projet

classes/
├── models/
│ ├── User.php
│ ├── Admin.php
│ ├── Doctor.php
│ ├── Patient.php
│ ├── Appointment.php
│ ├── Prescription.php
│ └── Medication.php
│
└── repositories/
├── BaseRepository.php
├── PatientRepository.php
├── DoctorRepository.php
├── AppointmentRepository.php
├── PrescriptionRepository.php
└── MedicationRepository.php


---

## Base de données
- SGBD : MySQL
- Tables : users, doctors, patients, appointments, prescriptions, medications
- Modélisation réalisée avec dbdiagram.io

---

## Installation
1. Installer PHP (≥ 8.0) et MySQL  
2. Créer la base de données `unity_care_clinic`
3. Importer le script SQL fourni
4. Configurer la connexion à la base de données
5. Lancer le projet sur un serveur local (XAMPP / WAMP)

---

## Sécurité
- Mots de passe hashés
- Validation des données
- Utilisation de PDO pour l’accès à la base de données

---

## Technologies
- PHP (POO)
- MySQL
- PDO
- UML / ERD

---

## Contexte
Projet réalisé dans un cadre académique pour l’apprentissage de la conception et du développement backend en PHP.

---

## Auteur
Nom : [Kadiri-Mohamed]  
Année : 2025–2026