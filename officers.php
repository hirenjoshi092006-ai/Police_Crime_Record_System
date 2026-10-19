<?php
$conn = mysqli_connect("localhost", "root", "", "police_crime_record");

if (!$conn) {
    die("Connection failed");
}

$message = "";

// Add officer
if (isset($_POST['add'])) {
    $id = $_POST['officer_id'];
    $station = $_POST['station_id'];
    $name = $_POST['officer_name'];
    $rank = $_POST['rank'];

    $sql = "INSERT INTO officers VALUES ('$id', '$station', '$name', '$rank')";

    if (mysqli_query($conn, $sql)) {
        header("Location: officers.php");
        exit;
    } else {
        $message = "Error adding officer: " . mysqli_error($conn);
    }
}

// Delete officer
if (isset($_POST['delete'])) {
    $id = $_POST['officer_id'];

    $sql = "DELETE FROM officers WHERE officer_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: officers.php");
        exit;
    } else {
        $message = "Error deleting officer: " . mysqli_error($conn);
    }
}

// Display officers
$result = mysqli_query($conn, "SELECT * FROM officers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Officers Management</title>
</head>
<body>

<h1>Officers Management</h1>
<a href="index.php">Return to Main Menu</a>

<p><?php echo htmlspecialchars($message); ?></p>

<h2>Officer Records</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Officer ID</th>
    <th>Station ID</th>
    <th>Officer Name</th>
    <th>Rank</th>
    <th>Actions</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['officer_id']; ?></td>
    <td><?php echo $row['station_id']; ?></td>
    <td><?php echo htmlspecialchars($row['officer_name']); ?></td>
    <td><?php echo htmlspecialchars($row['rank']); ?></td>
    <td>
        <a href="editOfficer.php?id=<?php echo $row['officer_id']; ?>">Edit</a>

        <form method="post" style="display:inline"
              onsubmit="return confirm('Delete this officer?');">
            <input type="hidden" name="officer_id"
                   value="<?php echo $row['officer_id']; ?>">
            <button type="submit" name="delete">Delete</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>

<h2>Add Officer</h2>

<form method="post">
    Officer ID:
    <input type="number" name="officer_id" required>
    <br><br>

    Station ID:
    <input type="number" name="station_id" required>
    <br><br>

    Officer Name:
    <input type="text" name="officer_name" required>
    <br><br>

    Rank:
    <input type="text" name="rank" required>
    <br><br>

    <button type="submit" name="add">Add Officer</button>
</form>

</body>
</html>