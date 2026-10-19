
<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "police_crime_record";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Crime Record System</title>
</head>
<body>

<header>
    <h1>Police Crime Record System</h1>
</header>

<table>
    <tr>
        <th><a href="policeStation.php">Police Station Data</a></th>
    </tr>
    <tr>
        <th><a href="officers.php">Officers Data</a></th>
    </tr>
    <tr>
        <th><a href="CriminalCases.php">Criminal Cases Data</a></th>
    </tr>
    <tr>
        <th><a href="complaints.php">Complaints Data</a></th>
    </tr>
</table>

</body>
</html>
<?php
mysqli_close($conn);
?>
