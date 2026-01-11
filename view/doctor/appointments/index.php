<?php ob_start(); ?>

<h1>My Appointments</h1>

<table>
    <tr>
        <th>Patient</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?= $a['patient_id'] ?></td>
            <td><?= $a['appointment_date'] ?></td>
            <td><?= $a['status'] ?></td>
            <td>
                <a href="/routes/doctor.php?action=appointment_show&id=<?= $a['id'] ?>">View</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
