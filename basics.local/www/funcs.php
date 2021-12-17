<?php

function print_hello( $name = null ) {
	if( $name == null ) {
		echo "<h1>Basics local ready</h1>" ;
	} else {
			echo "<h1>Basics local ready, $name</h1>" ;
	}
}

