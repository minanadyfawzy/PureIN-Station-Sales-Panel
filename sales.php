<?php
session_start();
require __DIR__ . '/db.php';

if (empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$isAdmin = (bool) $user['is_admin'];

/*
 * Head office can choose any station.
 * Station managers can only access their assigned station.
 */
if ($isAdmin) {
    $station = filter_input(INPUT_GET, 'station', FILTER_VALIDATE_INT);

    if ($station === false || $station === null) {
        $station = 1;
    }
} else {
    $station = (int) $user['station_id'];
}

/*
 * Make sure the requested station actually exists.
 */
$stationStmt = $db->prepare(
    'SELECT id, name FROM stations WHERE id = ?'
);
$stationStmt->execute([$station]);
$currentStation = $stationStmt->fetch();

if (!$currentStation) {
    http_response_code(404);
    exit('Station not found.');
}

/*
 * Get sales using a prepared statement.
 */
$salesStmt = $db->prepare(
    'SELECT id, sold_at, pump, fuel, litres, amount
     FROM sales
     WHERE station_id = ?
     ORDER BY sold_at DESC'
);
$salesStmt->execute([$station]);
$sales = $salesStmt->fetchAll();

/*
 * Only head office needs the station selector.
 */
if ($isAdmin) {
    $stations = $db
        ->query('SELECT id, name FROM stations ORDER BY id')
        ->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>المبيعات - لوحة الوقود</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="top">
    <span>
      مسجل الدخول:
      <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
    </span>
    |
    <a href="logout.php">تسجيل الخروج</a>
  </div>

  <h1>المبيعات</h1>

  <p>
    المحطة:
    <?= htmlspecialchars($currentStation['name'], ENT_QUOTES, 'UTF-8') ?>
  </p>

  <?php if ($isAdmin): ?>
    <form method="get">
      <label for="station">اختيار المحطة</label>

      <select name="station" id="station">
        <?php foreach ($stations as $s): ?>
          <option
            value="<?= (int) $s['id'] ?>"
            <?= (int) $s['id'] === $station ? 'selected' : '' ?>
          >
            <?= htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">عرض</button>
    </form>
  <?php endif; ?>

  <div class="sales-wrapper">
    <table class="sales">
      <thead>
        <tr>
          <th>الوقت</th>
          <th>المضخة</th>
          <th>نوع الوقود</th>
          <th>اللترات</th>
          <th>المبلغ</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($sales as $row): ?>
          <tr>
            <td data-label="الوقت">
              <?= htmlspecialchars($row['sold_at'], ENT_QUOTES, 'UTF-8') ?>
            </td>

            <td data-label="المضخة">
              <?= (int) $row['pump'] ?>
            </td>

            <td data-label="نوع الوقود">
              <?= htmlspecialchars($row['fuel'], ENT_QUOTES, 'UTF-8') ?>
            </td>

            <td data-label="اللترات">
              <?= number_format((float) $row['litres'], 2) ?>
            </td>

            <td data-label="المبلغ">
              <?= number_format((float) $row['amount'], 2) ?> ر.س
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
