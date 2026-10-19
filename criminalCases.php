
<?php
$conn = mysqli_connect("localhost", "root", "", "police_crime_record");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// Add case
if (isset($_POST['add'])) {
    $id = (int)$_POST['case_id'];
    $station = (int)$_POST['station_id'];
    $type = $_POST['case_type'];
    $date = $_POST['filing_date'];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO CriminalCases (case_id, station_id, case_type, filing_date)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "iiss", $id, $station, $type, $date);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: criminalCases.php");
        exit;
    } else {
        $message = "Error adding case: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// Delete case and linked complaints
if (isset($_POST['delete_case'])) {
    $id = (int)$_POST['case_id'];

    mysqli_begin_transaction($conn);

    try {
        // Delete complaints linked to this case
        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM Complaints WHERE case_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete criminal case
        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM CriminalCases WHERE case_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);

        header("Location: criminalCases.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);
        $message = "Delete failed: " . $e->getMessage();
    }
}

// Fetch cases for display
$result = mysqli_query(
    $conn,
    "SELECT * FROM CriminalCases ORDER BY case_id"
);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Criminal Cases</title>
</head>
<body>

<h1>Criminal Cases Management</h1>

<a href="index.php">Return to Main Menu</a>

<p style="color:red;">
    <?php echo htmlspecialchars($message); ?>
</p>

<h2>Case Records</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Case ID</th>
    <th>Station ID</th>
    <th>Case Type</th>
    <th>Filing Date</th>
    <th>Actions</th>
</tr>

<?php if (mysqli_num_rows($result) > 0) { ?>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['case_id']; ?></td>
        <td><?php echo $row['station_id']; ?></td>
        <td><?php echo htmlspecialchars($row['case_type']); ?></td>
        <td><?php echo htmlspecialchars($row['filing_date']); ?></td>
        <td>
            <a href="editCase.php?id=<?php echo $row['case_id']; ?>">
                Edit
            </a>

            <form method="post" action="criminalCases.php"
                  style="display:inline"
                  onsubmit="return confirm('Delete this case and all its complaints?');">

                <input type="hidden" name="case_id"
                       value="<?php echo (int)$row['case_id']; ?>">

                <button type="submit" name="delete_case">
                    Delete
                </button>
            </form>
        </td>
    </tr>
    <?php } ?>

<?php } else { ?>

    <tr>
        <td colspan="5">No criminal cases found.</td>
    </tr>

<?php } ?>

</table>

<h2>Add Criminal Case</h2>

<form method="post" action="criminalCases.php">
    Case ID:
    <input type="number" name="case_id" required>
    <br><br>

    Station ID:
    <input type="number" name="station_id" required>
    <br><br>

    Case Type:
    <input type="text" name="case_type" required>
    <br><br>

    Filing Date:
    <input type="date" name="filing_date" required>
    <br><br>

    <button type="submit" name="add">Add Case</button>
</form>

</body>
</html>
