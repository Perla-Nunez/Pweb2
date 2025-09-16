<?php require 'partials/head.php'; ?>
<?php require 'partials/header.php'; ?>

<div class="container">
    <h2 class="text-2xl font-bold mb-4">Lista de Usuarios</h2>

    <?php if (!empty($error)): ?>
        <div class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Nombre</th>
                <th class="border p-2">Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="border p-2"><?= htmlspecialchars($user['idUsuario']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($user['Nombre']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($user['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="border p-2 text-center">No se encontraron usuarios.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


