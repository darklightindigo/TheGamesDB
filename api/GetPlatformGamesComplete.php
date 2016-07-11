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
include($_SERVER["DOCUMENT_ROOT"] . "/modeles/modele_platform.php");
include($_SERVER["DOCUMENT_ROOT"] . "/api/api_function.php");

//$platform = str_replace(array(' - ', '-'), '%', $platform);
$platform = $_REQUEST["platform"];

//$language		= $_REQUEST["language"];
$user = $_REQUEST["user"];
$query;

if (!platform_exist($platform, $errorMessage)) {
    print $errorMessage;
    exit;
} else {
    $query = " SELECT id FROM games WHERE platform = $platform ";
}

$result = mysql_query($query) or die('Query failed: ' . mysql_error());
print "<Data>\n";
while ($obj = mysql_fetch_object($result)) {
    print "<Game>\n";

    // Base Info
    $subquery = "SELECT games.id, games.GameTitle, games.ReleaseDate FROM games WHERE games.id={$obj->id}";
    $baseResult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $baseObj = mysql_fetch_object($baseResult);
    foreach ($baseObj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
            print "<$key>$value</$key>\n";
        }
    }

    ## End XML item
    print "</Game>\n";
}
?>
</Data>

