<?php
//include external connection.php
include "connection.php";

// prepare an sql statement
$stmt = $conn->prepare("INSERT INTO users (username, password, firstname, lastname, email) VALUES (?, ?, ?, ?, ?)");
//bind the sql statement
$stmt->bind_param("sssss", $username, $password, $firstname, $lastname, $email);

// set parameters and execute
$username = mysql_escape_string($_POST["user_name"]);
$password = mysql_escape_string($_POST["password"]);
$checkpassword = mysql_escape_string($_POST["password_confirmation"]);
$firstname = mysql_escape_string($_POST["first_name"]);
$lastname = mysql_escape_string($_POST["last_name"]);
$email = mysql_escape_string($_POST["email_address"]);

$stmt->execute(); //execute the statement

  //display alert message
  echo "<script>alert('New record created.');</script>";
  $stmt->close(); //close the statement
  $conn->close(); //close the connection
?>