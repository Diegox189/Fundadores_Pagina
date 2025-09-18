<?php
// pages/students.php

// CREATE
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='create') {
  $stmt = $pdo->prepare("INSERT INTO students(document, first_name, last_name, grade, email, phone)
                         VALUES(?,?,?,?,?,?)");
  $stmt->execute([
    trim($_POST['document'] ?? ''),
    trim($_POST['first_name'] ?? ''),
    trim($_POST['last_name'] ?? ''),
    trim($_POST['grade'] ?? ''),
    trim($_POST['email'] ?? ''),
    trim($_POST['phone'] ?? '')
  ]);
  header("Location: ?page=students"); exit;
}

// UPDATE
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='update') {
  $stmt = $pdo->prepare("UPDATE students SET document=?, first_name=?, last_name=?, grade=?, email=?, phone=? WHERE id=?");
  $stmt->execute([
    trim($_POST['document'] ?? ''),
    trim($_POST['first_name'] ?? ''),
    trim($_POST['last_name'] ?? ''),
    trim($_POST['grade'] ?? ''),
    trim($_POST['email'] ?? ''),
    trim($_POST['phone'] ?? ''),
    (int)$_POST['id']
  ]);
  header("Location: ?page=students"); exit;
}

// DELETE
if (($_GET['delete'] ?? '')!=='') {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM students WHERE id=?")->execute([$id]);
  header("Location: ?page=students"); exit;
}

// LIST
$rows = $pdo->query("SELECT * FROM students ORDER BY last_name, first_name")->fetchAll();

// EDIT (si aplica)
$edit = null;
if (($_GET['edit'] ?? '')!=='') {
  $id = (int)$_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM students WHERE id=?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}
?>
<section>
  <h1>Estudiantes</h1>

  <div class="grid-2">
    <div class="card">
      <h2><?= $edit ? 'Editar estudiante' : 'Nuevo estudiante' ?></h2>
      <form method="post" class="form-grid">
        <?php if ($edit): ?>
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <?php else: ?>
          <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <label>Documento
          <input name="document" required value="<?= h($edit['document'] ?? '') ?>">
        </label>
        <label>Nombres
          <input name="first_name" required value="<?= h($edit['first_name'] ?? '') ?>">
        </label>
        <label>Apellidos
          <input name="last_name" required value="<?= h($edit['last_name'] ?? '') ?>">
        </label>
        <label>Grado/Grupo
          <input name="grade" required value="<?= h($edit['grade'] ?? '') ?>">
        </label>
        <label>Email
          <input type="email" name="email" value="<?= h($edit['email'] ?? '') ?>">
        </label>
        <label>Teléfono
          <input name="phone" value="<?= h($edit['phone'] ?? '') ?>">
        </label>
        <div class="actions">
          <button type="submit" class="btn-primary">Guardar</button>
          <?php if ($edit): ?>
            <a class="btn" href="?page=students">Cancelar</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <div class="card">
      <h2>Listado</h2>
      <div class="table-responsive">
        <table>
          <thead><tr>
            <th>Documento</th><th>Estudiante</th><th>Grado</th><th>Email</th><th>Teléfono</th><th>Acciones</th>
          </tr></thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= h($r['document']) ?></td>
              <td><?= h($r['last_name'].', '.$r['first_name']) ?></td>
              <td><?= h($r['grade']) ?></td>
              <td><?= h($r['email']) ?></td>
              <td><?= h($r['phone']) ?></td>
              <td class="nowrap">
                <a class="btn" href="?page=students&edit=<?= (int)$r['id'] ?>">Editar</a>
                <a class="btn-danger" href="?page=students&delete=<?= (int)$r['id'] ?>" onclick="return confirm('¿Eliminar estudiante?');">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
