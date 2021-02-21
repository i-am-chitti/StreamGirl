var likeVideoBtn = 1;

function likeVideo(videoId) {
    $.post("ajax/updateVideoLikes.php", {videoId: videoId, type:likeVideoBtn }, function(data) {
        if(data !== null && data !== "") {
            console.log("something went wrong");
        }
        else {
            var nLikes = parseInt(document.getElementById("numOfLikes").innerHTML);
            if(likeVideoBtn) {
                document.getElementById("likeBtn").style.color = "#ffbcbc";
                document.getElementById("numOfLikes").innerHTML = nLikes + 1;
                likeVideoBtn = 0;
            }
            else {
                document.getElementById("likeBtn").style.color = "#fff";
                document.getElementById("numOfLikes").innerHTML = nLikes - 1;
                likeVideoBtn = 1;
            }
        }
    })
}

function goBack() {
    window.history.back();
}

function changeLikes(picId) {

    color = document.getElementById("likeBtn"+picId).style.color;

    var type=0;

    if(color === "rgb(255, 255, 255)") {
        type = 1;
    }

    $.post("ajax/updatePicLikes.php", { picId: picId, type:type }, function(data) {
        if(data !== null && data!== "") {
            alert(data);
        }
        else {
            var nLikes = parseInt(document.getElementById("noOfLikes"+picId).innerHTML);
            if(type === 1) {
                document.getElementById("likeBtn"+picId).style.color = "#ffbcbc";
                document.getElementById("noOfLikes"+picId).innerHTML = nLikes + 1;
            }
            else {
                document.getElementById("likeBtn"+picId).style.color = "#fff";
                document.getElementById("noOfLikes"+picId).innerHTML = nLikes - 1;
            }
        }
    });
}