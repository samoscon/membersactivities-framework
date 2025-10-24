$(document).ready(function () {
    function changeClass() {
        let btn_class = document
            .getElementById('myListgroup').className;

        document.getElementById('myListgroup')
            .className = "list-group mt-4 visible";
    }
    
    $('.togglebtn').on("click", changeClass);
});
