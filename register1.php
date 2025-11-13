<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
session_start();
require_once 'connect1.php';

if (isset($_POST['signUp'])) {
    // Make sure the form field names match these:
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo "All fields are required!";
        exit;
    }

    $password = md5($password);

    // Check if email already exists
    $checkEmail = "SELECT * FROM registrar WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result && $result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        $insertQuery = "INSERT INTO registrar (fName, lName, email, password)
                        VALUES ('$firstName', '$lastName', '$email', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            header("Location: studentLogin1.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password = md5($password);

    $sql = "SELECT * FROM registrar WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        header("Location: registrar.php");
        exit();
    } else {
        echo "Incorrect Email or Password";
    }
}
?>
