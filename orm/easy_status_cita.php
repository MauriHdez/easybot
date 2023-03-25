<?php

namespace gamboamartin\easybot\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class easy_status_cita extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'easy_horario';
        $columnas = array($tabla => false);
        $campos_obligatorios[] = 'descripcion';

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array()): array|stdClass
    {
        if(!isset($this->registro['descripcion'])){
            $this->registro['descripcion'] = $this->registro['hora_inicio']."-".$this->registro['hora_fin']." ".
                $this->registro['easy_dia_semana_id'];
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