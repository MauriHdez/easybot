<?php /** @var gamboamartin\cat_sat\controllers\controlador_cat_sat_grupo_producto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->id_telegram_message; ?>
<?php echo $controlador->inputs->easy_cliente_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>
<div class="error" style="margin-bottom: 20px"></div>
<div class="col-row-12">
    <?php foreach ($controlador->buttons as $button){ ?>
        <?php echo $button; ?>
    <?php }?>
</div>

