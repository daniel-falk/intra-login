<?php

class DB {
    public static $ADDR='localhost';
    public static $USER='**';
    public static $PASS='**';
    public static $DB='**';
}

class DEFAULTS {
    public static $USER_NAME = 'admin';
    public static $USER_PASS = 'password';
}

class SECURITY {
    public static $USING_SSL = false; // Is the page accessed over https?
    public static $ANONYMOUS_PRIVILEGES = "observer"; // Privileges for users logging in with the same ip system
}

?>
