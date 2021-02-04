<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-success" id="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    <span class="icon icon-cross-circled"></span>
    <div class="message success" onclick="this.classList.add('hidden')"><?= $message ?></div>
    <button type="button" class="close" data-dismiss="alert"></button>
</div>

