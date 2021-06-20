<?php

require "db_connection.php";
require "functions.php";
session_start();
$userFullname = "";
$userName = "";
$userEmail = "";
$userPassword = "";
$errors = [];

if (isset($_POST["submit"])) {


    if (!isset($_POST['userName'], $_POST['userPassword'], $_POST["userFullname"], $_POST['userEmail'])) {

        $errors[] = ('Please complete the registration form!');
    }

    if (empty($_POST['userFullname'])) {

        $errors[] = ('Fullname Is Required');
    } else {
        $userFullname = trim($_POST["userFullname"]);

    }

    if (empty($_POST['userName'])) {

        $errors[] = ('Username Is Required');
    } elseif (strtolower($userName) == "admin") {
        $errors[] = ('Invalid Username. Please Choose another one');

    } else {
        $userName = trim($_POST["userName"]);

    }

    if (empty($_POST['userEmail'])) {

        $errors[] = ('Email Is Required');
    } elseif (!filter_var($_POST["userEmail"])) {
        $errors[] = "Please Provide a valid email address";

    } else {
        $userEmail = trim($_POST["userEmail"]);

    }

    if (empty($_POST['userPassword'])) {

        $errors[] = ('Password Is Required');
    } elseif (strlen($_POST['userPassword']) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } else {
        $userPassword = trim($_POST["userPassword"]);

    }

    if (empty($errors)) {

        try {
            if (check_username_exist($connection, $userName)) {
                $errors[] = "Username is already taken. Please choose another username";
            } else {
                if (check_email_exist($connection, $userEmail)) {
                    $errors[] = "Email already exists. Please choose another email";
                } else {

                    //save user to database
                    if ($stmt = $connection->prepare('INSERT INTO tbl_users (userName, userPassword, userFullname,userEmail) VALUES (?, ?, ?,?)')) {
                        $password_hash = password_hash($userPassword, PASSWORD_DEFAULT);
                        $stmt->execute([$userName, $password_hash, $userFullname, $userEmail]);
                        $success_message = 'Account Successfully created. Click Here to <a href="index.php">Login</a>';

                    } else {

                        $errors[] = 'Oops!!! Internal Server Error, please try again';

                    }

                }
            }
        } catch (Exception $ex) {
            $errors[] = $ex->getMessage();
        }

        $connection = null;

    }

}

function check_username_exist($connection, $username)
{

    $isExist = false;
    try {
        $sql = 'SELECT userId, userEmail,userPassword FROM tbl_users WHERE userName = :username';

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $isExist = true;

        }

        $connection = null;
        return $isExist;

    } catch (Exception $ex) {
        throw $ex;
    }

}

function check_email_exist($connection, $email)
{

    $isExist = false;
    try {

        $sql = 'SELECT userId,userEmail, userPassword FROM tbl_users WHERE userEmail = :email';

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $isExist = true;

        }

        $connection = null;
        return $isExist;

    } catch (Exception $ex) {
        throw $ex;
    }

}

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row p-5 d-flex justify-content-center" style="margin-top: 100px;">
            <div class="col-md-3">
                <div class="mb-3 p-2">
                    <h3 class="display-5 text-center">Complete the form below to create an account</h3>
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
                        <label for="userFullname"><b>Enter your fullname:</b></label>
                        <input type="text" class="form-control" placeholder="Enter your Full Name" name="userFullname"
                            value="<?php echo ($userFullname) ?>">
                    </div>

                    <div class="form-group">
                        <label for="Username"><b>Enter your username</b></label>
                        <input type="text" class="form-control" placeholder="Enter your Username" name="userName"
                            value="<?php echo ($userName) ?>">
                    </div>
                    <div class="form-group">
                        <label for="email"><b>Enter your Email</b></label>
                        <input type="email" class="form-control" placeholder="Enter Email" name="userEmail"
                            value="<?php echo ($userEmail) ?>">

                    </div>
                    <div class="form-group">
                        <label for="password"><b>Enter your Password</b></label>
                        <input type="password" class="form-control" placeholder="Enter Password" name="userPassword"
                            value="<?php echo ($userPassword) ?>">


                    </div>

                    <button type="submit" class="btn btn-info btn-block my-4" name="submit">Complete
                        Registration</button>

                </form>

                <div class="">
                    <p class="">I already have an account 😊 <a href="index.php">Let me Sign in</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <?php display_footer()?>