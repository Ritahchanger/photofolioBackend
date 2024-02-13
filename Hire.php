<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once("./Functions.php");

    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if ($data === null) {
        echo json_encode(array("message" => "Invalid JSON data"));
    } else {
        $connection = create_connection();

        if (create_database($connection, $database)) {
            $connection->select_db($database);

            $sql_table = "CREATE TABLE jobs (
                fname VARCHAR(200) NOT NULL,
                lname VARCHAR(200) NOT NULL,
                email VARCHAR(200) NOT NULL,
                message VARCHAR(200) NOT NULL
            )";

            $connection->query($sql_table);

            $fname = $data["fname"];
            $lname = $data["lname"];
            $email = $data["email"];
            $message = $data["message"];

            $stmt_create_job = $connection->prepare("INSERT INTO jobs (fname, lname, email, message) VALUES (?, ?, ?, ?)");
            $stmt_create_job->bind_param("ssss", $fname, $lname, $email, $message);
            $stmt_create_job->execute();

            echo json_encode(array("message" => "MESSAGE SENT TO THE ENGINEER"));

        } else {
            echo json_encode(array("message" => "Database not created"));
        }
    }
}
?>
