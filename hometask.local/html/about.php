<?php
@include_once "funcs.php";

if (function_exists("print_about")) {
    print_about();
    print_about("default parameter");
    print_about("123");
    exit;
}
