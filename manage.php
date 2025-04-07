<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<?php
// define variables and set to empty values
$actionErr = "";
$error = FALSE;
$user = $pass = "";
$amount = 0;

// Connor: Check for cookie before trusting it
if (!isset($_COOKIE['user']) || empty($_COOKIE['user'])) {
    echo "Access denied. No user is logged in.";
    exit();
}

$user=$_COOKIE['user'];

// Possible backdoor fix:  Check if user exists in DB before entering manage logic
$mysqli = new mysqli("localhost", "root", "root", "bank");
$check = $mysqli->prepare("SELECT 1 FROM users WHERE name = ?");
$check->bind_param("s", $user);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    echo "Invalid user.";
    exit();
}
$check->close();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  if (empty($_GET["action"])) {
    $error = TRUE;
    $actionrErr = "Action is required";
  } else {
    $action = test_input($_GET["action"]);
    if ($action == "deposit" || $action == "withdraw")
    {
       if (empty($_GET["amount"])) {
       $error = TRUE;
       $amountErr = "Amount is required";
       }
       else
       { 
          $amount = $_GET["amount"];
       }
   }
  }

 if (!$error)
 {
     echo "User $user";
     $mysqli = new mysqli("localhost","root", "root", "bank");
     
     // Check connection
     if ($mysqli->connect_errno) {
       echo "Failed to connect to MySQL: " . $mysqli -> connect_error; 
       exit();
    }
     // Perform query
    $stmt = "select balance from users where name='" . $user . "'";
    $result = $mysqli -> query($stmt);
    $obj = $result->fetch_object();
    $balance = $obj->balance;
    if ($action == "withdraw")
    {
	if($balance >= $amount){
        	$balance -= $amount;
	}
	else{
		echo "Not enough funds ";
	}
    }
    if ($action == "deposit")
    {
        $balance += $amount;
    }
    if ($action == "close") {
    	$stmtClose = $mysqli->prepare("delete from users where name = ?");
	$stmtClose->bind_param("s", $user);
	if (!$stmtClose->execute()) {
	     echo "Error: " . $stmtClose->error;
	}
	$stmtClose->close();

	setcookie('user', '', time() - 3600);
	unset($_COOKIE['user']);
	echo "Account closed";
	$mysqli->close();
	exit();
    }
    echo "Balance=" . $balance;
    $stmt = "update users set balance=" . $balance . " where name='" . $user . "'";
    $mysqli -> query($stmt);
    $mysqli -> close();
  	
   }
}
    
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  return $data;
}
?>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field</span></p>
<form method="get" action="index.php">
  Action: <input type="text" action="balance" value="<?php echo $action;?>">
  <span class="error">* <?php echo $actionErr;?></span>
  <br><br>
    Amount: <input type="text" amount="0" value="<?php echo $amount;?>">
  <span class="error">* <?php echo $amountErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="Submit">  
</form>

<?php
echo "<h2>Your Input:</h2>";
echo $user;
echo "<br>";
echo $pass;
?>

</body>
