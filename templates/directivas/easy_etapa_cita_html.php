<?php
namespace html;

use gamboamartin\easybot\models\easy_etapa_cita;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class easy_etapa_cita_html extends html_controler {

    public function select_easy_etapa_cita_id(int $cols,bool $con_registros,int $id_selected, PDO $link): array|string
    {
        $modelo = new easy_etapa_cita($link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Etapa Cita',required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }
}
