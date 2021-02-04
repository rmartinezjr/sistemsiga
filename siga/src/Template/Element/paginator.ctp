<?php
if ($this->Paginator->counter(['format' => __('{{pages}}')])>1){
    ?>
    <ul class="paginate">
        <?php // $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('<< ' . __('Anterior')) ?>
        <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
        <?= $this->Paginator->next(__('Siguiente') . ' >>') ?>
        <?php // $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
<?php           }else{  ?>
    <ul class="paginate2">
        <?php // $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('<< ' . __('Anterior')) ?>
        <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
        <?= $this->Paginator->next(__('Siguiente') . ' >>') ?>
        <?php // $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
<?php                } ?>