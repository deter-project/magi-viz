<?php
if(!isset($_GET['host']))
{
	die('Remote host not provided');
}

$host = $_GET['host'];
$hash = base_convert(md5($host), 16, 10);
$substring = substr($hash, 0,16);
$lport = 20000 + ($substring % 20000);

$rport = 27017;

$ssh_cmd = "ssh muser@users.isi.deterlab.net -L $lport:$host:$rport -f -o ExitOnForwardFailure=yes -N";

#Output needs to redirected for the command execution 
#to return after setting up the tunnel
$ssh_cmd_no_out = "$ssh_cmd > /dev/null";
//echo $ssh_cmd_no_out;

#Try to create ssh tunnel
exec($ssh_cmd_no_out, $output, $ret);
//echo $ret;
//print_r($output);

#If tunnel creation fails
if($ret){
	#Check if the required tunnel is already setup
	$check_cmd = "ps -ef | grep '$ssh_cmd' | grep -v grep";
	
	//echo $check_cmd;
	exec($check_cmd, $output, $ret);
	
	//echo print_r($output);
	#If some other process is using up the port
	if(!$output){
		die('Connection failed');
	}
}

echo $lport;

// $connection = ssh2_connect('users', 22);
// if (!$connection) die('Connection failed');
// $tunnel = ssh2_tunnel($connection, 'c-0.feedbackc.montage', 27018);

?>