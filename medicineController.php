<?php
session_start();
include "db_connection.php";
if (isset($_POST["medicineName"]) && isset($_POST["dosage"]) && isset($_POST["dosageUnit"]) && isset($_POST["milligrams"]) && isset($_POST["milligramUnit"]) && isset($_POST["frequency"]) && isset($_POST["frequencyUnit"])) {

    $medicineName = trim($_POST["medicineName"]);
    $dosage = trim($_POST["dosage"]);
    $dosageUnit = trim($_POST["dosageUnit"]);
    $milligrams = trim($_POST["milligrams"]);
    $milligramUnit = trim($_POST["milligramUnit"]);
    $frequency = trim($_POST["frequency"]);
    $frequencyUnit = trim($_POST["frequencyUnit"]);
    $userId = $_SESSION["userId"];

    try {

        $sql = "INSERT INTO tbl_medicine(medicineName,dosage,dosageUnit, milligrams,milligramUnit, frequency,frequencyUnit,userId) VALUES (:medicineName,:dosage,:dosageUnit,:milligrams, :milligramUnit,:frequency,:frequencyUnit,:userId)";

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":medicineName", $medicineName, PDO::PARAM_STR);
        $stmt->bindParam(":dosage", $dosage, PDO::PARAM_INT);
        $stmt->bindParam(":dosageUnit", $dosageUnit, PDO::PARAM_STR);
        $stmt->bindParam(":milligrams", $milligrams, PDO::PARAM_INT);
        $stmt->bindParam(":milligramUnit", $milligramUnit, PDO::PARAM_INT);
        $stmt->bindParam(":frequency", $frequency, PDO::PARAM_INT);
        $stmt->bindParam(":frequencyUnit", $frequencyUnit, PDO::PARAM_STR);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode("Medicine has successfully been saved");
        } else {
            echo json_encode("Error saving medicine.Try again");

        }

    } catch (Exception $ex) {
        echo json_encode($ex);
    }

} else {
    echo json_encode("Error Occured");
}