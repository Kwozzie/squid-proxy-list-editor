<?php

/**
 * Database connection.
 *
 * @author Justin Swan
 * @created 22 Feb 2013
 *
 * @todo testing, ensuring security and cross browser functionality. Move username and password info to db table?
 *
 */
require_once "ple_config.php";
if (!class_exists("mydb")) {

    class mydb extends mysqli {

        public function __construct() {

            parent::__construct(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if (mysqli_connect_error()) {
                //echo 'Error: Connection Error (' . mysqli_connect_errno() . ')';
                //echo mysqli_connect_error();
                die("Database connection failed");
            }
        }

        // dreamweavers function renamed for easier use
        public function filter($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

            $theValue = $this->real_escape_string($theValue);

            switch ($theType) {
                case "text":
                    $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                    break;
                case "long":
                case "int":
                    $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                    break;
                case "double":
                    $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
                    break;
                case "date":
                    $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                    break;
                case "defined":
                    $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                    break;
            }
            return $theValue;
        }

        public function __destructor() {
            $this->close();
        }

    }

}
?>
