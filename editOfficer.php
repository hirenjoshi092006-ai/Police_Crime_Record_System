<?php
$conn = mysqli_connect("localhost", "root", "", "police_crime_record");

$id = (int)$_GET['id'];

if (isset($_POST['update'])) {
    $station = (int)$_POST['station_id'];
    $name = mysqli_real_escape_string($conn, $_POST['officer_name']);
    $rank = mysqli_real_escape_string($conn, $_POST['rank']);

    $sql = "UPDATE officers
            SET station_id=$station,
                officer_name='$name',
                rank='$rank'
            WHERE officer_id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: officers.php");
        exit;
    } else {
        echo "Error updating officer: " . mysqli_error($conn);
    }
}

$result = mysqli_query($conn, "SELECT * FROM officers WHERE officer_id=$id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Officer not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Officer</title>
</head>
<body>

<h2>Edit Officer</h2>

<form method="post">
    Station ID:
    <input type="number" name="station_id"
           value="<?php echo $row['station_id']; ?>" required>
    <br><br>

    Officer Name:
    <input type="text" name="officer_name"
           value="<?php echo htmlspecialchars($row['officer_name']); ?>" required>
    <br><br>

    Rank:
    <input type="text" name="rank"
           value="<?php echo htmlspecialchars($row['rank']); ?>" required>
    <br><br>

    <button type="submit" name="update">Update</button>
</form>

<br>
<a href="officers.php">Cancel</a>

</body>
</html>