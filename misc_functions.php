<?php

function start_secure_session($using_ssl)
{
    session_name("login_session");
    ini_set('session.cookie_httponly',1);
    ini_set('session.use_only_cookies',1);
    $prms = session_get_cookie_params();
    session_set_cookie_params($prms["lifetime"],
        $prms["path"], 
        $prms["domain"], 
        $using_ssl,
        true);
    session_start();
    session_regenerate_id(true); 
}

?>
