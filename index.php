<?php
require_once 'database/MySqlConnection.php';
require_once 'DisplayData.php';

$dbConnection = new MySqlConnection();
$filterAndDisplay = new DisplayData($dbConnection);

$employeeName = isset($_POST['employee_name']) ? $_POST['employee_name'] : '';
$eventName = isset($_POST['event_name']) ? $_POST['event_name'] : '';
$eventDateFilter = isset($_POST['date']) ? $_POST['date'] : '';
$totalPrice = 0.00;

// Build SQL query based on filters
$sql = "SELECT p.participation_id, e.name AS employee_name, e.email, ev.name AS event_name, p.participation_fee, p.event_date, p.version
FROM participations p INNER JOIN employees e ON p.employee_id = e.id
INNER JOIN events ev ON p.event_id = ev.id
WHERE 1";

if (!empty($employeeName)) {
    $sql .= " AND e.name LIKE '%$employeeName%'";
}

if (!empty($eventName)) {
    $sql .= " AND ev.name LIKE '%$eventName%'";
}

if (!empty($eventDateFilter)) {
    $eventDateFilter = date('Y-m-d', strtotime($eventDateFilter));
    $sql .= " AND DATE(event_date) = '$eventDateFilter'";
}
$sql .=';';
$result = $filterAndDisplay->filterData($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Participation Filter</title>
</head>
<body>
    <h1>Event Participation Filter</h1>

    <!-- Filter form -->
    <form method="POST">
        <label for="employee_name">Employee Name:</label>
        <input type="text" name="employee_name" id="employee_name" value="<?= $employeeName ?>">
        
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" id="event_name" value="<?= $eventName ?>">
        
        <label for="date">Event Date:</label>
        <input type="text" name="date" id="date" placeholder="YYYY-MM-DD" value="<?= $eventDateFilter ?>">
        
        <button type="submit">Apply Filters</button>
    </form>

    <!-- Display filtered results in a table -->
    <?php if ($result->num_rows > 0) : ?>
    <table border="1" style="margin-top: 30px;">
        <thead>
            <tr>
                <th>Participation ID</th>
                <th>Employee Name</th>
                <th>Employee Mail</th>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Version</th>
                <th>Participation Fee</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['participation_id'] ?></td>
                    <td><?= $row['employee_name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['event_name'] ?></td>
                    <td><?= $row['event_date'] ?></td>
                    <td><?= $row['version'] ?></td>
                    <td><?= $row['participation_fee'] ?></td>
                </tr>
            <?php 
                $totalPrice += floatval($row['participation_fee']);
            endwhile; ?>
            <tr><td colspan="6">Total Price</td><td><?php echo number_format($totalPrice, 2);?></td></tr>
        </tbody>
    </table>
    <?php else: ?>
        <div class="" style="margin-top: 30px;">No information available at the moment!</div>
   <?php endif; ?>
</body>
</html>
