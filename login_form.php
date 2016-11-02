
<section class="login_form cf">

    <?php
    if (strlen($login->get_msg()) > 0)
        echo '<div class="msg">' . $login->get_msg() . '</div>';
    ?>

    <form name="login" action="?action=login" method="POST" onsubmit="return validateForm()">
        <ul>
            <li>
                <label for="username"><?=$lang->UI_USERNAME?></label>
                <input type="text" name="username" placeholder="<?=$lang->UI_USERNAME_DESC?>">
            </li>
            <li>
                <label for="user_password"><?=$lang->UI_PASSWORD?></label>
                <input type="password" name="user_password" placeholder="<?=$lang->UI_PASSWORD_DESC?>">
            </li>
            <li>
                <input type="submit" value="<?=$lang->UI_LOGIN?>">
            </li>
        </ul>
    </form>
</section>
