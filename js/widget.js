function toggle_widget(){
    var x = document.getElementById("wespher_widget_div");
    x.classList.toggle("l_hidden");
}
function show_half(){
    var x = document.getElementById("wespher_widget_div");
    x.classList.add("showhalf");
    x.classList.remove("l_hidden");
}
function show_full(){
    var x = document.getElementById("wespher_widget_div");
    x.classList.remove("showhalf");
    x.classList.remove("l_hidden");
}
function close_frame(){
    var x = document.getElementById("wespher_widget_div");
    x.classList.add("l_hidden");
}
function show_page(url){
    location.href = url;
}