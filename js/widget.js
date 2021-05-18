function show_widget(){
    var x = document.getElementById("wespher_widget_frame");
    x.style.display = "block";
    x.style.height = "500px";
}
function show_full(){
    var x = document.getElementById("wespher_widget_frame");
    x.style.display = "block";
    x.style.height = "calc( 100vh - 50px )";
}
function close_frame(){
    var x = document.getElementById("wespher_widget_frame");
    x.style.display = "none";
}
function show_page(url){
    location.href = url;
}