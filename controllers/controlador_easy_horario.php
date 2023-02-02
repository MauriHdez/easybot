<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\easybot\controllers;

use base\controller\controler;
use gamboamartin\easybot\models\easy_horario;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\easy_horario_html;
use PDO;
use stdClass;

class controlador_easy_horario extends system {
    public string $link_cat_sat_producto_alta_bd = '';

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new easy_horario(link: $link);
        $html_ = new easy_horario_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable', data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $configuraciones = $this->init_configuraciones();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones', data: $configuraciones);
            print_r($error);
            die('Error');
        }

        $init_controladores = $this->init_controladores(paths_conf: $paths_conf);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar controladores', data: $init_controladores);
            print_r($error);
            die('Error');
        }

        $init_links = $this->init_links();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar links', data: $init_links);
            print_r($error);
            die('Error');
        }
        $this->lista_get_data = true;
    }



    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'descripcion');
        $keys->selects = array();

        $init_data = array();
        $init_data['cat_sat_tipo_producto'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_division_producto'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_grupo_producto'] = "gamboamartin\\cat_sat";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    public function get_clases(bool $header, bool $ws = true): array|stdClass
    {
        $keys['cat_sat_grupo_producto'] = array('id','descripcion','codigo','codigo_bis');

        $salida = $this->get_out(header: $header,keys: $keys, ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar salida',data:  $salida,header: $header,ws: $ws);
        }

        return $salida;
    }

    public function productos(bool $header = true, bool $ws = false, array $not_actions = array()): array|string
    {
        $seccion = "cat_sat_producto";

        $data_view = new stdClass();
        $data_view->names = array('Id', 'Cod', 'Producto', 'Acciones');
        $data_view->keys_data = array($seccion . "_id", $seccion . '_codigo', $seccion . '_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\cat_sat\\models';
        $data_view->name_model_children = $seccion;

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $not_actions);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody', data: $contenido_table, header: $header, ws: $ws);
        }

        return $contenido_table;
    }

    private function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'SAT Clases';
        $this->titulo_lista = 'Registro de Clases';

        $this->path_vendor_views = 'gamboa.martin/cat_sat';
        $this->lista_get_data = true;

        return $this;
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_cat_sat_producto = new controlador_cat_sat_producto(link: $this->link,
            paths_conf: $paths_conf);

        return $this;
    }

    private function init_datatable(): stdClass
    {
        $columns["cat_sat_clase_producto_id"]["titulo"] = "Id";
        $columns["cat_sat_clase_producto_codigo"]["titulo"] = "Código";
        $columns["cat_sat_tipo_producto_descripcion"]["titulo"] = "Tipo";
        $columns["cat_sat_division_producto_descripcion"]["titulo"] = "División";
        $columns["cat_sat_grupo_producto_descripcion"]["titulo"] = "Grupo";
        $columns["cat_sat_clase_producto_descripcion"]["titulo"] = "Clase";
        $columns["cat_sat_clase_producto_n_productos"]["titulo"] = "Productos";

        $filtro = array("cat_sat_clase_producto.id", "cat_sat_clase_producto.codigo", "cat_sat_clase_producto.descripcion",
            "cat_sat_tipo_producto.descripcion", "cat_sat_division_producto.descripcion", "cat_sat_grupo_producto.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->link_cat_sat_producto_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'cat_sat_producto');
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link', data: $this->link_cat_sat_producto_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_cat_sat_producto_alta_bd;
    }

    private function init_selects(array $keys_selects, string $key, string $label, int $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array
    {
        $keys_selects = $this->init_selects(keys_selects: array(), key: "cat_sat_tipo_producto_id",
            label: "Tipo de Producto");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_division_producto_id",
            label: "División", con_registros: false);
        return $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_grupo_producto_id", label: "Grupo",
            con_registros: false);
    }

    protected function inputs_children(stdClass $registro): array|stdClass
    {
        $r_template = $this->controlador_cat_sat_producto->alta(header: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener template', data: $r_template);
        }

        $keys_selects = $this->controlador_cat_sat_producto->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $clase = (new cat_sat_clase_producto(link: $this->link))->get_clase(cat_sat_clase_producto_id: $this->registro_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener producto', data: $clase);
        }

        $keys_selects['cat_sat_tipo_producto_id']->id_selected = $clase['cat_sat_tipo_producto_id'];
        $keys_selects['cat_sat_tipo_producto_id']->filtro = array("cat_sat_tipo_producto.id" =>
            $clase['cat_sat_tipo_producto_id']);
        $keys_selects['cat_sat_tipo_producto_id']->disabled = true;

        $keys_selects['cat_sat_division_producto_id']->id_selected = $clase['cat_sat_division_producto_id'];
        $keys_selects['cat_sat_division_producto_id']->filtro = array("cat_sat_tipo_producto.id" =>
            $clase['cat_sat_tipo_producto_id']);
        $keys_selects['cat_sat_division_producto_id']->disabled = true;
        $keys_selects['cat_sat_division_producto_id']->con_registros = true;
        $keys_selects['cat_sat_division_producto_id']->extra_params_keys = array("cat_sat_division_producto_codigo");

        $keys_selects['cat_sat_grupo_producto_id']->id_selected = $clase['cat_sat_grupo_producto_id'];
        $keys_selects['cat_sat_grupo_producto_id']->filtro = array("cat_sat_division_producto.id" =>
            $clase['cat_sat_division_producto_id']);
        $keys_selects['cat_sat_grupo_producto_id']->disabled = true;
        $keys_selects['cat_sat_grupo_producto_id']->con_registros = true;
        $keys_selects['cat_sat_grupo_producto_id']->extra_params_keys = array("cat_sat_grupo_producto_codigo");

        $keys_selects['cat_sat_clase_producto_id']->id_selected = $clase['cat_sat_clase_producto_id'];
        $keys_selects['cat_sat_clase_producto_id']->filtro = array("cat_sat_grupo_producto.id" =>
            $clase['cat_sat_grupo_producto_id']);
        $keys_selects['cat_sat_clase_producto_id']->disabled = true;
        $keys_selects['cat_sat_clase_producto_id']->con_registros = true;
        $keys_selects['cat_sat_clase_producto_id']->extra_params_keys = array("cat_sat_clase_producto_codigo");

        $inputs = $this->controlador_cat_sat_producto->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }

        $this->inputs = $inputs;

        return $this->inputs;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Código');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Clase');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $clase = (new cat_sat_clase_producto(link: $this->link))->get_clase(cat_sat_clase_producto_id: $this->registro_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener clase', data: $clase);
        }

        $keys_selects['cat_sat_tipo_producto_id']->id_selected = $this->registro['cat_sat_tipo_producto_id'];
        $keys_selects['cat_sat_division_producto_id']->con_registros = true;
        $keys_selects['cat_sat_division_producto_id']->filtro = array("cat_sat_tipo_producto.id" =>
            $clase['cat_sat_tipo_producto_id']);
        $keys_selects['cat_sat_division_producto_id']->id_selected = $clase['cat_sat_division_producto_id'];
        $keys_selects['cat_sat_division_producto_id']->extra_params_keys = array('cat_sat_division_producto_codigo');
        $keys_selects['cat_sat_grupo_producto_id']->con_registros = true;
        $keys_selects['cat_sat_grupo_producto_id']->filtro = array("cat_sat_division_producto.id" =>
            $clase['cat_sat_division_producto_id']);
        $keys_selects['cat_sat_grupo_producto_id']->id_selected = $clase['cat_sat_grupo_producto_id'];
        $keys_selects['cat_sat_grupo_producto_id']->extra_params_keys = array('cat_sat_grupo_producto_codigo');


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }
}
