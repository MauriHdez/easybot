<?php
namespace html;

use gamboamartin\easybot\models\easy_horario;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class easy_horario_html extends html_controler {

    public function select_cat_sat_grupo_producto_id(int $cols,bool $con_registros,int $id_selected, PDO $link): array|string
    {
        $modelo = new easy_horario($link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Horario',required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }
}
