# 🏥 Unity Care Clinic – PHP Web Application

## 📌 Présentation

**Unity Care Clinic** est une application web développée en **PHP orienté objet** destinée à la gestion d'une clinique médicale.  
Elle permet l'authentification des utilisateurs et la gestion des médecins, patients, rendez-vous, prescriptions et médicaments.

Le projet suit une architecture **MVC simplifiée** et applique le **Repository Pattern** pour séparer la logique métier de l'accès aux données.

---

## 🎯 Fonctionnalités

### 🔐 Authentification
- Connexion des utilisateurs (Admin, Doctor, Patient)
- Déconnexion sécurisée
- Gestion de sessions avec régénération d'ID
- Accès aux fonctionnalités uniquement après connexion

### 👨‍💼 Administrateur
- Gestion des médecins (CRUD)
- Gestion des patients (CRUD)
- Consultation de tous les rendez-vous

### 👨‍⚕️ Médecin
- Consultation de ses rendez-vous
- Consultation des dossiers patients
- Création de prescriptions
- Ajout de médicaments

### 🧍 Patient
- Prise de rendez-vous
- Consultation de ses rendez-vous
- Consultation de ses prescriptions

---

## 🧱 Architecture du projet

```
Unity-Care-Clinic/
├── config/
│   ├── Database.php          # Connexion PDO à MySQL
│   └── Session.php           # Gestion des sessions
├── models/
│   ├── User.php
│   ├── Doctor.php
│   ├── Patient.php
│   ├── Appointment.php
│   ├── Prescription.php
│   └── Medication.php
├── repositories/
│   ├── BaseReposetry.php     # Interface du Repository Pattern
│   ├── UserRepository.php
│   ├── DoctorRepository.php
│   ├── PatientRepository.php
│   ├── AppointmentRepository.php
│   ├── PrescriptionRepository.php
│   └── MedicationRepository.php
├── services/
│   ├── AuthService.php
│   ├── UserService.php
│   ├── DoctorService.php
│   ├── PatientService.php
│   └── AppointmentService.php
├── view/
│   ├── admin/dashboard.php
│   ├── doctor/dashboard.php
│   └── patient/dashboard.php
├── routes/
│   └── router.php            # Routeur principal avec vérification des rôles
├── auth/
│   ├── login.php             # Page de connexion
│   └── logout.php            # Déconnexion
├── public/
│   └── style.css             # Styles Bootstrap personnalisés
├── utils/
│   └── Validator.php         # Validation des données
├── docs/
│   └── unity_care_clinic.sql # Script de base de données
├── autoload.php              # Autoloader PSR-4
├── index.php                 # Point d'entrée
└── .env                      # Variables d'environnement
```

---

## 🗄️ Base de données

**SGBD** : MySQL  
**Tables** :
- `users` - Tous les utilisateurs (Admin, Doctor, Patient)
- `doctors` - Spécialités des médecins
- `patients` - Dates de naissance des patients
- `appointments` - Rendez-vous
- `prescriptions` - Prescriptions médicales
- `medications` - Médicaments associés aux prescriptions

**Import** : Exécutez [docs/unity_care_clinic.sql](docs/unity_care_clinic.sql)

---

## 🔐 Sécurité

- ✅ Mots de passe hashés avec `password_hash()` (PASSWORD_DEFAULT)
- ✅ Validation des données avec [`Validator`](utils/Validator.php)
- ✅ Protection contre l'accès non autorisé via vérification des rôles
- ✅ Utilisation de PDO pour prévenir les injections SQL
- ✅ Régénération d'ID de session après connexion
- ✅ Suppression sécurisée des sessions à la déconnexion

---

## 🚀 Installation

1. **Installer les dépendances**
   - PHP ≥ 8.0
   - MySQL / MariaDB
   - Serveur local (XAMPP / WAMP / LAMP)

2. **Créer la base de données**
   ```bash
   mysql -u root -p < docs/unity_care_clinic.sql
   ```

3. **Configurer la connexion**
   - Modifier [config/Database.php](config/Database.php) avec vos identifiants MySQL

4. **Lancer le projet**
   ```
   http://localhost/Unity-Care-Clinic/
   ```

---

## 📋 Flux de l'application

1. **L'utilisateur accède** à `index.php` → Redirection vers [auth/login.php](auth/login.php)
2. **Connexion** via [`AuthService`](services/AuthService.php)
3. **Vérification du rôle** dans [routes/router.php](routes/router.php)
4. **Redirection** vers le tableau de bord approprié
5. **Déconnexion** via [auth/logout.php](auth/logout.php)

---

## 🛠️ Technologies

- **Backend** : PHP 8.0+ (POO)
- **Base de données** : MySQL / MariaDB avec PDO
- **Frontend** : HTML5, Bootstrap 5, CSS3
- **Design Pattern** : Repository Pattern, Service Layer
- **Validation** : Classes métier personnalisées

---

## 📝 Exemple d'utilisation

### Créer un patient
```php
$patientService = new PatientService();
$patientService->create([
    'nom' => 'Alaoui',
    'prenom' => 'Sara',
    'email' => 'sara@example.com',
    'username' => 'salaoui',
    'password' => 'password123',
    'telephone' => '0612345678',
    'date_naissance' => '1998-04-12'
]);
```

### Connexion utilisateur
```php
$authService = new AuthService();
if ($authService->login('sara@example.com', 'password123')) {
    // Redirection vers le tableau de bord patient
}
```

---

## 👤 Auteur

**Nom** : Kadiri Mohamed  
**Année** : 2025–2026  
**Contexte** : Projet académique pour l'apprentissage du développement backend en PHP

---

## 📄 Licence

Projet éducatif - Utilisation libre pour fins d'apprentissage