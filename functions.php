<?php
/**
 * Main file for ajax calls.
 *
 * @author Justin Swan
 * @created 22 Feb 2013
 *
 * @todo testing, ensuring security. Add username and password functionality?
 *
 */
if(!isset($_SESSION)) session_start();
$errors = array();
$response = "";

if(!isset($_SESSION['proxy_editor_admin'])) array_push($errors,"You must be logged in to perform this action <script>window.location = 'login.php?out';</script>.");
include dirname(__FILE__)."/mydb.php";
$db = new mydb();

/**
 * backup existing lists and regenerate new ones.
 * @author Justin Swan 2013
 */
function regenerate_lists(){
    global $errors, $db, $response;

    // check that main backup dir exists, if not, make it.
    if(!file_exists(BACKUP_FOLDER)){
        if(!mkdir(BACKUP_FOLDER,0755)) {
            array_push($errors, "Generate Lists Failed: Failed to create backups directory.");
            return false;
        }
    }

    // check that it is writeable (user may have man made backup directory)
    if(!is_writable(BACKUP_FOLDER)){
        if(!chmod(BACKUP_FOLDER,0755)){
            array_push($errors, "Generate Lists Failed: ".BACKUP_FOLDER." is NOT writeable");
            return false;
       }
    }

    // get lists
    $result = $db->query("SELECT * FROM access_lists");
    if(!$result || $result->num_rows == 0){
        array_push($errors, "Generate Lists Failed: Can't read access list names from database. ".$db->error);
        return false;
    }

    // for each list, backup if file exists, create if not, get domains from db and write to list file.
    while($access_list = $result->fetch_assoc()){
        $list_file = LIST_FOLDER."/".$access_list['file_name'];
        if(file_exists($list_file)){
            if (!copy($list_file, BACKUP_FOLDER."/".$access_list['file_name'])) {
                array_push($errors, "Generate Lists Failed: Unable to make backup copy of ".htmlentities($list_file));
                return false;
            }
        } else {
            if(!touch($list_file)){
                array_push($errors, "Generate Lists Failed: Unable to create file ".htmlentities($list_file));
                return false;
            } else {
                if(!chmod($list_file,0755)){
                    array_push($errors, "Generate Lists Failed: Unable to set 'write' permissions for file ".htmlentities($list_file));
                    return false;
                }
            }
        }

        // get new contents for list
        $domains_sql = "SELECT * FROM proxy_domains WHERE access_list = ".$db->filter($access_list['id'],"int");
        $domains_result = $db->query($domains_sql);
        if(!$domains_result){
            array_push($errors, "Generate Lists Failed: Unable to read domains for list ".htmlentities($access_list['list_name']));
            return false;
        }

        $access_list_content = "";
        if($domains_result->num_rows>0){
            while($domain = $domains_result->fetch_assoc()){
                $access_list_content.=$domain['domain']."\n";
            }
        }

        // now overwrite contents of original file.
        $fp = fopen($list_file, 'w+');
        if(!$fp){
            array_push($errors,"Unable to write to file ".htmlentities($list_file));
            return false;
        }
        fwrite($fp, $access_list_content);
        fclose($fp);

    }
    $response = "ok";


    // get each list

}

function get_proxy_list($list_id){

    global $errors, $db, $response;

    $sql = "SELECT * FROM proxy_domains WHERE access_list = ".$db->filter($list_id,"int");
    $result = $db->query($sql);
    if(!$result) {
        array_push($errors,$db->error);
        return false;
    }

    $response.="<table width='100%' cellpadding=0 cellspacing=0 id='list_table'><tr><th>Domain</th><th width='20'><input type='checkbox' name='select_all_domains' value='all'></th><th width='20'>&nbsp;</th><th width='20'>&nbsp;</th></tr>";

    if($result->num_rows >0){

         while($domain = $result->fetch_assoc()) {
            $response.="<tr>";
            $response.="<td>".$domain['domain']."</td>";
            $response.="<td><input type='checkbox' name='domain[]' value='".$domain['domain']."'></td>";
            $response.="<td><a href='functions.php?request=move_domain&domain=".$domain['domain']."' title='Change list for &quot;".$domain['domain']."&quot;?' class='move_domain' data-domain='".$domain['domain']."'><img src='images/move_lists.png' width='16' /></a></td>";
            $response.="<td><a href='functions.php?request=delete_domain&domain=".$domain['domain']."' title='Delete &quot;".$domain['domain']."&quot;?' class='delete_domain'><img src='images/delete_red.png' width='16' /></a></td>";
            $response.="</tr>";
        }

    } else {
        $response.= "<tr><td colspan='4'>No domains found</td></tr>";
    }
    $response.= "</table>";
}

