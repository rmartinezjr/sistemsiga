<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>

<div class="alert alert-success alert-dismissable alert-inicio">
    <span class="message"><?= $message ?></span>
</div>
