<?php

require_once("../includes/config.php");

require_once("../includes/classes/SearchResultsProvider.php");

require_once("../includes/classes/Entity.php");

require_once("../includes/classes/EntityProvider.php");

require_once("../includes/classes/Category.php");

require_once("../includes/classes/CategoryProvider.php");

require_once("../includes/classes/Provider.php");

require_once("../includes/classes/Star.php");

require_once("../includes/classes/StarProvider.php");

if(isset($_POST["term"])) {

    $srp = new SearchResultsProvider($con);
    
    echo $srp->getResults($_POST["term"]);

}
else {
    echo "No videoId and username provided. ";
}

?>