<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Demo</title>

    <a href="http://jquery.com/">jQuery</a>
    <script src="jquery-1.10.1.min.js"></script>

    <script>
    $( document ).ready(function() {
        console.log( "document loaded" );
    });
 
    $( window ).load(function() {
        console.log( "window loaded" );
    });
    </script>
</head>
<body>
    <?php
    $arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);

    echo json_encode($arr);
    ?>
</body>
</html>
