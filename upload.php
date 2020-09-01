<?php 
    $ziel_ordner = "uploads/";
    $ziel_datei = $ziel_ordner . basename($_FILES["bild"]["name"]);
    $uploadOk = 1;

    $imageFileType = strtolower(pathinfo($ziel_datei, PATHINFO_EXTENSION));

    //check ob Datei ein Bild ist!
    if( isset( $_POST["submit"] ) ){
        $check = getimagesize($_FILES["bild"]["tmp_name"]);

        if( $check !== false ){
            echo "Die Datei ist ein Bild - " . $check["mime"] . ".<br>";
            $uploadOk = 1;
        } else{
            echo "Datei ist KEIN Bild!<br>";
            $uploadOk = 0;
        }
    }

    //nur bestimmte Bilder hochladen
    if($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png"){
        echo "Das Bildformat wird nicht unterstützt!<br>";
        $uploadOk = 0;
    }

    //Dateigröße
    if( $_FILES["bild"]["size"] > 500000 ){
        echo "Die Datei ist zu groß!<br>";
        $uploadOk = 0;

    } elseif( $_FILES["bild"]["size"] < 1){
        echo "Die Datei ist kaputt!<br>";
        $uploadOk = 0;
    }

    //Ist Bild bereits vorhanden?
    if( file_exists($ziel_datei) ){
        $random = random_int(1, 9999999);
        echo "Der Dateiname: '" . basename($_FILES["bild"]["name"]). "' existiert bereits.<br>";
        $neuer_name = $random . "_" . basename($_FILES["bild"]["name"]);
        $ziel_datei = $ziel_ordner . $neuer_name;
        echo "Die Datei wird umbenannt: '" . $neuer_name . "'<br>";
        //$uploadOk = 0;
    }

    //Ab hier wird hochgeladen:
    if( $uploadOk == 0 ){
        echo "Die Datei wird nicht hochgeladen.<br>";
    } else{
        //wenn Ordner nicht vorhanden dann erstelle einen:
        if( !(is_dir($ziel_ordner)) ){
            mkdir($ziel_ordner);
        }

        //Hole die Qualität von der HTML:
        $quality = $_POST["quality"];
        //Kompression ausführen:
        compress($ziel_datei, $quality);
    }

    //=====================================================================
    //Ab hier wird die Datei komprimiert!
    function compress($ziel_datei, $quality){
        $info = getimagesize($_FILES["bild"]["tmp_name"]);

        if( $info["mime"] == "image/jpeg" ){
            $image = imagecreatefromjpeg($_FILES["bild"]["tmp_name"]);
        }
        elseif( $info["mime"] == "image/png" ){
            $image = imagecreatefrompng($_FILES["bild"]["tmp_name"]);
        }
        elseif( $info["mime"] == "image/jpg" ){
            $image = imagecreatefromjpg($_FILES["bild"]["tmp_name"]);
        }
        else{
            echo "Es ist ein Fehler beim Komprimieren aufgetreten!<br>";
            return false;
        }

        //echo "Die Datei wird um " . (100-$quality) . "% komprimiert.<br>";
        
        $vorher = $_FILES["bild"]["size"];
        imagejpeg($image, $ziel_datei, $quality);
        $nachher  = filesize($ziel_datei);

        vergleich($vorher, $nachher);
        echo "Sie finden die Datei unter '<b>" . realpath($ziel_datei) . "</b>'";
        return $ziel_datei;
    }
    //=====================================================================

    //Vergleich Vorher und Nachher Dateigröße
    function vergleich($vorher, $nachher){
        //BYTES IN KB
        $vorher_kb = floor($vorher/1024);
        echo "<b>VORHER: " . $vorher_kb . " KB.</b><br>";

        $nachher_kb = floor($nachher/1024);
        echo "<b>Nachher: " . $nachher_kb . " KB.</b><br>";

        $prozent = floor( ($nachher_kb/$vorher_kb)*100 );
        echo "~" . (100-$prozent) . "% komprimiert.<br>";
    }

    
?>