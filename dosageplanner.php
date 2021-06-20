<?php
session_start();
require "db_connection.php";

$user_id = $_SESSION["user_id"];
$errors = array();

if (isset($_POST["medicine_id"]) && isset($_POST["date_taken"]) && isset($_POST["time_taken"])) {

    $medicine_id = trim($_POST["medicine_id"]);
    $date_taken = trim($_POST["date_taken"]);
    $time_taken = trim($_POST["time_taken"]);

    if (empty($medicine_id)) {
        $errors[] = ("Please select a medicine");
    }
    if (empty($date_taken)) {
        $errors[] = ("Please select a date to take this medicine");

    }
    if (empty($time_taken)) {
        $errors[] = ("Please select the time to take this medicine");

    }

    if (!empty($errors)) {
        echo json_encode($errors);
    } else {

        try {
            //save dosage plan to the database
            $sql = "INSERT INTO tbldosageplanner (medicine_id,date_taken,time_taken,user_id) values (:medicine_id,:date_taken,:time_taken,:user_id);";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":medicine_id", $medicine_id, PDO::PARAM_INT);
            $stmt->bindParam(":date_taken", $date_taken, PDO::PARAM_STR);
            $stmt->bindParam(":time_taken", $time_taken, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(array("success" => "Dosage Planner Saved Successfully"));
            }
        } catch (Exception $ex) {
            echo json_encode($ex->getMessage());
        }
    }

} else {
    echo json_encode("Error Saving Plan");

}