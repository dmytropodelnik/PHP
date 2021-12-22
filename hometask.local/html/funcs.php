<?php

function print_privacy($text = null)
{
    if ($text == null) {
        echo "<h1>Privacy</h1>";
    } else {
        echo "<h1>Privacy $text</h1>";
    }
}

function print_about($text = null)
{
    if ($text == null) {
        echo "<h1>About</h1>";
    } else {
        echo "<h1>About $text</h1>";
    }
}
