<?php
include "../inc/dbinfo.inc";
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$database = mysqli_select_db($connection, DB_DATABASE);
VerifyUsersTable($connection, DB_DATABASE);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = htmlentities($_POST['NAME']);
    $user_address = htmlentities($_POST['ADDRESS']);
    $is_married = isset($_POST['IS_MARRIED']) ? 1 : 0;
    $user_age = intval($_POST['AGE']);
    if (!empty($user_name) && !empty($user_address) && $user_age > 0) {
        AddUser($connection, $user_name, $user_address, $is_married, $user_age);
    }
}
function AddUser($connection, $name, $address, $isMarried, $age) {
    $n = mysqli_real_escape_string($connection, $name);
    $a = mysqli_real_escape_string($connection, $address);
    $query = "INSERT INTO USERS (NAME, ADDRESS, IS_MARRIED, AGE) VALUES ('$n', '$a', '$isMarried', '$age');";
    if (!mysqli_query($connection, $query)) {
        echo("<p>Error adding user data.</p>");
    }
}
function VerifyUsersTable($connection, $dbName) {
    if (!TableExists("USERS", $connection, $dbName)) {
        $query = "CREATE TABLE USERS (
            ID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            NAME VARCHAR(100),
            ADDRESS VARCHAR(100),
            IS_MARRIED BOOLEAN,
            AGE INT
        )";
        if (!mysqli_query($connection, $query)) {
            echo("<p>Error creating table.</p>");
        }
    }
}
function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);
    $checktable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");
    return mysqli_num_rows($checktable) > 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User</title>
</head>
<body>
<h1>User</h1>
<!-- Input form -->
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
    <table border="0">
        <tr>
            <td>NAME</td>
            <td>ADDRESS</td>
            <td>MARRIED</td>
            <td>AGE</td>
        </tr>
        <tr>
            <td><input type="text" name="NAME" maxlength="100" size="30" required/></td>
            <td><input type="text" name="ADDRESS" maxlength="100" size="30" required/></td>
            <td><input type="checkbox" name="IS_MARRIED"/></td>
            <td><input type="number" name="AGE" required/></td>
            <td><input type="submit" value="Add User"/></td>
        </tr>
    </table>
</form>
<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
    <tr>
        <td>ID</td>
        <td>NAME</td>
        <td>ADDRESS</td>
        <td>MARRIED</td>
        <td>AGE</td>
    </tr>
    <?php
    $result = mysqli_query($connection, "SELECT * FROM USERS");
    while ($query_data = mysqli_fetch_row($result)) {
        echo "<tr>";
        echo "<td>", $query_data[0], "</td>",
            "<td>", $query_data[1], "</td>",
            "<td>", $query_data[2], "</td>",
            "<td>", $query_data[3] ? 'Yes' : 'No', "</td>",
            "<td>", $query_data[4], "</td>";
        echo "</tr>";
    }
    mysqli_free_result($result);
    ?>
</table>
<?php
mysqli_close($connection);
?>
</body>
</html>
