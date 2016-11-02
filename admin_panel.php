<?php

if (isset($_GET['action']) && !strcmp($_GET['action'], 'add_user')){
    $login->add_user($_POST['add_username'], $_POST['add_password'], $_POST['repeat_password'], $_POST['add_type']);
}

?>

<section class="welcome_panel cf">
    <div>
        Welcome <b><?=$_SESSION['username']?></b>. 
        Last activity: <?=date("G:i:s",$_SESSION['last_activity'])?>
    </div>
    <div class="button" onclick="parent.location='?action=logout'"><?=$lang->UI_LOGOUT?></div>
</section>

<section class="admin_panel cf">
<?php
if (!strcmp($_SESSION['type'], 'admin')) {
?>
<div class="desc"><?=$lang->UI_ADD_USER?></div>
    <?php 
    if (strlen($login->get_msg()) > 0)
        echo '<div class="msg">' . $login->get_msg() . '</div>';
    ?>

    <form action="?action=add_user" method="POST">
        <ul>
            <li>
                <label for="add_username"><?=$lang->UI_USERNAME?></label>
                <input type="text" name="add_username">
            </li>
            <li>
                <label for="add_password"><?=$lang->UI_PASSWORD?></label>
                <input type="password" name="add_password">
            </li>
            <li>
                <label for="repeat_password"><?=$lang->UI_REPEAT_PASSWORD?></label>
                <input type="password" name="repeat_password">
            </li>
            <li>
                <label for="add_type"><?=$lang->UI_USER_TYPE?></label>
                <select name="add_type">
                    <option value="admin"><?=$lang->UI_USER_ADMIN?></option>
                    <option value="user"><?=$lang->UI_USER_USER?></option>
                    <option value="observer"><?=$lang->UI_USER_OBSERVER?></option>
                </select>
            </li>
            <li>
                <input type="submit" value="<?=$lang->UI_ADD?>">
            </li>
        </ul>
    </form>
<?php 
} else {
    echo $lang->MSG_WARN_NO_PRIVILEGES;
}
?>
</section>


