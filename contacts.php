<?php
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");

  class Contact {
    function getContacts($json){
      // {userId : 1}
      include 'connection-pdo.php';
      $json = json_decode($json, true);
      $sql = "SELECT * FROM tblcontacts 
              WHERE contact_userId = :userId
              ORDER BY contact_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':userId', $json['userId']);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function getContactDetails($json){
      include 'connection-pdo.php';
      $json = json_decode($json, true);
      $sql = "SELECT a.*, b.grp_name 
              FROM tblcontacts a INNER JOIN tblgroups b 
                ON a.contact_group = b.grp_id
              WHERE a.contact_id = :contactId";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':contactId', $json['contactId']);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function getGroups($json) {
        include 'connection-pdo.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tblgroups ORDER BY grp_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }

    function addContact($json){
      //{userId:1, name:'Pitok', email:'Pitok@gmail.com', phone:'09876565434'
      //address:'CDO', groupId:3}
      include 'connection-pdo.php';
      $json = json_decode($json, true);
      $sql = "INSERT INTO tblcontacts(contact_userId, contact_name, contact_phone,
              contact_email, contact_address, contact_group)
              VALUES (:contact_userId, :contact_name, :contact_phone,
              :contact_email, :contact_address, :contact_group)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam('contact_userId', $json['userId']);
      $stmt->bindParam('contact_name', $json['name']);
      $stmt->bindParam('contact_phone', $json['phone']);
      $stmt->bindParam('contact_email', $json['email']);
      $stmt->bindParam('contact_address', $json['address']);
      $stmt->bindParam('contact_group', $json['groupId']);
      $stmt->execute();
      $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function updateContact($json){
      //{userId:1, contactName:'Pitok', email:'Pitok@gmail.com', contactPhone:'09876565434'
      //address:'CDO', groupId:3}
      include 'connection-pdo.php';
      $json = json_decode($json, true);
      $sql = "UPDATE tblcontacts SET contact_name=:contactName, 
              contact_phone=:contactPhone,
              contact_email=:contactEmail, contact_address=:contactAddress, 
              contact_group=:contactGroupId
              WHERE contact_id=:contactId";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam('contactId', $json['contactId']);
      $stmt->bindParam('contactName', $json['contactName']);
      $stmt->bindParam('contactPhone', $json['contactPhone']);
      $stmt->bindParam('contactEmail', $json['contactEmail']);
      $stmt->bindParam('contactAddress', $json['contactAddress']);
      $stmt->bindParam('contactGroupId', $json['contactGroupId']);
      $stmt->execute();
      $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function deleteContact($json){
      // {contactId : 1}
      include 'connection-pdo.php';
      $json = json_decode($json, true);
      $sql = "DELETE FROM tblcontacts 
              WHERE contact_id = :contactId";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':contactId', $json['contactId']);
      $stmt->execute();
      $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function searchContact($json){
      // {searchKey : don, userId:1}
      include 'connection-pdo.php';
      $json = json_decode($json, true);

      $searchKey = "%".$json['searchKey']."%";

      $sql = "SELECT * FROM tblcontacts 
              WHERE contact_userId = :userId
               AND contact_name LIKE '$searchKey'
              ORDER BY contact_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam("userId", $json['userId']);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function filterGroups($json){
      // {groupId : 1, userId:1}
      include 'connection-pdo.php';
      $json = json_decode($json, true);
  
      $sql = "SELECT * FROM tblcontacts 
              WHERE contact_userId = :userId
               AND contact_group = :groupId
              ORDER BY contact_name";
      
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":userId", $json['userId']);
      $stmt->bindParam(":groupId", $json['groupId']);
      
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }
  }

  //submitted by the client - operation and json
  if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    $operation = $_GET['operation'];
    $json = $_GET['json'];
  }else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $operation = $_POST['operation'];
    $json = $_POST['json'];
  }

  $contact = new Contact();
  switch($operation){
    case "getContacts":
      echo $contact->getContacts($json);
      break;
    case "getContactDetails":
      echo $contact->getContactDetails($json);
      break;
    case "getGroups":
      echo $contact->getGroups($json);
      break;
    case "addContact":
      echo $contact->addContact($json);
      break;
    case "updateContact":
      echo $contact->updateContact($json);
      break;
    case "deleteContact":
      echo $contact->deleteContact($json);
      break;
    case "searchContact":
      echo $contact->searchContact($json);
      break;
    case "filterGroups":
      echo $contact->filterGroups($json);
      break;
  }
?>