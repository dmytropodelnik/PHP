<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP API</title>
</head>
<body>
    <footer>
        <h1>Панель диагностики API</h1>
        <button id="testGetButton">GET</button>
        <button id="testPostButton">POST</button>
        <button id="testPutButton">PUT</button>
        <button id="testDeleteButton">DELETE</button>
        <p id="out"></p>
        <hr/>
        <?php

        echo "&copy; ITSTEP, KH-П-181, 2018 - " . date("y") . "<br/>";
        print_r($_GET);
        ?>
    </footer>
    <script src="index.js"></script>
</body>
</html>