function add_new_domain($new_domain, $list){
    global $errors, $db, $response;

    $new_domain = strtolower(trim($new_domain));

    // remove https:// http:// and www
    $new_domain = str_replace(array("http://","https://","www"),"",$new_domain);
    if(!checkdnsrr(ltrim($new_domain,"."))){
        array_push($errors,$new_domain." doesn't exist");
        return false;
    }

    if(substr($new_domain,0,1) != ".") $new_domain = ".".$new_domain;

    $sql = "SELECT * FROM proxy_domains JOIN access_lists ON proxy_domains.access_list = access_lists.id WHERE domain = ".$db->filter($new_domain,"text");
    $result = $db->query($sql);
    if(!$result) {
        array_push($errors,"Database error: ".$db->error);
        return false;
    }
    if($result->num_rows >0){
        $lists = array();

        while($list = $result->fetch_assoc()){
            array_push($lists,$list['list_name']);
        }
        array_push($errors,"This domain is already listed in '".implode(",",$lists)."'");
        $result->close();
        return false;
    }
    $result->close();



    // all checks passed, do the insert
    $insert_sql = "INSERT INTO proxy_domains (domain, access_list) VALUES (".$db->filter($new_domain,"text").",".$db->filter($list,"int").")";
    $inserted = $db->query($insert_sql);
    if(!$inserted || $db->affected_rows == 0) {
        array_push($errors,"Failed to add new domain.".$db->error);
        return false;
    }

    regenerate_lists();

}

function move_domain($domain,$list){
    global $errors, $db, $response;
    $sql = "UPDATE proxy_domains SET access_list = ".$db->filter($list,"int")." WHERE domain = ".$db->filter($domain,"text");
    $moved = $db->query($sql);

    if(!$moved || $db->affected_rows == 0 ) {
        array_push($errors,"Failed to move domain.".$db->error);
        return false;
    }
    regenerate_lists();
}

function move_multi_domains($domains,$list){
    global $errors, $db, $response;

    $sql = "UPDATE proxy_domains SET access_list = ".$db->filter($list,"int")." WHERE ";
    foreach($domains as $d) $sql.="domain = ".$db->filter($d,"text")." OR ";
    $updated = $db->query(rtrim($sql," OR "));

    if(!$updated || $db->affected_rows == 0 ) {
        array_push($errors,"Failed to move domains.".$db->error);
        return false;
    }
    regenerate_lists();
}

function delete_domain($domain){
    global $errors, $db, $response;
    $sql = "DELETE FROM proxy_domains WHERE domain =  ".$db->filter($domain,"text");
    $deleted = $db->query($sql);
    if(!$deleted || $db->affected_rows == 0) {
        array_push($errors,"Failed to delete domain.".$db->error);
        return false;
    }
    regenerate_lists();
}

function delete_multi_domains($domains){
     global $errors, $db, $response;
    $sql = "DELETE FROM proxy_domains WHERE ";
    foreach($domains as $d) $sql.="domain = ".$db->filter($d,"text")." OR ";
    $deleted = $db->query(rtrim($sql," OR "));

    if(!$deleted || $db->affected_rows == 0 ) {
        array_push($errors,"Failed to delete domains.".$db->error);
        return false;
    }
    regenerate_lists();
}

if(isset($_GET['request'])){

    switch($_GET['request']){
        case "get_proxy_list":
            if(isset($_GET["list_id"])) {
                get_proxy_list($_GET["list_id"]);
            } else array_push($errors, "A list id must be specified.");
            break;
        case "add_new_domain":
            if(isset($_GET['new_domain']) && isset($_GET['list'])){
                add_new_domain($_GET['new_domain'],$_GET['list']);
            } else array_push($errors, "New domain and list are required");
            break;
        case "move_domain":
            if(isset($_GET['domain']) && isset($_GET['list'])){
                move_domain($_GET['domain'],$_GET['list']);
            } else array_push($errors, "Domain and list are required");
            break;
        case "move_multi_domains":
            if(isset($_GET['domains']) && isset($_GET['list'])){
                move_multi_domains($_GET['domains'],$_GET['list']);
            } else array_push($errors, "Domain(s) and list are required");
            break;
        case "delete_domain":
            if(isset($_GET['domain'])){
                delete_domain($_GET['domain']);
            } else  array_push($errors, "Please specify a domain to delete.");
            break;
        case "delete_multi_domains":
            if(isset($_GET['domains'])){
                delete_multi_domains($_GET['domains']);
            } else  array_push($errors, "Please domains to delete.");
            break;
        case "regenerate_lists":
            regenerate_lists();
            break;
        default:
            array_push($errors, htmlentities($_GET['request'],ENT_QUOTES)." is an invalid action.");
    }

} else {
    array_push($errors, "No action specified");
}

//array_push($errors,"test error");
if(count($errors)>0){
    echo "error:";
    foreach($errors as $error) echo $error."<br>";
} else {

    echo $response;
}
//$db->close();

?>
