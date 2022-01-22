<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Галерея</title>
    <style>
        uploader {
            border: 1px solid #ccc;
            box-shadow: 5px 5px 2px #aaa;
            display: inline-block;
            margin: 5px;
            padding: 5px;
        }

        gallery {
            display: block;
            background-color: snow;
        }

        gallery .picture {
            border: 1px solid salmon;
            box-shadow: 5px 5px 2px #aaa;
            display: inline-block;
            margin: 1vw;
            padding: 1vw;
        }

        gallery .picture img {
            max-width: 20vw;
        }

        gallery .picture b {
            display: block;
        }
    </style>
</head>

<body>
    <h1>Галерея</h1>
    <uploader>
        <input type="file" name="pictureFile" />
        <input name="pictureDescriptionUk" value="Ця найкраща!" />
        <input name="pictureDescriptionEn" value="The best one!" />
        <input name="pictureDescriptionRu" value="Это самая лучшая!" />
        <button name="addPicture">Добавить</button>
    </uploader>

    <gallery></gallery>

    <paginator>
        <button id="prevButton">Prev</button>
        <span id="currentPage">1</span>
        <button id="nextButton">Next</button>
    </paginator> 
    <div id="dataFilter">
        <input type="date" id="datePicker" />
        <br />
        <button id="applyFilter">Filter</button>
    </div>

    <div id="langSwitch">
        <select id="langSelect"></select>
        <button id="setLang">Set</button>
        <button id="unsetLang">Unset</button>
    </div>

    <footer>
        <?php
        echo "&copy; ITSTEP, КН-П-181, 2018 - "
            . date("Y");
        ?>
    </footer>

    <script src="gallery.js"></script>
</body>

</html>