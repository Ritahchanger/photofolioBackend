<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once("./Functions.php");

    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if ($data === null) {

        echo json_encode(array("status" => "error", "message" => "Invalid JSON data"));
    } else {
        $connection = create_connection();

        if ($connection === false) {

            echo json_encode(array("status" => "error", "message" => "Failed to connect to the database"));
        } else {
            $database = $database;

            if (create_database($connection, $database)) {
                $connection->select_db($database);

                $sql_table = "CREATE TABLE IF NOT EXISTS jobs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    fname VARCHAR(200) NOT NULL,
                    lname VARCHAR(200) NOT NULL,
                    email VARCHAR(200) NOT NULL,
                    message TEXT NOT NULL
                )";


                if ($connection->query($sql_table) === false) {

                    echo json_encode(array("status" => "error", "message" => "Failed to create the table"));
                } else {

                    $stmt_create_job = $connection->prepare("INSERT INTO jobs (fname, lname, email, message) VALUES (?, ?, ?, ?)");
                    $fname = $data["fname"];
                    $lname = $data["lname"];
                    $email = $data["email"];
                    $message = $data["message"];
                    $stmt_create_job->bind_param("ssss", $fname, $lname, $email, $message);


                    if ($stmt_create_job->execute()) {

                        try {
                            require 'vendor/autoload.php';
                            $sendingemail="dennispeter2580@gmail.com";
                            $mail = new PHPMailer(true);
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'dennispeter2580@gmail.com';
                            $mail->Password   = 'erux hjgf yton farc';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 587;
                            $mail->setFrom($sendingemail,$fname . ' ' . $lname);
                            $mail->addAddress('peterdennis573@gmail.com');
                            $mail->isHTML(true);
                            $mail->Subject = 'JOB';
                            $mail->Body    = "Client Name:{$fname} {$lname} and the email is {$email}: {$message} ";
                            $mail->send();
                        } catch (Exception $e) {
                            http_response_code(500);
                            echo json_encode(array("error" => "Email could not be sent. Mailer Error: " . $mail->ErrorInfo));
                        }
                        echo json_encode(array("status" => "success", "message" => "Data inserted successfully"));
                    } else {

                        echo json_encode(array("status" => "error", "message" => "Failed to insert data into the database"));
                    }
                }
            } else {
                echo json_encode(array("status" => "error", "message" => "Database not created"));
            }

            $connection->close();
        }
    }
}
