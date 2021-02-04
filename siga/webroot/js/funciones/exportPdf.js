$(document).ready(function(){
    var specialElementHandler = {
        "#editor": function(element, renderer){
            return true;
        }
    }
    $(".btn-save").click(function(){
        var doc = new jsPDF();
        $("#left-logo").css({"width": "100px"});
        $("#right-logo").css({"width": "130px"});
        doc.fromHTML($("#target").html(), 15, 15, {
                "width": 100,
                "elementHandlers": specialElementHandler
            },
            function(bla){
                doc.save("sample-file-pdf.pdf");
            });

    });

});