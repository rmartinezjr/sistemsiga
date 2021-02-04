<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-danger alert-dismissable alert-inicio">
    <span class="message"><?php echo $message ?></span>
</div>
