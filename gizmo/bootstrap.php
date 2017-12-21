<?php
spl_autoload_extensions('.php,.class.php');
spl_autoload_register();

function pr($var) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}
?>
