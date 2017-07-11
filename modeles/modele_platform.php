<?php

function platform_exist($idPlatform, &$errorMessage) {
    $errorMessage = "";
    $exist = false;
    if (empty($idPlatform) || empty($idPlatform)) {
        $errorMessage = "<Error>A platform is required</Error>\n";
    } else {
        if ($platformQuery = mysql_query(" SELECT id FROM platforms WHERE id = '$idPlatform' ")) {
            if ($platformResult = mysql_fetch_object($platformQuery)) {
                $exist = true;
            } else {
                $errorMessage = "<Error>Platform Id not found</Error>";
            }
        }
    }
    return $exist;
}

?>