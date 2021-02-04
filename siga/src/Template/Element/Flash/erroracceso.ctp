<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-danger alert-dismissable alert-acceso">
    <span class="message"><?php echo $message ?></span>
</div>
<script>
    jQuery(function(){
        setTimeout(function () {
            $(".alert-acceso").slideUp();
        }, 4000);
    });
</script>
