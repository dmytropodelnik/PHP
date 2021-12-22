<?php
@include_once "funcs.php";

if (function_exists("print_privacy")) {
    print_privacy();
    print_privacy("default parameter");
    exit;
}
