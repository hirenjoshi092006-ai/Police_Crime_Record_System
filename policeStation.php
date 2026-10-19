<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "police_crime_record";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// ADD police station
if (isset($_POST['station'])) {
    $id = (int)$_POST['station_id'];
    $name = trim($_POST['station_name']);
    $district = trim($_POST['district']);
    $state = trim($_POST['state']);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO policestations
        (station_id, station_name, district, state)
        VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "isss",
        $id, $name, $district, $state
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: policeStation.php");
        exit;
    } else {
        $message = "Error adding station: " .
                   mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// Delete Police Station and Related Records
if (isset($_POST['delete_station'])) {
    $id = (int) $_POST['station_id'];

    mysqli_begin_transaction($conn);

    try {
        // Delete complaints linked to cases of this station
        $sql = "DELETE FROM Complaints
                WHERE case_id IN (
                    SELECT case_id FROM (
                        SELECT case_id
                        FROM CriminalCases
                        WHERE station_id = $id
                    ) AS temp
                )";
        mysqli_query($conn, $sql);

        // Delete criminal cases linked to this station
        $sql = "DELETE FROM CriminalCases WHERE station_id = $id";
        mysqli_query($conn, $sql);

        // Delete officers linked to this station
        $sql = "DELETE FROM officers WHERE station_id = $id";
        mysqli_query($conn, $sql);

        // Delete the police station
        $sql = "DELETE FROM policestations WHERE station_id = $id";
        mysqli_query($conn, $sql);

        mysqli_commit($conn);

        header("Location: policeStation.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);
        $message = "Delete failed: " . $e->getMessage();
    }
}

// GET all police stations  
$result = mysqli_query($conn,
    "SELECT * FROM policestations"
);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Station Management</title>
</head>
<body>

<h1>Police Station Management</h1>

<a href="index.php">Return to Main Menu</a>

<p><?php echo htmlspecialchars($message); ?></p>

<h2>Police Station Records</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Station ID</th>
        <th>Station Name</th>
        <th>District</th>
        <th>State</th>
        <th>Actions</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['station_id']); ?></td>
                <td><?php echo htmlspecialchars($row['station_name']); ?></td>
                <td><?php echo htmlspecialchars($row['district']); ?></td>
                <td><?php echo htmlspecialchars($row['state']); ?></td>

                <td>
                    <a href="editStation.php?id=<?php
                        echo (int)$row['station_id'];
                    ?>">Edit</a>

                    <form method="post"
                          action="policeStation.php"
                          style="display:inline"
                          onsubmit="return confirm('Are you sure you want to delete this station?');">

                        <input type="hidden"
                               name="station_id"
                               value="<?php echo (int)$row['station_id']; ?>">

                        <button type="submit"
                                name="delete_station">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>

    <?php } else { ?>
        <tr>
            <td colspan="5">No police stations found.</td>
        </tr>
    <?php } ?>
</table>

<h2>Add Police Station</h2>

<form method="post" action="policeStation.php">
    <label>Station ID:</label>
    <input type="number" name="station_id" required>
    <br><br>

    <label>Station Name:</label>
    <input type="text" name="station_name" required>
    <br><br>

    <label>District:</label>
    <input type="text" name="district" required>
    <br><br>

    <label>State:</label>
    <input type="text" name="state" required>
    <br><br>

    <button type="submit" name="station">
        Add Police Station
    </button>
</form>

</body>
</html>