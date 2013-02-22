<?php
/**
 * Main display page for proxy list editing.
 *
 * @author Justin Swan
 * @created 22 Feb 2013
 *
 * @todo testing, ensuring security and cross browser functionality.
 *
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (!isset($_SESSION))
    session_start();
if (!isset($_SESSION['proxy_editor_admin'])) {

    header("Location: login.php");
}
require_once("mydb.php");

$db = new mydb();
$access_list = array();
$al_sql = "SELECT * FROM access_lists";
$al_result = $db->query($al_sql) or die($db->error);
while ($al = $al_result->fetch_assoc()) {
    $access_list[] = $al;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Squid Proxy List Editor</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="styles.css" />
        <link rel="stylesheet" type="text/css" href="chosen/chosen.css" />
        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <div>
            <header><h1>Squid Proxy List Editor</h1></header>
            <section id="growl"><div></div></section>

            <section id="list">
                <nav>
                    <form method="post" action="index.php">
                        <div class="field">
                            <label>Access List:</label> <select name="access_list" class="chzn-select">
                            <?php
                            foreach($access_list as $al) {
                                echo "<option value=" . $al["id"] . ">" . $al["list_name"] . "</option>";
                            }
                            ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>New Domain:</label>
                            <div class="input_wrapper"><input type="text" name="new_domain" value=""/></div>
                            <input type="image" src="images/list_add.png" value="save" width="24" id="add_domain"/>
                        </div>
                        <br style="clear:both">
                    </form>
                </nav>
                <div id="list_container"></div>
                <form method="get" action="functions.php?request=change_domain_list" id="change_list_form">
                    <a class="close_label" href=""><img alt="Close" src="images/popup-close.png"></a>
                    <label>Move</label><div class="input_wrapper"><input type="text" id="change_list_input" name="change_list_input" value="" readonly="readonly"></div>
                    <label>to</label><div class="select_wrapper"><select name="change_list_select" id="change_list_select" class="chzn-select">
                            <?php
                            foreach($access_list as $al) {
                                echo "<option value=" . $al["id"] . ">" . $al["list_name"] . "</option>";
                            }
                            ?>
                            </select></div><input type="image" name="change_list_save" id="change_list_save" src="images/save.png" value="Save">
                </form>
                <div id="multi_change_form">
                <form method="post" action="functions.php?request=multi_move">
                        <label>Move selected sites to</label><div class="select_wrapper"><select name="change_multi_list_select" id="change_multi_list_select" class="chzn-select">
                            <?php
                            foreach($access_list as $al) {
                                echo "<option value=" . $al["id"] . ">" . $al["list_name"] . "</option>";
                            }
                            ?>
                            </select></div><input type="image" name="change_multi_list_save" id="change_multi_list_save" src="images/move_lists.png" value="Save" width="24" alt="Move all selected domains?" title="Move all selected domains?">
                            <input name="delete_selected_domains" src="images/delete_red.png" type="image" id="delete_selected_domains" width="24" alt="Delete all selected domains?" title="Delete all selected domains?">
                </form>

                </div><br style="clear:both">
            </section>
             <footer></footer>
        </div>
        <a id="logged_in" href="login.php?out">Log Out</a>
        <script src="jquery-1.9.0.min.js"></script>
        <script src="chosen/chosen.jquery.js"></script>
        <script src="jquery.joverlay.min.js"></script>
        <script src="jscript.js"></script>
    </body>
</html>