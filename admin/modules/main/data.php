<?php

	// Set a cookie for the chosen color. Return nothing.
	if (isset($_POST['color']) && $_POST['color'] != "") {
		setcookie("admincolor", $_POST['color'], time()+31556926);
	}

?>