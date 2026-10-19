<?php
$conn = mysqli_connect("localhost", "root", "", "police_crime_record");

$id = (int)$_GET['id'];

if (isset($_POST['update'])) {
    $case = (int)$_POST['case_id'];
    $name = $_POST['complainant_name'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare($conn,
        "UPDATE Complaints
         SET case_id=?, complainant_name=?, status=?
         WHERE complaint_id=?");

    mysqli_stmt_bind_param($stmt, "issi", $case, $name, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: complaints.php");
        exit;
    } else {
        echo "Error updating complaint: " . mysqli_stmt_error($stmt);
    }
}

$stmt = mysqli_prepare($conn,
    "SELECT * FROM Complaints WHERE complaint_id=?");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Complaint not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Complaint</title>
</head>
<body>

<h2>Edit Complaint</h2>

<form method="post">
    Case ID:
    <input type="number" name="case_id"
           value="<?php echo $row['case_id']; ?>" required>
    <br><br>

    Complainant Name:
    <input type="text" name="complainant_name"
           value="<?php echo htmlspecialchars($row['complainant_name']); ?>"
           required>
    <br><br>

    Status:
    <select name="status" required>
        <option value="Pending"
            <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>
            Pending
        </option>

        <option value="Under Investigation"
            <?php if ($row['status'] == 'Under Investigation') echo 'selected'; ?>>
            Under Investigation
        </option>

        <option value="Resolved"
            <?php if ($row['status'] == 'Resolved') echo 'selected'; ?>>
            Resolved
        </option>
    </select>
    <br><br>

    <button type="submit" name="update">Update Complaint</button>
</form>

<br>
<a href="complaints.php">Cancel</a>

</body>
</html>