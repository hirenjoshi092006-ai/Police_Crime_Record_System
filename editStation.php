<?php
$conn = mysqli_connect("localhost", "root", "", "police_crime_record");

$id = $_GET['id'];

if (isset($_POST['update'])) {
    $name = $_POST['station_name'];
    $district = $_POST['district'];
    $state = $_POST['state'];

    $sql = "UPDATE policestations
            SET station_name='$name',
                district='$district',
                state='$state'
            WHERE station_id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: policeStation.php");
        exit;
    } else {
        echo "Error updating station";
    }
}

$result = mysqli_query($conn, "SELECT * FROM policestations WHERE station_id=$id");
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Police Station</title>
</head>
<body>

<h2>Edit Police Station</h2>

<form method="post">
    Station Name:
    <input type="text" name="station_name"
           value="<?php echo htmlspecialchars($row['station_name']); ?>" required>
    <br><br>

    District:
    <input type="text" name="district"
           value="<?php echo htmlspecialchars($row['district']); ?>" required>
    <br><br>

    State:
    <input type="text" name="state"
           value="<?php echo htmlspecialchars($row['state']); ?>" required>
    <br><br>

    <button type="submit" name="update">Update</button>
</form>

<br>
<a href="policeStation.php">Cancel</a>

</body>
</html>