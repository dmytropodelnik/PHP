<?php

// echo "<pre>"; print_r($_SERVER);

// Looking for 'Locale' header
if (isset($_SERVER['HTTP_LOCALE'])) {
    switch(strtolower($_SERVER['HTTP_LOCALE'] )) {
        case 'ua': echo "Вітання"; break;
        case 'en': echo "Greetings"; break;
        case 'ru': echo "Приветствие"; break;
        default:   echo "Locale undefined"; 
    }
}
else {
    echo "No Locale header";
}