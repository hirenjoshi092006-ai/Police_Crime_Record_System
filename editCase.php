<?php
$conn = mysqli_connect("localhost", "root", "", "police_crime_record");

$id = (int)$_GET['id'];

if (isset($_POST['update'])) {
    $station = (int)$_POST['station_id'];
    $type = $_POST['case_type'];
    $date = $_POST['filing_date'];

    $stmt = mysqli_prepare($conn,
        "UPDATE CriminalCases
         SET station_id=?, case_type=?, filing_date=?
         WHERE case_id=?");

    mysqli_stmt_bind_param($stmt, "issi", $station, $type, $date, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: criminalCases.php");
        exit;
    } else {
        echo "Error updating case: " . mysqli_stmt_error($stmt);
    }
}

$stmt = mysqli_prepare($conn,
    "SELECT * FROM CriminalCases WHERE case_id=?");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Case not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Criminal Case</title>
</head>
<body>

<h2>Edit Criminal Case</h2>

<form method="post">
    Station ID:
    <input type="number" name="station_id"
           value="<?php echo $row['station_id']; ?>" required>
    <br><br>

    Case Type:
    <input type="text" name="case_type"
           value="<?php echo htmlspecialchars($row['case_type']); ?>" required>
    <br><br>

    Filing Date:
    <input type="date" name="filing_date"
           value="<?php echo $row['filing_date']; ?>" required>
    <br><br>

    <button type="submit" name="update">Update Case</button>
</form>

<br>
<a href="criminalCases.php">Cancel</a>

</body>
</html>