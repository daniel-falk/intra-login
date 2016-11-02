<?php

// Include language file
include 'language.php';
$lang = new language("EN");

// Include the login module
include_once 'login_module.php';
$login = new login_module($lang);
?>
<html>
<head>

<title>Daniels hemserver</title>

<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript">
    function validateForm()
    {
        var x=document.forms["login"]["username"].value;
        if (x==null || x=="")
        {
            alert("<?=$lang->MSG_ERR_EMPTY_FORM?>");
            return false;
        }
    }
</script>
</head>
<body>
<?php
// Check if logged in or perform a login if requested
if (!$login->check_logged_in(false)) {
  // LOGIN FIELD
  include "login_form.php";
} else {
  // ALREADY LOGGED IN
  include "admin_panel.php";
}
?>

</body>
</html>
