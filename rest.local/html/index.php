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

        <div style="border: 1px solid salmon; margin: 2vh 0; padding: 1">
            <input type="file" name="userFile" />
            <button id="filePostButton">POST</button>
            <button id="filePutButton">PUT</button>
        </div>

        <div style="border: 1px solid darksalmon; margin: 2vh 0; padding: 1">
            <button id="localeUaButton">Locale: Ua</button>
            <button id="localeEnButton">Locale: En</button>
            <button id="localeRuButton">Locale: Ru</button>
            <button id="localeFrButton">Locale: Fr</button>
            <button id="noLocaleButton">No Locale</button>
        </div>

        <p id="out"></p>
        <hr />
        <?php

        echo "&copy; ITSTEP, KH-П-181, 2018 - " . date("y") . "<br/>";
        print_r($_GET);
        ?>
    </footer>
    <script src="index.js"></script>
</body>

</html>