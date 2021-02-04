<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';

if (!Configure::read('debug')):
    $this->layout = 'layouterror';
    $real_url = (isset($_SERVER['HTTPS']) ? "https:" : "http:") . '//'.$_SERVER['HTTP_HOST'].$this->request->getAttribute('base').$url;

?>
    <script>
        function getUrl(){
            return "<?=$real_url?>";
        }
    </script>
    <div class="row">
        <div class="col-xs-12">
            <img class="icono-error" src="<?= $this->Url->image('iconoerror.png')?>">
            <span class="error-code">Error <?= $code ?></span>
            <input type="hidden" id="error-code" value="<?= $code ?>">
        </div>
        <div class="col-xs-12 texto-error"></div>

    </div>
    <script type="text/javascript">
        jQuery(function(){
            var error_code = $('#error-code').val();
            var url = '<?= \Cake\Routing\Router::url(['controller' => 'cerrors', 'action' => 'getmessageserror'], true)  . '/' ?>';
            $(".texto-error").load(url, {error_code: error_code, url_error: getUrl()});
        });
    </script>
<?php
else:
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.ctp');

    $this->start('file');
    ?>
    <?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
    <?php endif; ?>
    <?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
    <?php endif; ?>
    <?= $this->element('auto_table_warning') ?>
    <?php
    if (extension_loaded('xdebug')) :
        xdebug_print_function_stack();
    endif;

    $this->end();
?>
    <h2><?= h($message) ?></h2>
    <p class="error">
        <strong><?= __d('cake', 'Error') ?>: </strong>
        <?= __d('cake', 'The requested address {0} was not found on this server.', "<strong>'{$url}'</strong>") ?>
    </p>
<?php endif; ?>