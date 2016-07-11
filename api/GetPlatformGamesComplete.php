<?php

## Interface that allows clients to get
## platform list using platform
## Parameters:
##   $_REQUEST["platform"]
##   $_REQUEST["language"]		(optional)
##   $_REQUEST["user"]			(optional... overrides language setting)
##
## Returns:
##   XML items holding the games that matches the platform string
## Include functions, db connection, etc
include("include.php");
include('../simpleimage.php');
include($_SERVER["DOCUMENT_ROOT"] . "/modeles/modele_platform.php");
include($_SERVER["DOCUMENT_ROOT"] . "/api/api_function.php");

//Parameters
$platform = $_REQUEST["platform"];
$user = $_REQUEST["user"];

//check if platform exist
if (!platform_exist($platform, $errorMessage)) {
    print $errorMessage;
    exit;
}

$query = "SELECT g.id, g.GameTitle, g.Alternates, p.id as PlatformId";
$query .= ", p.name as Platform, g.ReleaseDate, g.Overview, g.Rating as ESRB";
$query .= ", g.Genre, g.Players, g.coop as 'Co-op', g.Youtube, g.Publisher";
$query .= ", g.Developer, g.Actors, AVG(r.rating) as Rating";
$query .= ", g.os, g.processor, g.ram, g.hdd, g.video, g.sound";
$query .= " FROM games as g LEFT JOIN ratings as r ON (g.id=r.itemid and r.itemtype='game'), platforms as p WHERE p.id={$platform} AND p.id = g.platform Group By g.id";
//print "<Query>\n";
//print $query . "\n";
//print "</Query>\n";

$result = mysql_query($query) or die('<ErrorQuery>Query failed: ' . mysql_error() . '</ErrorQuery>');
print "<Data>\n";
while ($obj = mysql_fetch_object($result)) {
    print "<Game>\n";

    foreach ($obj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
            switch ($key) {
                case 'Genre':
                    echo '<Genres>';
                    $genres = explode('|', $value);
                    foreach ($genres as $genre) {
                        if (!empty($genre)) {
                            echo '<genre>' . $genre . '</genre>';
                        }
                    }
                    echo '</Genres>';
                    break;

                case 'Alternates':
                    echo '<AlternateTitles>';
                    $alternates = explode(',', $value);
                    foreach ($alternates as $alternate) {
                        if (!empty($alternate)) {
                            echo '<title>' . $alternate . '</title>';
                        }
                    }
                    echo '</AlternateTitles>';
                    break;

                case'Youtube':
                    print "<$key>http://www.youtube.com/watch?v=$value</$key>\n";
                    break;

                case 'Rating':
                    print "<Rating>" . (float) $value . "</Rating>";
                    break;

                case 'Players':
                    if ($value == 4) {
                        print "<$key>4+</$key>";
                    } else {
                        print "<$key>" . $value . "</$key>";
                    }
                    break;

                default:
                    print "<$key>$value</$key>\n";
            }
        }
    }

    ##On Other Platforms
    $similarResult = mysql_query("SELECT g.id, g.platform FROM games as g, platforms as p WHERE g.GameTitle = \"$baseObj->GameTitle\" AND g.Platform = p.id AND g.Platform != '$baseObj->PlatformId' ORDER BY p.name");
    $similarRowCount = mysql_num_rows($similarResult);

    if ($similarRowCount > 0) {
        print "<Similar><SimilarCount>" . $similarRowCount . "</SimilarCount>";
        while ($similarRow = mysql_fetch_assoc($similarResult)) {
            print "<Game>";
            print "<id>" . $similarRow['id'] . "</id>";
            print "<PlatformId>" . $similarRow['platform'] . "</PlatformId>";
            print "</Game>";
        }
        print "</Similar>";
    }

    ## Process Images
    print "<Images>\n";

    processFanart($obj->id);
    processBoxart($obj->id);
    processBanner($obj->id);
    processScreenshots($obj->id);
    processClearLOGO($obj->id);

    print "</Images>\n";

    ## End XML item
    print "</Game>\n";
}
print "</Data>\n";
?>

