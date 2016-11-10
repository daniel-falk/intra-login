<?php

class user
{
    public $user_name;
    public $user_type;
    public function __construct($user_name, $user_pass)
    {
        $this->user_name = $user_name;
        $this->user_pass = $user_pass;
    }
}

class Data
{
    public $username;
    public $user_type;
    public $valid_time;
    public $login_time;
    public $last_activity;
    public $logged_in;
    public $msg;
}

class login_module 
{
    private $msg = '';
    private $lang;
    private $php_ok = false;
    private $mysql_addr;
    private $mysql_user;
    private $mysql_pass;
    private $mysql_db;
    private $conn;

    public function __construct($lang)
    {
        $this->lang = $lang;
        if (session_status() == PHP_SESSION_NONE)
            start_secure_session(SECURITY::$USING_SSL);
        if ($this->check_minimum_php()) {
            $this->php_ok = true;
            $this->mysql_addr = DB::$ADDR; 
            $this->mysql_user = DB::$USER;
            $this->mysql_pass = DB::$PASS;
            $this->mysql_db = DB::$DB;
        }
    }

    public function get_user_name() 
    {
        if (isset($_SESSION['username']))
            return $_SESSION['username'];
        return null;
    }

    public function get_user_type()
    {
        if (isset($_SESSION['type']))
            return $_SESSION['type']; 
        return null;
    }

    public function get_login_time()
    {
        if (isset($_SESSION['login_time']))
            return $_SESSION['login_time'];
        return null;
    }

    public function get_last_activity()
    {
        if (isset($_SESSION['last_activity']))
            return $_SESSION['last_activity'];
        return null;
    }

    public function is_logged_in()
    {
        if (isset($_SESSION['is_logged_in']) 
            && isset($_SESSION['last_activity']) 
            && time() - $_SESSION['last_activity'] < SECURITY::$SESSION_TIMEOUT)
            return true;
        return false;
    }

    public function get_msg() 
    {
        return $this->msg;
    }

    public function get_data()
    {
        $data = new Data();
        $data->username = $this->get_user_name();
        $data->user_type = $this->get_user_type();
        $data->valid_time = SECURITY::$SESSION_TIMEOUT;
        $data->login_time = $this->get_login_time();
        $data->last_activity = $this->get_last_activity();
        $data->logged_in = $this->is_logged_in();
        $data->msg = $this->msg;
        return $data;
    }

    public function add_user($name, $pass, $pass2, $type)
    {
        if ($this->is_logged_in()){
            $this->msg = $this->lang->MSG_WARN_NO_PRIVILEGES;
            return false;
        }
        if (strcmp($pass, $pass2)){
            $this->msg = $this->lang->MSG_WARN_PASS_MATCH;
            return false;
        }
        // Try to connect to the database
        if (!$this->connect_to_db())
            return false;
        $ret = $this->put_new_user($name, $pass, $type);
        $this->close_db();
        return $ret;
    }

    public function check_logged_in($pass_through_interns) 
    {
        // Logout if requested
        if (isset($_GET['action']) && strcmp($_GET['action'],'logout')==0){
            $this->logout();
            return false;
        }
        // Check PHP version is good enough for the password encryption
        if (!$this->php_ok)
            return false;
        // Try to connect to the database
        if (!$this->connect_to_db())
            return false;
        // Make sure that there are atleast one user, in other case add one
        $this->build_table_structure();
        // If pass_through_interns is true then do an automatic login if client IP == server IP
        if ($pass_through_interns==true)
            $this->login_no_check('', SECURITY::$ANONYMOUS_PRIVILEGES);
        // Check if user login data is stored in a session
        if ((!empty($_SESSION['username']) || $pass_through_interns==true) && $this->is_logged_in()) {
            $_SESSION['last_activity'] = time();
            $this->close_db();
            return true;
        }	  
        // Check if login is requested (using POST data)
        $ret = false;
        if (isset($_GET['action']) && !strcmp($_GET['action'],'login'))
            if ($this->login())
                $ret = true;
        $this->close_db();
        return $ret;
    }

    private function check_same_network() 
    {
        // TODO ADD TRACE
        if (strcmp($_SERVER['SERVER_ADDR'],$_SERVER['REMOTE_ADDR'])==0)
            return true;
        return false;
    }

    private function logout() 
    {
        $_SESSION = array();
        session_destroy();
        $this->msg = $this->lang->MSG_FB_LOGGED_OUT;
    }

    // Login with username and pass from POST data
    private function login() 
    {
        // Check if username and pass is submitted
        if (!$this->check_login_data_not_empty())
            return false;
        // Check if username and password exists in database
        if ($user = $this->confirm_login_data_with_db()){
            $this->login_no_check($user->user_name, $user->user_pass);
            return true;
        }
        return false;
    }

    private function check_login_data_not_empty() 
    {
        if (!empty($_POST['username']) && !empty($_POST['user_password']))
            return true;
        else {
            if (empty($_POST['username']) && empty($_POST['user_password']))
                $this->msg = $this->lang->MSG_WARN_NO_USER_NO_PASS;
            else if (empty($_POST['username']))
                $this->msg = $this->lang->MSG_WARN_NO_USER;
            else
                $this->msg = $this->lang->MSG_WARN_NO_PASS;
        }
        return false;
    }

