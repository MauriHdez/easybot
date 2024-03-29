<?php

namespace gamboamartin\easybot\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class easy_etapa_cita extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'easy_etapa_cita';
        $columnas = array($tabla => false, "easy_cita" => $tabla, "easy_status_cita" => $tabla, "easy_cliente"=>"easy_cita",
            "easy_horario"=>"easy_cita");
        $campos_obligatorios[] = 'descripcion';

        $columnas_extra = array();
        /*$columnas_extra['easy_telegram_id_telegram_message']
            = "(SELECT * FROM 
            easy_telegram INNER JOIN 
            easy_cliente ON easy_cliente.id = easy_telegram.easy_client_id
            WHERE easy_cliente.id = easy_cita.easy_cliente_id )";*/

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array()): array|stdClass
    {
        $easy_cita = (new easy_cita($this->link))->registro(registro_id: $this->registro['easy_cita_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $easy_cita);
        }
           
        $easy_status_cita = (new easy_status_cita($this->link))->registro(registro_id: $this->registro['easy_status_cita_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $easy_status_cita);
        }
        
        if(!isset($this->registro['descripcion'])){
            $this->registro['descripcion'] = $easy_cita['easy_cliente_nombre']."-".$easy_cita['easy_cita_fecha_cita']."-".
                $easy_status_cita['easy_status_cita_descripcion'];
        }

        $this->registro = $this->campos_base(data: $this->registro, modelo: $this);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $this->registro = $this->limpia_campos_extras(registro: $this->registro, campos_limpiar: array('cat_sat_tipo_producto_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar grupo producto', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    public function get_grupo(int $cat_sat_grupo_producto_id): array|stdClass
    {
        $registro = $this->registro(registro_id: $cat_sat_grupo_producto_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener grupo producto', data: $registro);
        }

        return $registro;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false, array $keys_integra_ds = array()): array|stdClass
    {
        $registro = $this->campos_base(data: $registro, modelo: $this, id: $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $registro);
        }

        $registro = $this->limpia_campos_extras(registro: $registro, campos_limpiar: array('cat_sat_tipo_producto_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar grupo producto', data: $r_modifica_bd);
        }
        return $r_modifica_bd;
    }
}