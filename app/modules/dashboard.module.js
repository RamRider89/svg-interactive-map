localStorage.removeItem("latestFile");
$("body").ready(async function () {

    setLocalSideBarStyle();

    $("#imageContainer").click(function () {
        $(".menu-layers").removeClass("show");
    });
});

function setLocalSideBarStyle() {
    $("#Dashboard").addClass('active');
}
