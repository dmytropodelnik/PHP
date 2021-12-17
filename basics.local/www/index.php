<?php
$x = 10 ;

@include_once "funcs.php" ;
if( ! function_exists( "print_hello" ) ) {
	echo "<i>Runtime error </i>" ;
	exit ;
}

function print_x() {
	global $x ;
	
	echo $x ;
}

$f = function() {
	return "Hello" ;
} ;

include "view.php" ;
