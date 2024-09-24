<?php
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");

  class User {
    function login($json){
      include 'connection-pdo.php';
      //{username:'pitok',password:12345}
      $json = json_decode($json, true);
      $sql = "SELECT * FROM tblusers 
              WHERE usr_username = :username AND usr_password = :password";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':username', $json['username']);
      $stmt->bindParam(':password', $json['password']);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function register($json){
      //{username:'pitok',password:'12345', fullname:'PItok Batolata'}
      include 'connection-pdo.php';
      $json = json_decode($json, true);
      $sql = "INSERT INTO tblusers(usr_username, usr_password, usr_fullname)
        VALUES(:username, :password, :fullname)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':username', $json['username']);
      $stmt->bindParam(':password', $json['password']);
      $stmt->bindParam(':fullname', $json['fullname']);
      $stmt->execute();
      $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function getUsers(){

    }
  }

  //submitted by the client - operation and json data
  if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    $operation = $_GET['operation'];
    $json = $_GET['json'];
  }else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $operation = $_POST['operation'];
    $json = $_POST['json'];
  }

  $user = new User();
  switch($operation){
    case "login":
      echo $user->login($json);
      break;
    case "register":
      echo $user->register($json);
      break;
    case "getUsers":
      echo $user->getUsers($json);
      break;
    // default:
    //   echo json_encode(["error" => "Invalid operation"]);
  }
?>