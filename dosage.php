<?php

session_start();
require "db_connection.php";
require "functions.php";

$errors = array();
$success_message = "";
$userId = $_SESSION["userId"];

if (!isset($_SESSION['userLoggedIn'])) {

    header("Location: index.php");

    exit;
} else {

    if (isset($_POST["btnSaveDosage"])) {
        if (isset($_POST["medicineId"]) && isset($_POST["dateTaken"]) && isset($_POST["timeTaken"])) {

            $medicineId = trim($_POST["medicineId"]);
            $dateTaken = trim($_POST["dateTaken"]);
            $timeTaken = trim($_POST["timeTaken"]);

            if (empty($medicineId)) {
                $errors[] = ("Please select a medicine");
            }
            if (empty($dateTaken)) {
                $errors[] = ("Please select a date to take this medicine");

            }
            if (empty($timeTaken)) {
                $errors[] = ("Please select the time to take this medicine");

            }

            if (!empty($errors)) {
                echo json_encode($errors);
            } else {

                try {
                    //save dosage plan to the database
                    $sql = "INSERT INTO tbl_dosages (medicineId,dateTaken,timeTaken,userId) values (:medicineId,:dateTaken,:timeTaken,:userId);";
                    $stmt = $connection->prepare($sql);
                    $stmt->bindParam(":medicineId", $medicineId, PDO::PARAM_INT);
                    $stmt->bindParam(":dateTaken", $dateTaken, PDO::PARAM_STR);
                    $stmt->bindParam(":timeTaken", $timeTaken, PDO::PARAM_STR);
                    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $success_message = "Dosage Plan Saved Successfully";
                    }
                } catch (Exception $ex) {
                    $errors[] = $ex->getMessage();
                }
            }

        } else {
            $errors[] = "Internal Server Error";

        }

    }

    try {

        $medicine_per_page = 3;
        $number_of_pages = 0;

        $sql = "SELECT * FROM tbl_dosages WHERE userId = :userId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $total_rows = $stmt->rowCount();

                $number_of_pages = ceil($total_rows / $medicine_per_page);

                if (!isset($_GET["page"])) {
                    $page = 1;
                } else {
                    $page = $_GET["page"];
                }

                $starting_limit = ($page - 1) * $medicine_per_page;

                $sql = "SELECT dosageId,medicineName,dateTaken,timeTaken FROM tbl_dosages INNER JOIN tbl_users on tbl_dosages.userId = tbl_users.userId INNER JOIN tbl_medicine on tbl_dosages.medicineId = tbl_medicine.medicineId WHERE tbl_dosages.userId = :userId LIMIT " . $starting_limit . "," . $medicine_per_page;

                $stmt = $connection->prepare($sql);
                $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                } else {

                }

            }

        } else {

        }

    } catch (Exception $ex) {
        echo $ex->getMessage();
    }

    if (isset($_POST["btnUpdateDosage"])) {

        if ($_POST["dosageId"] != null) {
            try {

                $dosageId = $_POST["dosageId"];
                $medicineId = trim($_POST["medicineId"]);
                $dateTaken = trim($_POST["dateTaken"]);
                $timeTaken = trim($_POST["timeTaken"]);

                if (getDosagePlanById($connection, $dosageId) != null) {
                    $sql = "UPDATE tbl_dosages SET medicineId = :medicineId, dateTaken = :dateTaken, timeTaken = :timeTaken,userId = :userId where dosageId = :dosageId";

                    if ($stmt = $connection->prepare($sql)) {
                        $stmt->bindParam(":medicineId", $medicineId, PDO::PARAM_INT);
                        $stmt->bindParam(":dateTaken", $dateTaken, PDO::PARAM_STR);
                        $stmt->bindParam(":timeTaken", $timeTaken, PDO::PARAM_STR);
                        $stmt->bindParam(":dosageId", $dosageId, PDO::PARAM_INT);
                        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            $success_message = "Dosage Plan Successfully Updated";

                        } else {
                            $errors[] = "Error Updating Dosage Plan";

                        }

                    } else {
                        $errors[] = "Oops! Something went wrong";
                    }

                } else {

                    $errors[] = "Dosage Plan Does not Exist!";
                }

            } catch (Exception $ex) {
                $errors[] = $ex->getMessage();
            }

        }

    }

    if (isset($_GET["deleteDosage"]) && isset($_GET["dosageId"])) {
        //Delete Dosage Plan
        try {

            $dosageId = $_GET["dosageId"];
            if (getDosagePlanById($connection, $dosageId) != null) {
                if (deleteDosageById($connection, $dosageId)) {

                    header("location: dosage.php");
                } else {
                    header("location: dosage.php");
                }

            } else {
                $errors[] = "Dosage with that ID is not found";
            }

        } catch (Exception $ex) {
            $errors[] = $ex->getMessage();
        }

    }

}

