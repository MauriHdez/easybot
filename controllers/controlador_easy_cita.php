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
use base\controller\init;
use gamboamartin\easybot\models\easy_cita;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\out_permisos;
use gamboamartin\system\system;
use gamboamartin\template\html;
use gamboamartin\validacion\validacion;
use html\easy_cita_html;
use html\easy_horario_html;
use PDO;
use stdClass;

class controlador_easy_cita extends system {

    public controlador_easy_cita $controlador_easy_cita;
    public string $link_easy_horario_alta_bd = '';

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new easy_cita(link: $link);
        $html_ = new easy_cita_html(html: $html);
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

    public function alta(bool $header, bool $ws = false): array|string
    {

        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects['codigo'] = new stdClass();
        $keys_selects['codigo']->cols = 4;

        $keys_selects['hora_inicio'] = new stdClass();
        $keys_selects['hora_inicio']->cols = 4;
        $keys_selects['hora_inicio']->place_holder = 'Hora Inicio';

        $keys_selects['hora_fin'] = new stdClass();
        $keys_selects['hora_fin']->cols = 4;
        $keys_selects['hora_fin']->place_holder = 'Hora Fin';

        $keys_selects['easy_dia_semana_id'] = new stdClass();
        $keys_selects['easy_dia_semana_id']->cols = 12;
        $keys_selects['easy_dia_semana_id']->label = 'Dia Semana';

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    private function base(): array|static
    {
        $campos_view = $this->campos_view();
        if(errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar campos_view', data: $campos_view);
        }

        $this->modelo->campos_view = $campos_view;

        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();


        return $this;
    }

    final public function init_alta(): array|stdClass|string
    {

        $r_template = parent::alta(header:false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener template',data:  $r_template);
        }

        $base = $this->base();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera base',data:  $base);
        }

        return $r_template;
    }


    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'hora_inicio', 'hora_fin');
        $keys->selects = array();

        $init_data = array();
        $init_data['easy_dia_semana'] = "gamboamartin\\easybot";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    public function campos_view_base(array $init_data, stdClass $keys): array
    {
        $selects = (new init())->select_key_input($init_data, selects: $keys->selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar select',data:  $selects);
        }
        $keys->selects = $selects;

        $campos_view = (new init())->model_init_campos_template(
            campos_view: array(),keys:  $keys, link: $this->link);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }
        return $campos_view;
    }

    private function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'Cita';
        $this->titulo_lista = 'Cita';

        $this->path_vendor_views = 'gamboa.martin/easybot';
        $this->lista_get_data = true;

        return $this;
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        //$this->controlador_easy_horario = new controlador_easy_horario(link: $this->link,paths_conf: $paths_conf);

        return $this;
    }

    private function init_datatable(): stdClass
    {
        $columns["easy_cliente_id"]["titulo"] = "Id";
        $columns["easy_cliente_codigo"]["titulo"] = "Código";
        $columns["easy_cliente_nombre"]["titulo"] = "Nombre";
        $columns["easy_cliente_ap"]["titulo"] = "Apellido Paterno";
        $columns["easy_cliente_am"]["titulo"] = "Apellido Materno";
        $columns["easy_cliente_telefono"]["titulo"] = "Telefono";
        $columns["easy_cliente_correo"]["titulo"] = "Correo";
        $columns["easy_cliente_direccion"]["titulo"] = "Direccion";
        $columns["adm_genero_descripcion"]["titulo"] = "Genero";

        $filtro = array("easy_cliente.id", "easy_cliente.codigo", "easy_cliente.nombre",
            "easy_cliente.ap", "easy_cliente.am", "easy_cliente.telefono","easy_cliente.correo");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->link_easy_horario_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'easy_cliente');
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link', data: $this->link_easy_horario_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_easy_horario_alta_bd;
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

    protected function key_select(int $cols, bool $con_registros, array $filtro,string $key, array $keys_selects,
                                  int|null $id_selected, string $label): array
    {
        $key = trim($key);
        if($key === ''){
            return $this->errores->error(mensaje: 'Error key esta vacio',data:  $key);
        }
        $valida = (new validacion())->valida_cols_css(cols: $cols);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar cols',data:  $valida);
        }

        $label = $this->label_init(key: $key,label:  $label);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar label',data:  $label);
        }

        if(!isset($keys_selects[$key])){
            $keys_selects[$key] = new stdClass();
        }

        $keys_params = array('cols','con_registros','label','id_selected','filtro');

        foreach ($keys_params as $key_val){

            /**
             * REFACTORIZAR
             */
            if($key_val === 'id_selected'){
                if(is_null($id_selected)){
                    $id_selected = -1;
                }
            }
            if(!isset($$key_val)){
                return $this->errores->error(mensaje: 'Error key val no es una variable valida',data:  $key_val);
            }

            $value = $$key_val;
            if(isset($keys_selects[$key]->$key_val)){
                $value=$keys_selects[$key]->$key_val;
            }

            $keys_selects = $this->integra_key_to_select(key: $key,key_val:  $key_val,keys_selects:  $keys_selects,
                value:  $value);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al integrar keys',data:  $keys_selects);
            }
        }

        return $keys_selects;
    }

    private function label_init(string $key, string $label): array|string
    {
        $key = trim($key);
        if($key === ''){
            return $this->errores->error(mensaje: 'Error key esta vacio',data:  $key);
        }
        $label = trim($label);
        if($label === ''){
            $label = $this->label(key: $key);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al generar label',data:  $label);
            }
        }
        return $label;
    }

    private function label(string $key): string |array
    {
        $key = trim($key);
        if($key === ''){
            return $this->errores->error(mensaje: 'Error key esta vacio',data:  $key);
        }
        $label = trim($key);
        $label = str_replace('_', ' ', $label);
        return ucwords($label);
    }

    private function integra_key_to_select(string $key, string $key_val, array $keys_selects, string|bool|array|null $value ): array
    {
        $key = trim($key);
        if($key === ''){
            return $this->errores->error(mensaje: 'Error key esta vacio',data:  $key);
        }
        $key_val = trim($key_val);
        if($key_val === ''){
            return $this->errores->error(mensaje: 'Error key_val esta vacio',data:  $key_val);
        }
        if(!isset($keys_selects[$key])){
            $keys_selects[$key] = new stdClass();
        }
        $keys_selects[$key]->$key_val = $value;
        return $keys_selects;
    }

    public function init_selects_inputs(): array
    {
         return $this->init_selects(keys_selects: array(), key: "easy_dia_semana_id",
            label: "Dia Semana");
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Código');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Clase');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    protected function init_modifica(): array|stdClass|string
    {
        if($this->registro_id<=0){
            return $this->errores->error(mensaje: 'Error registro_id debe ser mayor a 0', data: $this->registro_id);
        }

        $r_template = parent::modifica(header: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener template',data:  $r_template);
        }

        $base = $this->base();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera base',data:  $base);
        }
        return $r_template;
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

        $keys_selects['easy_dia_semana_id']->id_selected = $this->registro['easy_dia_semana_id'];


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }

    protected function base_upd(array $keys_selects, array $params, array $params_ajustados): array|stdClass
    {

        if(count($params) === 0){
            $params = (new \gamboamartin\system\_ctl_base\init())->params(controler: $this,params:  $params);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al asignar params', data: $params);
            }
        }

        if(count($params_ajustados) === 0) {
            $params_ajustados['elimina_bd']['next_seccion'] = $this->tabla;
            $params_ajustados['elimina_bd']['next_accion'] = 'lista';
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $this->buttons = array();
        $buttons = (new out_permisos())->buttons_view(controler:$this, not_actions: $this->not_actions,
            params: $params, params_ajustados: $params_ajustados);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar botones',data:  $buttons);
        }

        $data = new stdClass();
        $data->buttons = $buttons;
        $data->inputs = $inputs;
        $this->buttons = $buttons;
        return $data;
    }

}
