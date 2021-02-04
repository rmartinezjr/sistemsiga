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
    $this->assign('templateName', 'error500.ctp');

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
    <?php if ($error instanceof Error) : ?>
    <strong>Error in: </strong>
    <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
<?php endif; ?>
    <?php
    echo $this->element('auto_table_warning');

    if (extension_loaded('xdebug')) :
        xdebug_print_function_stack();
    endif;

    $this->end();
    ?>
    <h2><?= __d('cake', 'An Internal Error Has Occurred') ?></h2>
    <p class="error">
        <strong><?= __d('cake', 'Error') ?>: </strong>
        <?= h($message) ?>
    </p>
<?php endif; ?>