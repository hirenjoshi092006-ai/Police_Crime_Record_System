<?php
$conn = mysqli_connect("localhost", "root", "", "police_crime_record");

if (!$conn) {
    die("Connection failed");
}

$message = "";

// Add complaint
if (isset($_POST['add'])) {
    $id = (int)$_POST['complaint_id'];
    $case = (int)$_POST['case_id'];
    $name = $_POST['complainant_name'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare($conn,
        "INSERT INTO Complaints VALUES (?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "iiss", $id, $case, $name, $status);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: complaints.php");
        exit;
    } else {
        $message = "Error adding complaint: " . mysqli_stmt_error($stmt);
    }
}

// Delete complaint
if (isset($_POST['delete'])) {
    $id = (int)$_POST['complaint_id'];

    $stmt = mysqli_prepare($conn,
        "DELETE FROM Complaints WHERE complaint_id = ?");

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: complaints.php");
        exit;
    } else {
        $message = "Error deleting complaint: " . mysqli_stmt_error($stmt);
    }
}

// Display complaints
$result = mysqli_query($conn, "SELECT * FROM Complaints");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaints Management</title>
</head>
<body>

<h1>Complaints Management</h1>
<a href="index.php">Return to Main Menu</a>

<p><?php echo htmlspecialchars($message); ?></p>

<h2>Complaint Records</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Complaint ID</th>
    <th>Case ID</th>
    <th>Complainant Name</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['complaint_id']; ?></td>
    <td><?php echo $row['case_id']; ?></td>
    <td><?php echo htmlspecialchars($row['complainant_name']); ?></td>
    <td><?php echo htmlspecialchars($row['status']); ?></td>
    <td>
        <a href="editComplaint.php?id=<?php echo $row['complaint_id']; ?>">
            Edit
        </a>

        <form method="post" style="display:inline"
              onsubmit="return confirm('Delete this complaint?');">
            <input type="hidden" name="complaint_id"
                   value="<?php echo $row['complaint_id']; ?>">
            <button type="submit" name="delete">Delete</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>

<h2>Add Complaint</h2>

<form method="post">
    Complaint ID:
    <input type="number" name="complaint_id" required>
    <br><br>

    Case ID:
    <input type="number" name="case_id" required>
    <br><br>

    Complainant Name:
    <input type="text" name="complainant_name" required>
    <br><br>

    Status:
    <select name="status" required>
        <option value="Pending">Pending</option>
        <option value="Under Investigation">Under Investigation</option>
        <option value="Resolved">Resolved</option>
    </select>
    <br><br>

    <button type="submit" name="add">Add Complaint</button>
</form>

</body>
</html>