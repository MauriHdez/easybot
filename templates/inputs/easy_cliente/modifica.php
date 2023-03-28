<?php /** @var gamboamartin\cat_sat\controllers\controlador_cat_sat_grupo_producto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->telefono; ?>
<?php echo $controlador->inputs->correo; ?>
<?php echo $controlador->inputs->direccion; ?>
<?php echo $controlador->inputs->adm_genero_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>
<div class="error" style="margin-bottom: 20px"></div>
<div class="col-row-12">
    <?php foreach ($controlador->buttons as $button){ ?>
        <?php echo $button; ?>
    <?php }?>
</div>

