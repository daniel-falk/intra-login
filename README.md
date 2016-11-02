# intra-login
Easy to use login module for your private pages or intranet. Written in PHP.

# Usage
Copy the files to a web directory on your server. Change the settings in `config_params.php` to match your mysql database. Set default password and username that will be used to create the first admin. This is done the first time you visit the webpage.

# TODO
* show time until logout
* Answer with a warning if new user already exist
* Add a table with allowed users with "change password" and "remove" that only admins can see
* add new user -> no confirmation!?
* Safety:
 - (DONE) Protect against injection [1]
 - Securely start a session
 - Protect against brute force [2]
 - Make sure browser is the same [3]
* Add more languages

1. Done using real_escape_string on the vars.
2. Check IP in the login trace table and force a 30 second wait after 5 wrongs? People can change IP using proxy?
3. Store the `$_SERVER[HTTP_USER_AGENT]` hash value in a session. On each check of the session values this is comparerd to the current hash of the string.

# Coding style:

## Functions
A function is defined with return type and name at the same row (makes content search easier).
Starting bracket is placed on its own line.
Example:
```
int foo(int arg1)
{
    ..
}
```

Functions with many arguments can be broken to new lines
```
int foo2(int arg1,
        int arg2,
        int arg3)
{
    ...
}
```

## Conditional statements
Conditional statements should not have brackets if only one line of code. If either one of the statement alternatives have multiple rows then both can have brackets. Example:
```
if (myVar == true){
    ...
} else {
    ...
    ...
    ...
}
```