function getMedicineByUserId($connection, $user_id)
{
    try {

        $sql = "SELECT * FROM tbl_medicine WHERE userId = :user_id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

    } catch (\Throwable$th) {
        //throw $th;
    } finally {
        $connection = null;
    }

}

function deleteDosageById($connection, $dosageId)
{
    try {

        $sql = "DELETE FROM tbl_dosages WHERE dosageId = :dosageId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":dosageId", $dosageId, PDO::PARAM_INT);
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


    <div class="container ">

        <!-- Modal -->
        <div class="modal fade" id="dosageModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby=""
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Dosage Plan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="">Select Medicine</label>
                                <select class="form-control" name="medicineId" id="medicineId">
                                    <option value="" selected>Please Select Medicine</option>
                                    <?php

$userMedicines = getMedicineByUserId($connection, $userId);
foreach ($userMedicines as $medicine) {
    echo '<option value="' . $medicine["medicineId"] . '" >' . $medicine["medicineName"] . '</option>';
}
?>
                                </select>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Date Taken</label>
                                        <input type="date" class="form-control" id="dateTaken" name="dateTaken"
                                            id="dateTaken">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Time Taken</label>
                                        <input type="time" class="form-control" id="timeTaken" name="timeTaken">
                                    </div>
                                </div>
                                <input type="hidden" id="dosageId" name="dosageId">
                            </div>
                            <button type="submit" name="btnUpdateDosage" class="btn btn-primary btn-block">Update Dosage
                                Plan</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- End of modal -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="dosage-errors" id="dosage-errors">
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


                <form action="" method="POST">
                    <div class="form-group">
                        <label for="">Select Medicine</label>
                        <select class="form-control" name="medicineId" id="medicineId">
                            <option value="" selected>Please Select Medicine</option>
                            <?php

$userMedicines = getMedicineByUserId($connection, $userId);
foreach ($userMedicines as $medicine) {
    echo '<option value="' . $medicine["medicineId"] . '" >' . $medicine["medicineName"] . '</option>';
}
?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Date Taken</label>
                                <input type="date" class="form-control" id="dateTaken" name="dateTaken" id="dateTaken">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Time Taken</label>
                                <input type="time" class="form-control" id="timeTaken" name="timeTaken">
                            </div>
                        </div>
                        <input type="hidden" id="dosageId" name="dosageId">
                    </div>
                    <button type="submit" name="btnSaveDosage" id="btnSubmit" class="btn btn-primary btn-block">Save
                        New Plan</button>
                </form>
            </div>

            <div class="col-md-8">
                <div class="row">
                    <div class="col-12 my-2">
                        <h4 class=" text-uppercase">View All Dosage Plans</h4>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive ">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>

                                        <th scope="col">Medicine Name</th>
                                        <th scope="col">Date Taken</th>
                                        <th scope="col">Time Taken</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
if (!empty($rows)) {
    foreach ($rows as $row) {

        echo '<tr>

                                <td>' . $row["medicineName"] . '</td>
                                <td>' . $row["dateTaken"] . '</td>
                                <td>' . $row["timeTaken"] . '</td>
                                <td>
                                <a href="?deleteDosage=true&dosageId=' . $row["dosageId"] . '" class="btn btn-danger btn-block mb-2">Delete</a>
                                    <button id="btnUpdateDosagePlan" data-id="' . $row["dosageId"] . '" class="btn btn-info btn-block">Edit</button>
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