<?php
namespace html;

use gamboamartin\easybot\models\easy_cliente;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class easy_cliente_html extends html_controler {

    public function select_easy_cliente_id(int $cols,bool $con_registros,int $id_selected, PDO $link): array|string
    {
        $modelo = new easy_cliente($link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Cliente',required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }
}
