<?php

class language 
{
    public $MSG_ERR_EMPTY_FORM;
    public $UI_USERNAME;
    public $UI_USERNAME_DESC;
    public $UI_PASSWORD;
    public $UI_REPEAT_PASSWORD;
    public $UI_PASSWORD_DESC;
    public $UI_ADD;
    public $UI_LOGIN;
    public $UI_LOGOUT;
    public $UI_USER_TYPE;
    public $UI_USER_ADMIN;
    public $UI_USER_USER;
    public $UI_USER_OBSERVER;
    public $UI_ADD_USER;
    public $UI_ALLOWED_USERS;
    public $MSG_FB_NEW_USER_ADDED;
    public $MSG_ERR_PHP_LOW;
    public $MSG_ERR_MYSQL_NEW_TABLE;
    public $MSG_ERR_USER_NOT_ADDED;
    public $MSG_ERR_DATABASE;
    public $MSG_WARN_NO_USER_NO_PASS;
    public $MSG_WARN_NO_USER;
    public $MSG_WARN_NO_PASS;
    public $MSG_WARN_WRONG_LOGIN;
    public $MSG_WARN_PASS_MATCH;
    public $MSG_WARN_NO_PRIVILEGES;
    public $MSG_FB_LOGGED_OUT;
    public $MSG_USER_ADDED;

    public function __construct($language) 
    {
        if (!strcmp($language, "EN")) {

            // ------ Login form ---------

            $this->MSG_ERR_EMPTY_FORM="Please fill out the username";

            $this->UI_USERNAME="Username";
            $this->UI_USERNAME_DESC="Your login name";
            $this->UI_PASSWORD="Password";
            $this->UI_REPEAT_PASSWORD="Repeat password";
            $this->UI_PASSWORD_DESC="Your password";
            $this->UI_ADD="Add";
            $this->UI_LOGIN="Login";
            $this->UI_LOGOUT="Logout";
            $this->UI_USER_TYPE="User privileges";
            $this->UI_USER_ADMIN="Admin";
            $this->UI_USER_USER="User";
            $this->UI_USER_OBSERVER="Observer";
            $this->UI_ADD_USER="Add a new user";
            $this->UI_ALLOWED_USERS="Allowed users";

            // ----- Login Module -------  

            // ERROR MSGS
            $this->MSG_ERR_PHP_LOW="PHP version is too low to perform login check. Ask system administrator to upgrade to version 5.5.0 or higher.";
            $this->MSG_ERR_MYSQL_NEW_TABLE="Could not create the users table. Check database permissions.";
            $this->MSG_ERR_USER_NOT_ADDED="Could not add user. Check database permissions.";
            $this->MSG_ERR_DATABASE="Can't log into the database";
            // WARNINGS
            $this->MSG_WARN_NO_USER_NO_PASS="You did not submit a username and password.";
            $this->MSG_WARN_NO_USER="You did not submit a username.";
            $this->MSG_WARN_NO_PASS="You did not submit a password.";
            $this->MSG_WARN_WRONG_LOGIN="Password and/or username is wrong.";
            $this->MSG_WARN_PASS_MATCH="Passwords do not match";
            $this->MSG_WARN_NO_PRIVILEGES="You do not have the privileges to acces these settings.";
            // FEED BACK MSGS
            $this->MSG_FB_LOGGED_OUT="You are now logged out.";
            $this->MSG_USER_ADDED="New user successfully added.";
            $this->MSG_FB_NEW_USER_ADDED="New user successfully added.";
        }
    }
}
?>
