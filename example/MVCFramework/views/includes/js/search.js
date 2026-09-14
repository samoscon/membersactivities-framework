$(document).ready(function () {
    function displayFiles() {
        var value = $("#myInput").val().toLowerCase();
        $("#myTable tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    }

    $("#myInput").on("keyup", displayFiles);
    displayFiles();
});
