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

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="index.php";
  $loginUsername = $_POST['Username'];
  $LoginRS__query = sprintf("SELECT Username FROM members WHERE Username=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_member, $member);
  $LoginRS=mysql_query($LoginRS__query, $member) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO members (Username, Password, Email, Authcode) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['Username'], "text"),
                       GetSQLValueString(md5($_POST['Password']), "text"),
                       GetSQLValueString($_POST['Email'], "text"),
                       GetSQLValueString($_POST['Authcode'], "text"));

  mysql_select_db($database_member, $member);
  $Result1 = mysql_query($insertSQL, $member) or die(mysql_error());

  $insertGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>會員系統</title>
</head>

<body>
<form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
  <table width="333" border="1">
    <tr>
      <td colspan="2" align="center" valign="middle" bgcolor="#99FFFF">使用者註冊</td>
    </tr>
    <tr>
      <td width="120" align="left">使用者名稱：</td>
      <td width="197"><label for="Username"></label>
      <input type="text" name="Username" id="Username"></td>
    </tr>
    <tr>
      <td>密碼：</td>
      <td><label for="Password"></label>
      <input type="password" name="Password" id="Password"></td>
    </tr>
    <tr>
      <td>信箱：</td>
      <td><label for="Email"></label>
      <input type="text" name="Email" id="Email"></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input name="Authcode" type="hidden" id="Authcode" value="Authcode">
        <a href="index.php">登入</a>
<input type="submit" name="button" id="button" value="送出"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1">
</form>
</body>
</html>