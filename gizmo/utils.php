<?php

function dump($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
};

function folder($array) {
	return implode(DIRECTORY_SEPARATOR, $array);
}

?>
