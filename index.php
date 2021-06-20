<?php
session_start();
include "db_connection.php";
include "functions.php";
if (isset($_SESSION['userLoggedIn']) == true) {
    header("Location: home.php");
}

$errors = array();
$userName = "";
$userPassword = "";

if (isset($_POST["submit"])) {

    if (!isset($_POST['userName'], $_POST['userPassword'])) {

        $errors[] = 'Username and password is required';
    }

    if (empty($_POST['userName'])) {
        $errors[] = ('Username Is Required');
    } else {
        $userName = trim($_POST["userName"]);

    }

    if (empty($_POST['userPassword'])) {

        $errors[] = ('Password Is Required');
    } else {
        $userPassword = trim($_POST["userPassword"]);

    }

    if (empty($errors)) {
        try {
            $sql = "SELECT * FROM tbl_users WHERE userName = ? or userEmail = ?";
            $statement = $connection->prepare($sql);
            if ($statement) {

                $statement->execute([$userName, $userName]);

                if ($statement->rowCount() > 0) {
                    $row = $statement->fetch(PDO::FETCH_OBJ);

                    if (password_verify($userPassword, $row->userPassword)) {

                        session_regenerate_id();
                        $_SESSION['userLoggedIn'] = true;
                        $_SESSION['userName'] = $_POST['userName'];
                        $_SESSION['userFullname'] = $row->userFullname;
                        $_SESSION['userId'] = $row->userId;
                        header('Location: home.php');

                    } else {

                        $errors[] = 'Username or password is incorrect';
                    }
                } else {

                    $errors[] = "!!!Username or password is incorrect";
                }

                $statement = null;

            } else {
                $errors[] = "Oops!!! Internal Server Error, please try again";
            }
        } catch (Exception $ex) {
            $errors[] = $ex->getMessage();
        }

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row p-5 d-flex justify-content-center" style="margin: 150px;">
            <div class="col-md-3">
                <div class="mb-3 p-2">
                    <h3 class="display-5 text-center">Please login to continue</h3>
                </div>

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
                <form action="" method="POST" autocomplete="off">

                    <div class="form-group">
                        <label for="userName"><b>Username</b></label>
                        <input type="text" placeholder="userName" class="form-control" name="userName" required
                            value="<?php echo ($userName) ?>">

                    </div>
                    <div class="form-group">
                        <label for="userPassword"><b>Password</b></label>
                        <input type="password" placeholder="Enter Password" class="form-control" name="userPassword"
                            required value="<?php echo ($userPassword) ?>">
                    </div>




                    <button type="submit" class="btn btn-info btn-block my-4 " name="submit">Sign in</button>
                </form>

                <div class="d-flex  flex-column ">
                    <p class=""><a href="#">Forgot Password</a>.</p>
                    <p class="">I don't have an account 😲 <a href="register.php">Let me register</a>.</p>
                </div>
            </div>
        </div>
    </div>


    <?php display_footer()?>