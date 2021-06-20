<?php
session_start();
require "db_connection.php";
require "functions.php";
$errors = [];
$success_message = "";
if (!isset($_SESSION['userLoggedIn'])) {

    header("Location: index.php");

    exit;
} else {

    try {

        $userId = $_SESSION["userId"];

        $data_per_page = 4;

        $number_of_pages = 0;

        $sql = "SELECT * FROM tbl_medicine WHERE userId = :id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":id", $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $total_table_rows = $stmt->rowCount();

                $number_of_pages = ceil($total_table_rows / $data_per_page);

                if (!isset($_GET["page"])) {
                    $current_page = 1;
                } else {
                    $current_page = $_GET["page"];
                }

                $start_limit = ($current_page - 1) * $data_per_page;

                $sql = "SELECT * FROM tbl_medicine WHERE userId = :id LIMIT " . $start_limit . "," . $data_per_page;
                $stmt = $connection->prepare($sql);

                $stmt->bindParam(":id", $userId, PDO::PARAM_INT);

                if ($stmt->execute()) {

                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                } else {
                    //send an error to the user
                }

            }

        } else {
            echo "Internal Server Error";
        }

    } catch (Exception $ex) {
        echo $ex->getMessage();
    }

    if (isset($_GET["deleteMedicine"])) {
        if ($_GET["deleteMedicine"] == true) {
            if (isset($_GET["medicineId"])) {
                $medicineId = $_GET["medicineId"];
                if (deleteMedicineById($connection, $medicineId)) {

                    header("location: home.php");
                } else {
                    header("location: home.php");
                }

                return;
            }

        }
    }

    if (isset($_POST["btnSaveMedicine"])) {
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
                    header("location: home.php");
                    $success_message = "Medicine has successfully been saved";
                } else {
                    header("location: home.php");
                    $errors[] = ("Error saving medicine.Try again");

                }

            } catch (Exception $ex) {
                $error[] = ($ex->getMessage());
            }

        } else {
            $errors[] = ("Error Please Submit Form again");
        }

    }

}

function deleteMedicineById($connection, $medicine_id)
{
    try {

        $sql = "DELETE FROM tbl_medicine WHERE medicineId = :medicine_id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":medicine_id", $medicine_id, PDO::PARAM_INT);
        if ($stmt->execute()) {

            return true;

        } else {
            return false;
        }

    } catch (Exception $ex) {
        throw $ex;
    } finally {
        $connection = null;
    }

}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Medicine App</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse ml-4" id="navbarSupportedContent">
                <ul class="navbar-nav m-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="./home.php">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./dosage.php">Dosage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="./logout.php">Logout</a>
                    </li>

                </ul>

            </div>
        </div>

    </nav>


    <div class="container">
        <div class="row ">
            <div class="col-12 mb-3">
                <div class="row">
                    <div class="col-12 p-2 my-2">
                        <h4 class="">Save New Medicine</h4>
                    </div>
                    <div class="col-12">

                        <div class="error-div" id="error-div">
                            <?php
foreach ($errors as $error) {
    echo ' <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>' . $error . '</strong>
                </div>';

}

if (isset($success_message) && !empty($success_message)) {
    echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>' . $success_message . '</strong>
                </div>';

}

?>
                        </div>


                        <form id="frmMedicine" action="" method="POST">
                            <div class="form-group">
                                <label for="medicineName">Name of medicine: </label>
                                <input type="text" class="form-control" id="medicineName" name="medicineName"
                                    placeholder="Enter the name of the medicine" required>

                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Dosage: </label>
                                        <input type="number" class="form-control" id="dosage" name="dosage" required
                                            placeholder="Dosage Quantity">

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Dosage Unit</label>
                                        <select class="form-control" id="dosageUnit" name="dosageUnit" required>

                                            <option value="Tablet">Tablet</option>
                                            <option value="Bottle">Bottle</option>
                                            <option value="Syringe">Syringe/Injection</option>

                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Frequency </label>
                                        <input type="number" class="form-control" id="frequency" name="frequency"
                                            placeholder="How many should be taken?" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Units</label>
                                        <select class="form-control" id="frequencyUnit" name="frequencyUnit" required>
                                            <option value="Per Day">Per Day</option>
                                            <option value="Per Week">Per Week</option>
                                            <option value="Per Month">Per Month</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Milligrams: </label>
                                <input type="number " class="form-control" id="milligrams" name="milligrams"
                                    placeholder="Enter milligrams" required>
                            </div>
                            <div class="form-group">
                                <label for="">Unit</label>
                                <select class="form-control" id="milligramUnit" name="milligramUnit" required>
                                    <option value="Miilgrams">Miilgrams</option>
                                    <option value="Grams">Grams</option>
                                </select>
                            </div>
                            <button type="submit" name="btnSaveMedicine" id="btnSaveMedicine"
                                class="btn btn-success btn-block">Save
                                Medicine</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="row">
                    <div class="col-12 p-2 my-2">
                        <h4 class=" text-uppercase">List Of All Medicine</h4>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Dosage</th>
                                        <th scope="col">Unit.</th>
                                        <th scope="col">Milligrams</th>
                                        <th scope="col">Unit</th>
                                        <th scope="col">Frequency</th>
                                        <th scope="col">Units</th>
                                        <th scope="col" colspan="2">Edit/Delete</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
if (!empty($rows)) {

    foreach ($rows as $row) {

        echo '<tr>

                                <td>' . $row["medicineName"] . '</td>
                                <td>' . $row["dosage"] . '</td>
                                <td>' . $row["dosageUnit"] . '</td>
                                <td>' . $row["milligrams"] . '</td>
                                <td>' . $row["milligramUnit"] . '</td>
                                <td>' . $row["frequency"] . '</td>
                                <td>' . strtoupper($row["frequencyUnit"]) . '</td>
                                <td>


                                <a  href="?deleteMedicine=true&medicineId=' . $row["medicineId"] . '" class="btn btn-danger btn-block mb-2">Delete</a>


                                </td>
                                </tr>';
    }

}

?>


                                </tbody>
                            </table>

                        </div>
                        <!-- end of table -->
                        <nav aria-label="" class="mt-4">
                            <ul class="pagination">
                                <?php
for ($page = 1; $page <= $number_of_pages; $page++) {
    echo '<li class="page-item"><a class="page-link" href="?page=' . $page . '">' . $page . '</a></li>';
}

?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>


        </div>

    </div>
    <?php display_footer();?>