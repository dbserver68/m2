<?php require_once('Connections/member.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['Username'])) {
  $loginUsername=$_POST['Username'];
  $password=md5($_POST['Password']);
  $MM_fldUserAuthorization = "State";
  $MM_redirectLoginSuccess = "main.php";
  $MM_redirectLoginFailed = "index.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_member, $member);
  	
  $LoginRS__query=sprintf("SELECT Username, Password, State FROM members WHERE Username=%s AND Password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $member) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'State');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>會員系統</title>
</head>

<body>
<form name="form1" method="POST" action="<?php echo $loginFormAction; ?>">
  <table width="334" border="1">
    <tr>
      <td colspan="2" align="center" valign="middle" bgcolor="#99FFFF">使用者登入</td>
    </tr>
    <tr>
      <td width="113">使用者名稱：</td>
      <td width="205"><label for="Username"></label>
      <input type="text" name="Username" id="Username">        <label for="Username"></label></td>
    </tr>
    <tr>
      <td>密碼：</td>
      <td><label for="Password"></label>
      <input name="Password" type="password" id="Password" value=""></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input type="hidden" name="Authcode" id="Authcode">
        <a href="reg.php">註冊</a>        <input type="submit" name="button" id="button" value="送出"></td>
    </tr>
  </table>
</form>
</body>
</html>