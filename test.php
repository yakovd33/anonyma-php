<?php
    if (isset($_GET['tag'])) {
        print_r($_GET);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>
    <body>
        <form action="test.php" method="GET">
            <input type="checkbox" value="lalala" name="tag[]">
            <input type="checkbox" value="hehehe" name="tag[]">
            <input type="checkbox" value="hahaha" name="tag[]">
            <input type="submit">
        </form>
    </body>
</html>