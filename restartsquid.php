<?php
/**
 * Call the Restart (actually reconfigure) Squid shell script via php
 * NB: You will need to add an entry to /etc/sudoers for this to work, and restrict security problems
 *
 * visudo /etc/sudoers and add the following two lines.
 * # Allow www-data to reconfigure (reload cache?) of Squid
 * www-data ALL = NOPASSWD:/var/www/lists/restartsquid.sh
 *
 *
 * @author Justin Swan <jswan@skyreach.com.au>
 *
 * @return string output of shell command if failure occurred, else empty string.
 */
function restartSquid() {
	$output = shell_exec("sudo ".dirname(__FILE__)."/restartsquid.sh");
	return $output;
}
// Test if this is running correctly by uncommenting the following lines.
//
//$restartStatus = restartSquid();
//echo $restartStatus;
