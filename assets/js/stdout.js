function createImageFromHTML(){
    var div = document.querySelector('#imagecontainer');
    var selecteddate = document.querySelector("#currentdate").value;
    var canvas = document.createElement('canvas');

    div.style.display = "block";

    var scaleBy = 5;
    var w = 1000;
    var h = 1000;
    canvas.width = w * scaleBy;
    canvas.height = h * scaleBy;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    var context = canvas.getContext('2d');
    context.scale(scaleBy, scaleBy);

    html2canvas(div, {
        canvas:canvas,
        onrendered: function (canvas) {
            if(param == 'preview'){
                document.body.appendChild(canvas);
            }
            else{
                var canvasData = canvas.toDataURL("image/png");
                var xmlHttpReq = false;
                if (window.XMLHttpRequest) {
                    ajax = new XMLHttpRequest();
                }

                else if (window.ActiveXObject) {
                    ajax = new ActiveXObject("Microsoft.XMLHTTP");
                }
                ajax.open('POST', 'app/requestHandler.php?action=email&date='+selecteddate, false);
                ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                ajax.onreadystatechange = function() {
                    console.log(ajax.responseText);
                }
                ajax.send("imgData="+canvasData);
                div.style.display = "none";
            }

        }
    });
}
