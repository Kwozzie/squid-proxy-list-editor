<?php
/**
 * Main login page.
 *
 * @author Justin Swan
 * @created 22 Feb 2013
 *
 * @todo testing, ensuring security and cross browser functionality. Move username and password info to db table?
 *
 */
if(!isset($_SESSION)) session_start();
$errors = array();
if(isset($_GET["out"])){
    $_SESSION = array();
    session_destroy();
    array_push($errors, "Successfully logged out");
}

$user_hash = array();
$user_hash['justin']['hash'] = "2e73e57397a3c823c58868c5921bbca0129b3bc536941ecdbdd8499ce8addff17e14dc9dcd2d3776d66118008c48f3685714b2ca887abf2b071dcd92e1ab15ee";
$user_hash['justin']['salt'] = "dfaax!@#adfkcka;12376658dlkfjadf";
// echo password_hash("username","password","yourownsalt"); // to make your own hash for $user_hash

function password_hash($username, $password, $salt) {
    return hash("SHA512", $username . $password . $salt );
}


if(isset($_POST['proxy_editor_username']) && isset($_POST['proxy_editor_password'])){
    if(empty($_POST['proxy_editor_username']) || empty($_POST['proxy_editor_password'])){
        array_push($errors, "Username and password are both required");
    } else {

        $username = filter_input(INPUT_POST, 'proxy_editor_username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'proxy_editor_password', FILTER_SANITIZE_STRING);

        if(array_key_exists($username, $user_hash)){
            if(password_hash($username,$password,$user_hash[$username]["salt"]) == $user_hash[$username]["hash"]){
                $_SESSION['proxy_editor_admin'] = true;
                header("Location: index.php");
            }
        }

        // if it made it this far, username and/or password are invalid. Don't give more information than needed by saying only one of these is wrong.
        array_push($errors, "Invalid username/password");
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Squid Proxy List Editor - Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="styles.css" />
        <link rel="stylesheet" type="text/css" href="chosen/chosen.css" />
        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <div>
            <header><h1>Squid Proxy List Editor - Login</h1></header>
            <section id="growl"><div><?php
            if(count($errors)>0){
                foreach($errors as $error) echo $error."<br>";
            }
            ?></div></section>
            <section id="login">
                <form method="post" action="login.php">
                    <div class="input_wrapper"><label>Username:</label><input name="proxy_editor_username" type="text" value="" placeholder="" /></div>
                    <div class="input_wrapper"><label>Password:</label><input name="proxy_editor_password" type="password" value="" placeholder="" /></div>
                    <div class="submit_wrapper"><input type="submit" name="submit" value="Login"></div><br style="clear:both">
                </form>
            </section>
            <footer></footer>
        </div>
        <script src="jquery-1.9.0.min.js"></script>
        <script src="chosen/chosen.jquery.js"></script>
        <script src="jscript.js"></script>
    </body>
</html>
