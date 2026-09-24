<?php
session_start();
require __DIR__ . '/db.php';

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare(
        'SELECT id, username, password, station_id, is_admin
         FROM users
         WHERE username = ?'
    );
    $stmt->execute([$username]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        // Store only the information needed by the application.
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'station_id' => $user['station_id'],
            'is_admin' => (int) $user['is_admin'],
        ];

        header('Location: sales.php');
        exit;
    }

    $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>لوحة الوقود</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="box">
    <h1>لوحة الوقود</h1>

    <?php if ($error): ?>
      <p class="error">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endif; ?>

    <form method="post">
      <p>
        <label>
          اسم المستخدم<br>
          <input
            name="username"
            value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="username"
            required
          >
        </label>
      </p>

      <p>
        <label>
          كلمة المرور<br>
          <input
            name="password"
            type="password"
            autocomplete="current-password"
            required
          >
        </label>
      </p>

      <p>
        <button type="submit">تسجيل الدخول</button>
      </p>
    </form>
  </div>
</body>
</html>
