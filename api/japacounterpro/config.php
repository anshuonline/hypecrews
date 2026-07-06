        <?php
// config.php
$servername = "localhost";
$username = "u388169091_rajdeep"; // Replace with your Hostinger DB username
$password = "@Japacounterpro1234.com"; // Replace with your Hostinger DB password
$dbname = "u388169091_japacounterpro"; // Replace with your Hostinger DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
date_default_timezone_set('Asia/Kolkata');
// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}


?>
