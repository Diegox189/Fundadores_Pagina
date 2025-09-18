<?php
// pages/teachers.php

// CREATE
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='create') {
  $stmt = $pdo->prepare("INSERT INTO teachers(name, subject, email) VALUES(?,?,?)");
  $stmt->execute([
    trim($_POST['name'] ?? ''),
    trim($_POST['subject'] ?? ''),
    trim($_POST['email'] ?? '')
  ]);
  header("Location: ?page=teachers"); exit;
}

// UPDATE
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='update') {
  $stmt = $pdo->prepare("UPDATE teachers SET name=?, subject=?, email=? WHERE id=?");
  $stmt->execute([
    trim($_POST['name'] ?? ''),
    trim($_POST['subject'] ?? ''),
    trim($_POST['email'] ?? ''),
    (int)$_POST['id']
  ]);
  header("Location: ?page=teachers"); exit;
}

// DELETE
if (($_GET['delete'] ?? '')!=='') {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM teachers WHERE id=?")->execute([$id]);
  header("Location: ?page=teachers"); exit;
}

// LIST
$rows = $pdo->query("SELECT * FROM teachers ORDER BY name")->fetchAll();

// EDIT
$edit = null;
if (($_GET['edit'] ?? '')!=='') {
  $id = (int)$_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id=?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}
?>
<section>
  <h1>Docentes</h1>

  <div class="grid-2">
    <div class="card">
      <h2><?= $edit ? 'Editar docente' : 'Nuevo docente' ?></h2>
      <form method="post" class="form-grid">
        <?php if ($edit): ?>
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <?php else: ?>
          <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <label>Nombre completo
          <input name="name" required value="<?= h($edit['name'] ?? '') ?>">
        </label>
        <label>Área/Asignatura
          <input name="subject" value="<?= h($edit['subject'] ?? '') ?>">
        </label>
        <label>Email
          <input type="email" name="email" value="<?= h($edit['email'] ?? '') ?>">
        </label>
        <div class="actions">
          <button type="submit" class="btn-primary">Guardar</button>
          <?php if ($edit): ?><a class="btn" href="?page=teachers">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="card">
      <h2>Listado</h2>
      <div class="table-responsive">
        <table>
          <thead><tr><th>Nombre</th><th>Asignatura</th><th>Email</th><th>Acciones</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= h($r['name']) ?></td>
              <td><?= h($r['subject']) ?></td>
              <td><?= h($r['email']) ?></td>
              <td class="nowrap">
                <a class="btn" href="?page=teachers&edit=<?= (int)$r['id'] ?>">Editar</a>
                <a class="btn-danger" href="?page=teachers&delete=<?= (int)$r['id'] ?>" onclick="return confirm('¿Eliminar docente?');">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