    private function connect_to_db() 
    {
        $this->conn = new mysqli($this->mysql_addr, $this->mysql_user, $this->mysql_pass, $this->mysql_db);
        if($this->conn->connect_error) {
            $this->msg = $this->lang->MSG_ERR_DATABASE;
            return false;
        }
        $this->conn->set_charset("utf8");
        return true;	
    }

    private function close_db() 
    {
        $this->conn->close();
    }

    private function confirm_login_data_with_db()
    {
        // Prepare to check if login is successfull
        $username = $this->conn->real_escape_string($_POST['username']); 
        $sql = "SELECT pass_hash, usertype FROM users WHERE username = '$username' LIMIT 1";
        $query = $this->conn->query($sql);
        // Prepare to add a login trace to the database
        $trace_sql = "INSERT INTO login_trace (ip, time, correct, username, pass_hash) VALUES (?, NOW(), ?, ?, ?)";
        // Check if user exists
        if ($query && $query->num_rows==1) {
            // Check if login is good 
            $row = $query->fetch_assoc();
            $user_pass_hash = $row['pass_hash'];
            if (password_verify($_POST['user_password'], $user_pass_hash)){
                $correct = 'yes';
                $user_pass_hash_log = '';
                $query->close();
                $trace_query = $this->conn->prepare($trace_sql);
                $trace_query->bind_param('ssss',$_SERVER['REMOTE_ADDR'],$correct, $username, $user_pass_hash_log);
                $trace_query->execute();
                $trace_query->close();
                return new user($username, $row['usertype']);
            }
            $correct = 'wrong_pass';
        } else
            $correct = 'wrong_user';
        // Bad login
        $query->close();
        $user_pass_hash_log = password_hash($_POST['user_password'], PASSWORD_BCRYPT); 
        $trace_query = $this->conn->prepare($trace_sql);
        $trace_query->bind_param('ssss',$_SERVER['REMOTE_ADDR'],$correct, $username, $user_pass_hash_log);
        $trace_query->execute();
        $trace_query->close();
        $this->msg = $this->lang->MSG_WARN_WRONG_LOGIN;
        return false;
    }

    // Creates the sessions to tell that user is loged in
    // without checking username or password
    private function login_no_check($username, $type) 
    {
        $_SESSION['username'] = $username;
        $_SESSION['type'] = $type; 
        $_SESSION['is_logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = $_SESSION['login_time'];
        return;
    }

    private function check_minimum_php() 
    {
        if (version_compare(PHP_VERSION, '5.5.0', '>='))
            return true;
        $this->msg = $this->lang->MSG_ERR_PHP_LOW;
        return false;
    }

    // Check if there are atleast one user in the users table
    // If table does not exist, add it.
    // If no users, add a new user with the default name and pass
    private function build_table_structure() 
    {
        // Check if login trace table exist
        $result = $this->conn->query("SHOW TABLES LIKE 'login_trace'");
        if (!$result || $result->num_rows==0)
            if (!$this->add_trace_table())
                return false;
        // Check if users table exist
        $result = $this->conn->query("SHOW TABLES LIKE 'users'");
        if (!$result || $result->num_rows==0)
            if (!$this->add_users_table())
                return false;
        // Check if atleast one user exist
        $query = "SELECT username FROM users";
        $result = $this->conn->query($query);
        if ($result && $result->num_rows > 0)
            return true;
        return $this->put_new_user(DEFAULTS::$USER_NAME, DEFAULTS::$USER_PASS, "admin");
    }

    // Add a new user to the allowed users table
    public function put_new_user($name, $pass, $type) 
    {
        $safe_name = $this->conn->real_escape_string($name);
        $safe_type = $this->conn->real_escape_string($type);
        if (strlen($safe_name)<=0 ||
            strlen($pass)<=0 || 
            strlen($safe_type)<=0) {
            $this->msg = $this->lang->MSG_WARN_NO_USER_NO_PASS;
            return false;
        }
        $query = "INSERT INTO users " .
            "(username, pass_hash, usertype) " .
            "VALUES ('$safe_name', '" . password_hash($pass, PASSWORD_BCRYPT) . "', '$safe_type');";
        if ($this->conn->query($query)) {
            $msg = $this->lang->MSG_USER_ADDED;
            return true;
        }
        $msg = $this->lang->MSG_ERR_USER_NOT_ADDED;
        return false; 
    }

    // Add the users table if it does not exist in the database
    // Also add a default (admin) user
    private function add_users_table() 
    {
        $query = 'CREATE TABLE users (' .
            'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
            'username VARCHAR(50) NOT NULL UNIQUE,' .
            'pass_hash VARCHAR(255) NOT NULL,' .
            'usertype VARCHAR(50));';
        if ($this->conn->query($query))
            return true;
        $msg = $this->lang->MSG_ERR_MYSQL_NEW_TABLE;
        return false;
    }

    // Add the login trace table to the database
    private function add_trace_table() 
    {
        $query = 'CREATE TABLE login_trace (' .
            'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
            'ip VARCHAR(45) NOT NULL,' .
            'time DATETIME NOT NULL,' . 
            'correct VARCHAR(10) NOT NULL,' .
            'username VARCHAR(50) NOT NULL,' .
            'pass_hash VARCHAR(255));';
        if ($this->conn->query($query))
            return true;
        $msg = $this->lang->MSG_ERR_MYSQL_NEW_TABLE;
        return false;
    }
}

?>
