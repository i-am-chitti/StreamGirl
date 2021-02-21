<?php 

require_once("./includeClasses.php");

$metaInfo = MetaProvider::getMetaInfo($con, 15, null);  //index page
$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

?>

<div class="textboxContainer">
    <input type="text" class="searchInput" placeholder="Search for something">
</div>

<div class="results">

</div>

<script>
    $(function() {
        
        var timer;

        $(".searchInput").keyup(function() {
            clearTimeout(timer);
            
            timer = setTimeout(function() {
                var val = $(".searchInput").val();
                if(val !== "") {
                    $.post("ajax/getSearchResults.php", { term: val }, function(data) {
                        $(".results").html(data);
                    })
                }
                else {
                    $(".results").html = "";
                }
            }, 500);
        })
    })
</script>