<div class="header-inicio">
    <div class="row">
        <div class="col-md-1 col-xs-2 col-lg-1">
            <img src="../img/favicon-ansp.png" class="img-logo img-responsive">
        </div>
        <div class="col-md-5 col-lg-7 col-xs-7 titulo-sistem">
            <h2>Sistema para la gestion de solicitudes</h2>
            <h4>Academia Nacional de Seguridad Publica</h4>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dateContainer = $(".hora-header-inicio");
    setInterval(function() {
        var date = new Date();
        var html = '';

        html += zeroSpan(date.getHours()) + ":";
        html += zeroSpan(date.getMinutes()) + ":";
        html += zeroSpan(date.getSeconds());

        dateContainer.html(html);
    }, 1000);

    function zeroSpan (number) {
        if (number < 10) {
            return "0" + number;
        }
        return number;
    }
</script>