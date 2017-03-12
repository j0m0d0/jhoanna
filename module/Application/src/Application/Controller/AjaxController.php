<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\CompensacionAsignacionEntity;
use Application\Model\EquipoFolioEntity;
use Application\Model\FolioOtEntity;
use Application\Model\ItemDetalleEquipoEntity;
use Application\Model\RequisicionEntity;
use Application\Model\ItemDetalleEstudiosEntity;
use Application\Model\ItemDetalleLabEntity;
use Application\Model\ItemDetallePersonalEntity;
use Application\Model\OrdenTrabajoEntity;
use Application\Model\GastosObraEntity;
use Application\Model\LogEntity;
use Application\Model\RegistroAsignacionEntity;
use Application\Model\PresupuestoVoboEntity;
use Application\Model\RelInsumoItemEntity;
use Application\Model\BeneficiarioExternoEntity;
use Application\Model\CuentaBancariaEntity;
use Application\Model\WizzardDataVoboEntity;
use Application\Model\WizzardEntity;
use Application\Model\WizzardDataEntity;
use Application\Model\WizzardTipoEnsayeEntity;
use Application\Model\WizzardNormasAplicablesEntity;
use Zend\Authentication\AuthenticationService;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
//Mail
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;

class AjaxController extends AbstractActionController
{
    private $auth;
    public function __construct()
    {
        //Cargamos el servicio de autenticación en el constructor
        $this->auth = new AuthenticationService();
    }

    /*public function getIdUserAuth()
    {
        $id_user = "";
        $auth    = $this->auth;
        $identi  = $auth->getStorage()->read();
        if ($identi != null) {
            foreach ($identi as $key => $value) {
                if ($key == 'id') {
                    $id_user = $value;
                }
            }
        }
        return $id_user;
    }*/

    function getIdUserAuth()
    {
        $id_user = '';
        $auth    = $this->auth;
        $identi  = $auth->getStorage()->read();
         if ($identi!=null){
           foreach ($identi as $key => $value) {
                if ($key == 'id') {
                  $id_user = $value;
              }
           }
         }
         return $id_user;
    }

    public function getcontactosapiAction()
    {
        $id_cliente        = $this->params('id');
        $contactos_cliente = $this->getClienteContactoMapper()->getAllByIdArray($id_cliente);
        header('Content-Type: application/json');
        echo json_encode($contactos_cliente);

        $this->layout('layout/blank');
    }

    public function getbenefextAction()
    {
        $beneficiario = $this->getMapper("BeneficiarioExternoMapper")->fetchAll();
        header('Content-Type: application/json');
        echo json_encode($beneficiario);
        $this->layout('layout/blank');
    }

    public function getbenefaccountAction()
    {
        $user_id = $this->params()->fromQuery("user");
        $type = $this->params()->fromQuery("type");
        $cuentas = $this->getMapper("CuentaBancariaMapper")->fetchAllByUser( $user_id , $type);
        header('Content-Type: application/json');
        echo json_encode($cuentas);
        $this->layout('layout/blank');
    }

    public function createbenfextAction()
    {
        $entity = new BeneficiarioExternoEntity();
        $entity->setNombre( $this->params()->fromPost('nombre') );
        $entity->setApaterno( $this->params()->fromPost('apaterno') );
        $entity->setAmaterno( $this->params()->fromPost('amaterno') );
        $entity->setTelefono( $this->params()->fromPost('telefono') );
        $entity->setCorreo( $this->params()->fromPost('correo') );
        $entity->setRfc( $this->params()->fromPost("rfc") );
        $entity->setPersonType( "1" );
        $entity->setRazonSocial( $this->params()->fromPost('razon_social') );
        $externo = $this->getMapper("BeneficiarioExternoMapper")->save( $entity );
        $this->Registro("Ajax","Alta de beneficiario externo",$externo);
        // PARAMETRO ENVIADOS POR POST MODAL CUENTAS BANCARIA AL DAR DE ALTA UN NUEVO PERSONAL
        $cuenta = $this->params()->fromPost("cuenta");
        $tarjeta = $this->params()->fromPost("tarjeta");
        $banco = $this->params()->fromPost("banco");
        $clabe = $this->params()->fromPost("clabe");
        $suc = $this->params()->fromPost("suc");
        if($this->params()->fromPost("is_externo") != null){
            $is_externo = $this->params()->fromPost("is_externo");    
        }else{
            $is_externo = "0";
        }
        if (count($cuenta) > 0) {
            foreach ($cuenta as $key => $value) {
            // Las cuentas se almacenaran siempre y cuando se tenga el numero de cuenta o el numero de tarjeta
                if ( $value != "" || $tarjeta[$key] != ""  ){
                    $entity_bank = new CuentaBancariaEntity();
                    $entity_bank->setTarjeta( $tarjeta[$key] );
                    $entity_bank->setClabe( $clabe[$key] );
                    $entity_bank->setSucursal( $suc[$key] );
                    $entity_bank->setCuenta( $value );
                    $entity_bank->setIsNomina( "0" );
                    $entity_bank->setIsExterno( $is_externo[$key]   );
                    $entity_bank->setBancoId( $banco[$key] );
                    $entity_bank->setUsuarioId( $externo );
                    $this->getMapper("CuentaBancariaMapper")->save( $entity_bank );
                }
            }
        }
        $this->layout('layout/blank');
    }

    public function comentvoboAction()
    {
        $id_user        = $this->getIdUserAuth();
        $id_presupuesto = $this->params('id');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $comentario = $this->params()->fromPost('comentario');
            if ($comentario != null) {
                $presupuesto_comentario = new PresupuestoVoboEntity;
                $presupuesto_comentario->setIdPresupuesto($id_presupuesto);
                $presupuesto_comentario->setComentario($comentario);
                $presupuesto_comentario->setIdUsuario($id_user);
                $this->getPresupuestoVoboMapper()->save($presupuesto_comentario);
                header('Content-Type: application/json');
                echo json_encode("true");
                $this->layout('layout/blank');
            }
        }
    }

    public function testAction()
    {
        /*$i = false;
        $id_presupuesto = $this->params("id");//$presupuesto['id_presupuesto_padre'];

        $pdata = $this->getPresupuestoMapper()->getById($id_presupuesto);

        echo "Presupuesto hijo : " . $id_presupuesto . " Padre : " . $pdata['id_presupuesto_padre'] . "<br />";

        do{

        if ( $pdata['id_presupuesto_padre'] != 0 ) {
        $pdata = $this->getPresupuestoMapper()->getById($pdata['id_presupuesto_padre']);
        echo "Presupuesto hijo : " . $pdata['id'] . " Padre : " . $pdata['id_presupuesto_padre'] . "<br />";
        }else{
        $i = true;
        $id_presupuesto = $pdata['id'];
        }
        } while ($i == false);

        echo "Presupuesto Inicial : " . $id_presupuesto;

        $entity = $this->getMapper("OrdenTrabajoMapper")->getByPresupuestoId($id_presupuesto);

        echo "<pre>";
        print_r($entity);
        echo "</pre>";*/

        /*echo "FOLIO : EPS-GL-LOF-0160.16";
        $tipo = explode("-", "EPS-GL-LOF-0160.16");

        $tipo = ( $tipo['0'] == "EPS" )? "E" : "G" ;
        echo "<pre>";
        print_r($tipo);
        echo "</pre>";*/

        $correo =  $this->getUsuarioPersonalMapper()->getByRolArray( "3" );

        /*echo "<pre>";
            print_r($correo);
        echo "</pre>";*/

        $ot       = $this->getMapper("OrdenTrabajoMapper")->fetchAllApproved("", "");
        $response = array();

        foreach ($ot as $key => $value) {
            $personal     = $this->getMapper("PersonalAsignadoMapper")->fetchAllByOt($value['id']);
            $equipo       = $this->getMapper("VehiculoAsignadoMapper")->fetchAllByOt($value['id'], 0, "");
            $requisicion  = $this->getMapper("RequisicionMapper")->getByFolio($value['folio_pre']);
            $inicios      = $this->getMapper("InicioMapper")->getByIdOt($value['id']);
            $gastos       = $this->getMapper("GastosObraMapper")->getByPresupuesto($value['presupuesto_id']);
            $req_vehiculo = $this->getMapper("ReqVehiculoMapper")->getByOtId($value['id']);
            $vehiculo     = $this->getMapper("VehiculoAsignadoMapper")->fetchAllByOt($value['id'], 1, "");

            //if (count($vehiculo) > 0) {
            // echo "<pre>";
            //                    print_r($vehiculo);
            //    echo "</pre>";
            //}

            
            
            /*print_r($accesoslog);*/

            $personal     = ($personal != "") ? count($personal) : "0";
            $equipo       = (count($equipo) > 0) ? count($equipo) : "0";
            $requisicion  = ($requisicion != "") ? count($requisicion) : "0";
            $inicios      = ($inicios != "") ? count($inicios) : "0";
            $gastos       = ($gastos != "") ? count($gastos) : "0";
            $req_vehiculo = (count($req_vehiculo) > 0) ? count($req_vehiculo) : "0";
            $vehiculo     = (count($vehiculo) > 0) ? count($vehiculo) : "0";

            $response[] = array(
                "ot_id"           => $value['id'],
                "folio_presupues" => $value['folio_pre'],
                "folio_ot"        => $value['folio'],
                "personal"        => $personal,
                "equipo"          => $equipo,
                "requisicion"     => $requisicion,
                "inicios"         => $inicios,
                "gastos"          => $gastos,
                "req_vehiculo"    => $req_vehiculo,
                "vehiculo"        => $vehiculo,
            );

        }

        return array("response" => $response);

        /*$cc = $this->getCorreosByRol( array( "5" , "9" , "10" , "18" , "19" , "20" , "21" , "22" , "23" ) );

        if ($cc != "") {
        foreach ($cc as $key => $value) {
        //$mail->addCc($value['correo'], $value['nombre']." ".$value['apaterno']);

        echo $value['correo'] . ", " .$value['nombre']." ".$value['apaterno'] . "<br />";
        }
        }

        echo "<pre>";
        print_r($cc);
        echo "</pre>";*/

        $this->layout('layout/blank');
    }

    public function accesoAction(){
        $accesoslog = $this->getMapper("LogLoginMapper")->fetchAllData();
        return array("accesoslog"=>$accesoslog);
    }

    public function logsAction(){
        $logs = $this->getMapper("LogMapper")->fetchAllData();
        return array("logs"=>$logs);
    }

    public function testmailAction()
    {
        $correo =  $this->getUsuarioPersonalMapper()->getByRolArray( "6" );
        $this->layout('layout/blank');
    }

    public function getlasfolioAction()
    {
        $id = $this->params()->fromQuery("equipoid");
        $folio     = $this->getMapper("EquipoFolioMapper")->getById($id);
        $dt_equipo = $this->getMapper("EquipoVehiculoMapper")->getById($id);

        $entity = new EquipoFolioEntity();
        if ($folio == null) {
            $entity->setIdEquipo($id);
            $entity->setFolio("1");
            //$this->getMapper("EquipoFolioMapper")->save($entity);
            $folio = $dt_equipo->getClave() . "-" . str_pad(1, 4, "0", STR_PAD_LEFT);
        } else {
            $new_folio = $folio->getFolio() + 1;
            $entity->setIdEquipo($id);
            $entity->setFolio($new_folio);
            //$this->getMapper("EquipoFolioMapper")->update($entity);
            $folio = $dt_equipo->getClave() . "-" . str_pad($new_folio, 4, "0", STR_PAD_LEFT);
        }
        header('Content-Type: application/json');
        echo json_encode($folio);
        $this->layout('layout/blank');
    }

    public function getciudadesAction()
    {
        $id   = $this->params()->fromPost("id");
        $data = $this->getMapper("MunicipiosMapper")->getCiudades($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getclienteapiAction()
    {
        $id      = $this->params('id');
        $cliente = $this->getClienteMapper()->getByIdArray($id);
        header('Content-Type: application/json');
        echo json_encode($cliente);
        $this->layout('layout/blank');
    }

    public function getcontactdataapiAction()
    {
        $id   = $this->params('id');
        $data = $this->getClienteContactoMapper()->getByIdArray($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function gettajetasasignadasAction()
    {
        $tipo       = $this->params()->fromQuery("tipo");
        $id_tarjeta = $this->params()->fromQuery("tarjeta");
        $data = $this->getMapper("VehiculoDetalleMapper")->getByTarjetasId($tipo, $id_tarjeta);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getformlaboratorioAction()
    {
        $id_presupuesto   = $this->params('id');
        $request          = $this->getRequest();
        $servs            = $this->params()->frompost("serv");
        $item_detalle_lab = new ItemDetalleLabEntity();
        if (!empty($servs)) {
            foreach ($servs as $id_serv => $cantidad) {
                foreach ($cantidad as $key => $value) {
                    if ($value != null) {
                        $item_detalle_lab->setCantidad($value);
                        $servicio  = $this->getCatServLaboratorioMapper()->getById($id_serv);
                        $costo_uni = $servicio->getPu();
                        $item_detalle_lab->setCostoUn($costo_uni);
                        $item_detalle_lab->setPresupuestoDetalleId($id_presupuesto);
                        $item_detalle_lab->setCatServLaboratorioId($id_serv);
                        $this->getItemDetalleLabMapper()->save($item_detalle_lab);
                    }
                }
            }
        }
        $this->layout('layout/blank');
    }

    public function savereqservicioAction()
    {
        if($this->params()->fromPost('id')){
             $req_service = $this->getMapper("RequisicionMapper")->getByIdEntity($this->params()->fromPost('id'));
                $req_service->setStatus($this->params()->fromPost('status'));
                $req_service->setMotivoSuspencion($this->params()->fromPost('motivo'));
            $this->getMapper("RequisicionMapper")->save($req_service);
                $data = "se suspendio la requisicion";
            $this->Registro("Requisición de Servicio","Requisición Suspendida",$this->params()->fromPost('id'));
        }
        // $folio_presupuesto        = $this->params()->fromPost('folio_presupuesto');
        // $fecha_requerido          = $this->params()->fromPost('fecha_requerido');
        // $ot_id                    = $this->params()->fromPost('ot_id');
        // $muestreo_realizado       = $this->params()->fromPost('muestreo_realizado');
        // $tipo_servicio            = $this->params()->fromPost('tipo_servicio');
        // $tipo_req                 = $this->params()->fromPost('tipo_req');
        // $recorrido                = $this->params()->fromPost('recorrido');
        // $fecha_prestacion         = $this->params()->fromPost('fecha_prestacion');
        // $fecha_limite             = $this->params()->fromPost('fecha_limite');
        // $requerimiento_servicio   = $this->params()->fromPost('requerimiento_servicio');
        // //$datauser = $this->layout()->identi;
        // $req_service = new RequisicionEntity();
        // $req_service->setFolioPresupuesto($folio_presupuesto);
        // $req_service->setFechaRequerido($fecha_requerido);
        // $req_service->setMotivo("Requisición de Servicio");        
        // $req_service->setOtId($ot_id);
        // $req_service->setOwner($this->layout()->identi->id);
        // $req_service->setAreaId($this->layout()->identi->id_area);
        // $req_service->setMuestreoRealizado($muestreo_realizado);
        // $req_service->setTipoServicio($tipo_servicio);
        // $req_service->setTipoReq($tipo_req);
        // $req_service->setRecorrido($recorrido);
        // $req_service->setFechaPrestacion($fecha_prestacion);
        // $req_service->setFechaLimite($fecha_limite);
        // $req_service->setRequerimientoServicio($requerimiento_servicio);
        // $req_service->setClasif("1");
        // $req_service->setEstado("0");
        // $data = $this->getMapper("RequisicionMapper")->save($req_service);        
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function formatosdisponiblesAction()
    {
        $tipo_muestra   = $this->params()->fromPost("tipo_muestra");
        $cat_muestra    = $this->params()->fromPost("cat_muestra");
        $formatos = $this->getMapper("WizzardMapper")->arrayAll($tipo_muestra,$cat_muestra); 
        header('Content-Type: application/json');
        echo json_encode($formatos); 
        exit(0);
        //$this->layout('layout/blank');
    }

     public function counttypemuestraAction()
    {
        $id_ot   = $this->params()->fromPost("id");
        $tipo_muestra    = $this->params()->fromPost("tipo_muestra");
        $muestras = $this->getMapper("MuestrasMapper")->countTypeMuestra($id_ot,$tipo_muestra);
        header('Content-Type: application/json');
        echo json_encode($muestras); 
        exit(0);
        //$this->layout('layout/blank');
    }

     public function nummuestraexitenteAction()
    {
        $id_ot   = $this->params()->fromPost("id");
        //$tipo_muestra    = $this->params()->fromPost("tipo_muestra");
        $muestras = $this->getMapper("MuestrasMapper")->numMuestraExitente($id_ot/*,$tipo_muestra*/);
        header('Content-Type: application/json');
        echo json_encode($muestras); 
        exit(0);
        //$this->layout('layout/blank');
    }

    public function editrequisicionAction()
    {
            $auth = $this->auth;
            $identi=$auth->getStorage()->read();
            $current_id = $this->params("id"); 
            $aprobar = $this->params()->fromPost('aprobar');
            $id_req = $this->params()->fromPost('current_id');
            //$UserId = $this->params()->fromPost('user_id');//$this->layout()->identi->id;
            $id_user        = $this->getIdUserAuth();
            $suceso = "no hay cambio de estatus";
            if($aprobar == 1){
                $requisicion = $this->getMapper("RequisicionMapper")->getByIdEntity($current_id);
                $requisicion->setConfirmUser( $id_user );
                date_default_timezone_set("America/Mexico_City");
                $requisicion->setFechaConfirm( date("Y-m-d H:i:s") );
                $requisicion->setStatus( 8 );//Confirmación de una Requisicion
                $this->getMapper("RequisicionMapper")->save($requisicion);
                $this->Registro("Requisición Consumible","Confirmada",$current_id);
                $suceso = "aprobada";
            }elseif ($aprobar == 2) {
                $motivo_cancelacion = $this->params()->fromPost('motivo_cancelacion');
                $requisicion = $this->getMapper("RequisicionMapper")->getByIdEntity($current_id);
                $requisicion->setConfirmUser( $id_user );
                date_default_timezone_set("America/Mexico_City");
                $requisicion->setFechaConfirm( date("Y-m-d H:i:s") );
                $requisicion->setStatus( 6 );//Requisicion cancelada
                if($motivo_cancelacion != null){
                    $requisicion->setMotivoCancelacion( $motivo_cancelacion );
                }
                $this->getMapper("RequisicionMapper")->save($requisicion);
                ////////////////////////////////////////////////////////////////////////
                $this->Registro("Requisición Consumible","Cancelada por Gerencia",$current_id); 
                $suceso = "cancelada";
            }

        header('Content-Type: application/json');
        echo json_encode($suceso); 
        exit(0);
    }

    public function aprobarequisicionAction()
    {
            $current_id = $this->params('id'); 
            $suceso = "no hay cambio de estatus";
            $motivo_rechazo = $this->params()->fromPost('motivo_rechazo');
            $aprobar = $this->params()->fromPost('aprobar');
            $requisicion = $this->getMapper("RequisicionMapper")->getByIdEntity($current_id);
            date_default_timezone_set("America/Mexico_City");
            
            if($aprobar != null){
                if($aprobar == 9 || $aprobar == 37){
                    $requisicion->setAdminApproved( $this->getIdUserAuth() );
                    $requisicion->setFechaApproved( date("Y-m-d H:i:s") );
                    $requisicion->setStatus( $aprobar );
                    $suceso = "aprobada";
                }
                if($aprobar == 11){
                    $requisicion->setStatus( $aprobar );
                }
            }
            if($motivo_rechazo != null){
                $requisicion->setMotivoRechazo( $motivo_rechazo );
                $suceso = "rechazado";
            }           
            $this->getMapper("RequisicionMapper")->save($requisicion);

        header('Content-Type: application/json');
        echo json_encode($suceso); 
        exit(0);

    }

    public function getdatapruAction()
    {
        $id = $this->params()->fromPost('id');
        $pruebas = $this->getMapper("PruebasMapper")->getByIdData($id);
        header('Content-Type: application/json');
        echo json_encode($pruebas);
        $this->layout('layout/blank');
    }

    public function setdatapruAction()
    {
        if($this->params()->fromPost('id')){
            $id = $this->params()->fromPost('id');
            $laboratorista_id = $this->params()->fromPost('laboratorista_id');
            $fecha_ini = $this->params()->fromPost('fecha_ini');
            $fecha_entrega = $this->params()->fromPost('fecha_entrega');
            $ubicacion = $this->params()->fromPost('ubicacion');
            $procedimiento = $this->params()->fromPost('procedimiento');
            $normas = $this->params()->fromPost('normas');
                $entity_pru = $this->getMapper("PruebasMapper")->getById($id);
                $entity_pru->setLaboratoristaId($laboratorista_id);
                $entity_pru->setFechaIni($fecha_ini);
                $entity_pru->setFechaEntrega($fecha_entrega);
                $entity_pru->setUbicacion($ubicacion);
                $entity_pru->setNormaAplica($normas);
                $entity_pru->setProcedimiento($procedimiento);
            $this->getMapper("PruebasMapper")->save($entity_pru);
            $this->Registro("Requisición de Servicio","Actualización de prueba",$id);
        }
        header('Content-Type: application/json');
        echo json_encode($id);
        $this->layout('layout/blank');
    }

    public function getformequipoAction()
    {
        $id_presupuesto      = $this->params('id');
        $request             = $this->getRequest();
        $servs               = $this->params()->frompost("serv");
        $item_detalle_equipo = new ItemDetalleEquipoEntity();
        if (!empty($servs)) {
            foreach ($servs as $id_serv => $cantidad){
                foreach ($cantidad as $key => $value){
                    if ($value != null) {
                        $item_detalle_equipo->setCantidad($value);
                        $equipo    = $this->getCatEquipoMapper()->getById($id_serv);
                        $costo_uni = $equipo->getPu();
                        $item_detalle_equipo->setCostoUn($costo_uni);
                        $item_detalle_equipo->setPresupuestoDetalleId($id_presupuesto);
                        $item_detalle_equipo->setCatEquipoId($id_serv);
                        $this->getItemDetalleEquipoMapper()->save($item_detalle_equipo);
                    }
                }
            }
        }
        $this->layout('layout/blank');
    }

    public function additemAction()
    {
        $id_presupuesto = $this->params('id');
        $id_item        = $this->params()->fromPost('item_id');
        $servicio       = $this->params()->fromPost('select_item');
        $cantidad       = $this->params()->fromPost('cantidad');
        $costo_un       = $this->params()->fromPost('costo_un');
        $utilidad       = $this->params()->fromPost('utilidad');
        $descuento      = $this->params()->fromPost('descuento');
        $indirecto      = $this->params()->fromPost('indirecto');
        $item_detalle_equipo = new ItemDetalleEquipoEntity();
        for ($i = 0; $i < count($servicio); $i++) {
            if ($cantidad[$i] > 0 || $cantidad[$i] != "") {
                $item_detalle_equipo->setCantidad($cantidad[$i]);
                $item_detalle_equipo->setCostoUn($costo_un[$i]);
                $item_detalle_equipo->setPresupuestoDetalleId($id_presupuesto);
                $item_detalle_equipo->setCatEquipoId($servicio[$i]);
                $item_detalle_equipo->setItemId($id_item);
                $item_detalle_equipo->setUtilidad($utilidad[$i]);
                $item_detalle_equipo->setDescuento($descuento[$i]);
                $item_detalle_equipo->setIndirecto($indirecto[$i]);
                $item_detalle = $this->getItemDetalleEquipoMapper()->save($item_detalle_equipo);
                $data = $this->getMapper('CostoUnitarioMapper')->getByIdCuentaAddArr($servicio[$i]);
                if ($data != "") {
                    foreach ($data as $key => $value) {
                        $rel_insumo = new RelInsumoItemEntity();
                        $rel_insumo->setPresupuestoId($id_presupuesto);
                        $rel_insumo->setCuentaId($servicio[$i]);
                        $rel_insumo->setCostoUnitarioId($value['id']);
                        $rel_insumo->setCosto($value['costo']);
                        $rel_insumo->setCantidad($value['cantidad']);
                        $rel_insumo->setUnidad($value['unidad']);
                        $rel_insumo->setItemDetalleId($item_detalle);
                        $this->getMapper("RelInsumoItemMapper")->save($rel_insumo);
                    }
                }
            }
        }
        $data = $this->getitems($id_item, $id_presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function confirmvehiculeassignedAction()
    {
        $id   = $this->params()->fromQuery("id");
        $type = $this->params()->fromQuery("type");
        $this->getMapper("VehiculoAsignadoMapper")->updateStatus($id, $type);
        header('Content-Type: application/json');
        echo json_encode("true");
        $this->layout('layout/blank');
    }

    public function gettotalAction()
    {
        $presupuesto_id = $this->params('id');
        $data           = $this->getItemDetalleEquipoMapper()->getAllByPresupuesto($presupuesto_id);
        $subtotal = 0.00;
        if ($data != "") {
            foreach ($data as $key => $val) {
                $pu    = 0.00;
                $pui   = 0;
                $puiu  = 0;
                $puiud = 0;
                $pu = $val['precio_serv'];
                if ($val['indirecto'] > 0) {
                    $pui = ($pu * $val['indirecto']) / 100;
                };
                $pu = $pu + $pui;

                if ($val['utilidad'] > 0) {
                    $puiu = ($pu * $val['utilidad']) / 100;
                };
                $pu = $pu + $puiu;
                if ($val['descuento'] > 0) {
                    $puiud = ($pu * $val['descuento']) / 100;
                };
                $pu = $pu - $puiud;
                $pu = $val['cantidad'] * $pu;
                $subtotal += $pu;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(number_format($subtotal, 2));
        $this->layout('layout/blank');
    }

    public function deleteitemAction()
    {
        $id_presupuesto = $this->params('id');
        $id_item        = $this->params()->fromPost('id_item');
        $item_src       = $this->params()->fromPost('tabla');

        $this->getItemDetalleEquipoMapper()->deleteById($id_item);
        $this->getMapper("RelInsumoItemMapper")->deleteById($id_item);
        $data = $this->getitems($item_src, $id_presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getitems($id_item, $id_presupuesto)
    {
        $data = $this->getItemDetalleEquipoMapper()->getByIdAndItem($id_presupuesto, $id_item);
        return $data;
    }

    public function updatestatusAction()
    {

        $status         = $this->params()->fromPost("status");
        $presupuesto_id = $this->params()->fromPost("presupuesto_id");
        $pdata = $this->getPresupuestoMapper()->getById($presupuesto_id);
        $to   = ""; //$this->getCorreoByServiceType( $pdata['tipo_servicio'] );
        $from = "";
        $cc   = $this->getCorreosByRol(array("14"));
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->url()->fromRoute("vobo", array("action" => "presupuestovobo", "id" => $presupuesto_id));
        $message = "Se ha creado un nuevo presupuesto que requiere de su VoBo y aprobaci&oacute;n.";
        $message .= "<br /> <a href='" . $url . "'> Ver Presupuesto # " . $presupuesto_id . "</a>";
        $subject = "Nuevo presupuesto para validar";
        $attach  = "";
            
        if ($status == 2) {
            
            $this->sendMail($to, $from, $cc, $message, $subject, $attach);
            $accion = "Solicitud de VoBo/PENDIENTE VoBo.";
            $modulo = "Presupuesto";
        } elseif ($status == 14) {

            
            $message = $this->create_update_ot( $pdata['folio'] , $presupuesto_id , $pdata);
            $this->flashMessenger()->addMessage( $message );
            $accion = "Aprobado por el cliente";
            $modulo = "Presupuesto";
            //DESCOMENTAR TODO ESTO
                /*$presupuesto = $this->getMapper("PresupuestoMapper")->getById($presupuesto_id);
                //$band = $this->cancreateot( $presupuesto_id );

                if ($presupuesto['id_presupuesto_padre'] == 0) {

                    $ot_lastFolio = $this->getLastFolioOt($presupuesto_id);

                    $ot = new OrdenTrabajoEntity;
                    $ot->setFolio($ot_lastFolio);
                    $ot->setPresupuestoId($presupuesto_id);
                    $ot->setCatStatusId(15);
                    $this->getMapper("OrdenTrabajoMapper")->save($ot);

                    // ENVIO DE CORREO A LAS AREAS RESPONSABLES

                    $to   = $this->getCorreoByServiceType($pdata['tipo_servicio']);
                    $from = "GeoTest";

                    $cc = $this->getCorreosByRol(array("5", "9", "10", "18", "19", "20", "21", "22", "23"));

                    $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->url()->fromRoute("presupuestogeneral", array("action" => "presupuestodetalle", "id" => $presupuesto_id));

                    $message = "Se ha autorizado un nuevo presupuesto. <br />";
                    $message .= "<br /> &bullet; Folio Presupuesto : <a href='" . $url . "'> Ver Presupuesto # " . $presupuesto_id . "</a>";
                    $message .= "<br /> &bullet; Folio de Obra  : <a href='#'>  " . $ot_lastFolio . "</a>";
                    $subject = "Nuevo presupuesto Autorizado";
                    $attach  = "";
                    $this->sendMail($to, $from, $cc, $message, $subject, $attach);

                } else {
                    if ($this->cancreateot($presupuesto_id)) {
                        $ot_lastFolio = $this->getLastFolioOt($presupuesto_id);
                        $ot           = new OrdenTrabajoEntity;
                        $ot->setFolio($ot_lastFolio);
                        $ot->setPresupuestoId($presupuesto_id);
                        $ot->setCatStatusId(15);
                        $this->getMapper("OrdenTrabajoMapper")->save($ot);

                        // ENVIO DE CORREO A LAS AREAS RESPONSABLES

                        $to   = $this->getCorreoByServiceType($pdata['tipo_servicio']);
                        $from = "GeoTest";

                        $cc = $this->getCorreosByRol(array("5", "9", "10", "18", "19", "20", "21", "22", "23"));

                        $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->url()->fromRoute("presupuestogeneral", array("action" => "presupuestodetalle", "id" => $presupuesto_id));

                        $message = "Se ha autorizado un nuevo presupuesto. <br />";
                        $message .= "<br /> &bullet; Folio Presupuesto : <a href='" . $url . "'> Ver Presupuesto # " . $presupuesto_id . "</a>";
                        $message .= "<br /> &bullet; Folio de Obra  : <a href='#'>  " . $ot_lastFolio . "</a>";
                        $subject = "Nuevo presupuesto Autorizado";
                        $attach  = "";
                        $this->sendMail($to, $from, $cc, $message, $subject, $attach);
                    }else{
                        $message = $this->updatePresupuestoOt( $pdata['folio'] , $presupuesto_id );
                        $this->flashMessenger()->addMessage( $message );
                    }
                }
            */
            // HASTA AQUI
        }else{
            $accion = "Cancelado";
            $modulo = "Presupuesto";
        }

        $this->getPresupuestoMapper()->updateStatus($presupuesto_id, $status);            
        $this->Registro($modulo,$accion,$presupuesto_id);
        header('Content-Type: application/json');
        echo json_encode("status actualizado");
        $this->layout('layout/blank');
    }


    public function create_update_ot( $p_folio , $new_presupuesto_id , $pdata)
    {
        $message = "";
        $_str = explode(" ", $p_folio );
        $presupuesto = $this->getMapper("PresupuestoMapper")->getLastByFolio( $_str[0] );

        if ( !empty($presupuesto) ) {
            $orden_entity = $this->getMapper("OrdenTrabajoMapper")->getByPresupuestoId( $presupuesto[0]['id'] );   
            $orden_entity->setPresupuestoId( $new_presupuesto_id );
            $this->getMapper("OrdenTrabajoMapper")->save( $orden_entity );
            $message = "&bullet; Orden de trabajo con Id : " . $orden_entity->getId() . "<br />" .
                        "&bullet; Folio : " . $orden_entity->getFolio() . "<br />" .
                        "Se han actualizados los alcances del presupuesto " . $presupuesto[0]['id'] . " al presupuesto con id " . $new_presupuesto_id; 
        }else{
            $ot_lastFolio = $this->getLastFolioOt($new_presupuesto_id);
            $ot           = new OrdenTrabajoEntity;
            $ot->setFolio($ot_lastFolio);
            $ot->setPresupuestoId($new_presupuesto_id);
            $ot->setCatStatusId(15);
            $this->getMapper("OrdenTrabajoMapper")->save($ot);
            
            // ENVIO DE CORREO A LAS AREAS RESPONSABLES
            $to   = $this->getCorreoByServiceType($pdata['tipo_servicio']);
            $from = "GeoTest";
            $cc = $this->getCorreosByRol(array("4","5", "9", "10", "18", "19", "20", "21", "22", "23","24","26"));
            $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->url()->fromRoute("presupuestogeneral", array("action" => "presupuestodetalle", "id" => $new_presupuesto_id));
            $message = "Se ha autorizado un nuevo presupuesto. <br />";
            $message .= "<br /> &bullet; Folio Presupuesto : <a href='".$url."'>Ver Presupuesto #".$new_presupuesto_id."</a>";
            $message .= "<br /> &bullet; Folio de Obra  : <a href='#'>  ".$ot_lastFolio."</a>";
            $subject = "Nuevo presupuesto Autorizado";
            $attach  = "";
            $this->sendMail($to, $from, $cc, $message, $subject, $attach);
        }
        return $message;
    }



    public function updatestatusprenominaAction()
    {
        $id = $this->params('id');
        /*$prenominaent = $this->getMapper("PrenominaMapper")->getById($id);
        $prenominas = $this->getMapper("PrenominaMapper")->PrenominasById($id);
        $status = $this->params()->fromPost('status');
        $prenomina_id = $this->params()->fromPost("prenomina_id");
        $pdata = $this->getMapper("PrenominaMapper")->PrenominasById($prenomina_id);
        print_r($pdata);

        if($status === '18'){

        $prenominaent->setEstatus( $status );
        $this->getMapper("PrenominaMapper")->savereturnid($prenominaent);

        /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        /////// /////// ENVIO DE CORREO A LOS USUARIOS CON ROL GETENTE DE AREA ////////
        /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        // OBTIENE TODOS LOS USUARIOS CON EL ROL DE GERENTE DE AREA SEA LABORATORIO O ESTUDIOS
        //tipo servicio es el que indica de que rol es el gerente si de estudios o de laboratorio

        $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->url()->fromRoute('prenomina', array('action'=>'prenominadetalles','id'=> $id));
        if($prenominas['tipo_servicio_id'] === '1' || $prenominas['tipo_servicio_id'] === '4' ){
        $cc = $this->getMapper("UsuarioPersonalMapper")->getByRolInArray( array("6") );
        }
        if($prenominas['tipo_servicio_id'] === '2' || $prenominas['tipo_servicio_id'] === '3'){
        $cc = $this->getMapper("UsuarioPersonalMapper")->getByRolInArray( array("7") );
        }

        $msg_mail = "Se ha verificado una nueva solicitud de Prenomina <br />
        &bullet; Id Solicitud : <a href='".$url."'>" . $id . "</a> <br />
        &bullet; Titulo : " . utf8_encode($prenominas['titulo']) . " <br />
        &bullet; Folio : " . utf8_encode($prenominas['folio']) . " <br />
        &bullet; Con Fecha de : " . $prenominas['fecha_ini'] ." Al : " . $prenominas['fecha_fin'] . " <br />";
        $subject = "Nueva Prenomina Verificada";
        $this->getHelper( "GlobalsFunctionHelper" )->sendMail($to = "", "",$cc,$msg_mail,$subject,$attach="");

        //  $message = array("msg" => "La Prenomina ha sido verificada!.", "type" => "success");
        // return $this->redirect()->toRoute('prenomina', array("action" => "prenominadetalles", "id" => $id));
        }
        if($status === '5'){
        $AutorizacionID = $this->layout()->identi->id;
        $prenominaent->setEstatus( $status );
        $prenominaent->setAutorizacionId( $AutorizacionID );
        $this->getMapper("PrenominaMapper")->savereturnid($prenominaent);

        $user = $this->getMapper("UsuarioPersonalMapper")->getById($AutorizacionID);

        /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        /////// /////// ENVIO DE CORREO A LOS USUARIOS CON ROL GETENTE DE AREA ////////
        /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        // OBTIENE TODOS LOS USUARIOS CON EL ROL DE GERENTE DE AREA SEA LABORATORIO O ESTUDIOS
        //tipo servicio es el que indica de que rol es el gerente si de estudios o de laboratorio

        $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->url()->fromRoute('prenomina', array('action'=>'prenominadetalles','id'=> $id));
        $cc = $this->getMapper("UsuarioPersonalMapper")->getByRolInArray( array("15") );

        $msg_mail = "Se aprobado una nueva de Prenomina <br />
        &bullet; Id Solicitud : <a href='".$url."'>" . $id . "</a> <br />
        &bullet; Titulo : " . utf8_encode($prenominas['titulo']) . " <br />
        &bullet; Folio : " . utf8_encode($prenominas['folio']) . " <br />
        &bullet; Aprobado Por : " . utf8_encode($user->getNombre()." ".$user->getApaterno()." ".$user->getAmaterno()) . " <br />
        &bullet; Con Fecha de : " . $prenominas['fecha_ini'] ." Al : " . $prenominas['fecha_fin'] . " <br />";
        $subject = "Nueva Prenomina Aprobada";
        $this->getHelper( "GlobalsFunctionHelper" )->sendMail($to = "", "",$cc,$msg_mail,$subject,$attach="");

        //return $this->redirect()->toRoute('prenomina', array("action" => "prenominadetalles", "id" => $id));
        // $message = array("msg" => "La Prenomina ha sido Aprobada!.", "type" => "success");
        }
        if($status === '6'){
        $AutorizacionID = $this->layout()->identi->id;
        $prenominaent->setEstatus( $status );
        $prenominaent->setAutorizacionId( $AutorizacionID );
        $this->getMapper("PrenominaMapper")->savereturnid($prenominaent);

        $user = $this->getMapper("UsuarioPersonalMapper")->getById($AutorizacionID);

        // /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        // /////// /////// ENVIO DE CORREO A LOS USUARIOS CON ROL GETENTE DE AREA ////////
        // /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        // // OBTIENE TODOS LOS USUARIOS CON EL ROL DE GERENTE DE AREA SEA LABORATORIO O ESTUDIOS
        // //tipo servicio es el que indica de que rol es el gerente si de estudios o de laboratorio

        // $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->url()->fromRoute('prenomina', array('action'=>'prenominadetalles','id'=> $id));
        // $cc = $this->getMapper("UsuarioPersonalMapper")->getByRolInArray( array("15") );

        // $msg_mail = "Se aprobado una nueva de Prenomina <br />
        //             &bullet; Id Solicitud : <a href='".$url."'>" . $id . "</a> <br />
        //             &bullet; Titulo : " . utf8_encode($prenominas['titulo']) . " <br />
        //             &bullet; Folio : " . utf8_encode($prenominas['folio']) . " <br />
        //             &bullet; Aprobado Por : " . utf8_encode($user->getNombre()." ".$user->getApaterno()." ".$user->getAmaterno()) . " <br />
        //             &bullet; Con Fecha de : " . $prenominas['fecha_ini'] ." Al : " . $prenominas['fecha_fin'] . " <br />";
        // $subject = "Nueva Prenomina Aprobada";
        // $this->getHelper( "GlobalsFunctionHelper" )->sendMail($to = "", "",$cc,$msg_mail,$subject,$attach="");

        //return $this->redirect()->toRoute('prenomina', array("action" => "prenominadetalles", "id" => $id));
        // $message = array("msg" => "La Prenomina ha sido Cancelada!.", "type" => "success");

        }*/

        // $message = array("msg" => "La Prenomina ha sido Cancelada!.", "type" => "danger");
        return $this->redirect()->toRoute('prenomina', array("action" => "prenominadetalles", "id" => $id));
        // return array("message"=>$message);

        header('Content-Type: application/json');
        echo json_encode("estatus actualizado");
        $this->layout('layout/blank');
    }

    public function updatestatushorasextrasAction()
    {
        $id = $this->params('id');
        return $this->redirect()->toRoute('horasextras', array("action" => "horasextrasdetalles", "id" => $id));

        header('Content-Type: application/json');
        echo json_encode("estatus actualizado");
        $this->layout('layout/blank');
    }

    public function cancreateot($id_presupuesto)
    {
        //$id_presupuesto = $this->params("id");
        $result = true;
        $i      = false;

        //echo "Por Aprobar ( ". $id_presupuesto. " )<br />";
        do {
            $presupuesto = $this->getPresupuestoMapper()->getById($id_presupuesto);

            if ($presupuesto['id_presupuesto_padre'] != 0) {

                $presupuesto = $this->getPresupuestoMapper()->getById($presupuesto['id_presupuesto_padre']);
                if ($presupuesto['cat_status_id'] == 14) {
                    $result = false;
                    //echo "Presupuesto ". $presupuesto['id'] . " Aprobado <br />";
                } else {
                    //echo "Presupuesto ". $presupuesto['id'] . " No Aprobado <br />";
                }
                $id_presupuesto = $presupuesto['id_presupuesto_padre'];

            } else {
                if ($presupuesto['cat_status_id'] == 14) {
                    $result = false;
                    //echo "Presupuesto ". $presupuesto['id'] . " Aprobado <br />";
                } else {
                    //echo "Presupuesto ". $presupuesto['id'] . " No Aprobado <br />";
                }
                $i = true;
            }
            /*elseif( $presupuesto['id_presupuesto_padre'] == 0  ){
        $false = true;
        }else{
        if ($presupuesto['cat_status_id'] != 14) {
        $i = true;
        $id_presupuesto = $presupuesto['id'];
        }
        }*/
        } while ($i == false);

        if (!$result) {
            //echo "NO PUEDO CREAR OT";
            return false;
        } else {
            //echo "PRUEDO CREAR UNA OT";
            return true;
        }
    }

    public function sendMail($to, $from, $cc = "", $message, $subject, $attach)
    {

        $html_code = "<div>
                        <div style='padding-bottom: 25px;'>
                            <a href='http://geotest.com.mx/' target='blank'><img src='http://geotest.com.mx/wp-content/uploads/2015/09/geomarca-header1.png' style='width:300px;'></a>
                        </div>
                        <fieldset style='border-radius: 20px; width:auto; padding:30px;'>
                            <div>
                                <strong>Estimado(a):</strong>
                                <p style='width:auto;'>
                                    " . utf8_decode($message) . "
                                </p>
                                <p>Por su atenci&oacute;n gracias!.</p>
                            </div>
                        </fieldset>
                    </div>";

        $html       = new MimePart($html_code);
        $html->type = "text/html";
        $body       = new MimeMessage();
        $body->setParts(array($html));

        $mail = new Message();
        $mail->setEncoding("UTF-8");
        $mail->setBody($body);
        $mail->setFrom('erp@geotest.com.mx', 'Geotest MailSender');
        if ($to != "") {
            foreach ($to as $key => $value) {
                if ( $value['correo'] != "" ) {
                    $mail->addTo($value['correo'], $value['nombre']." ".$value['apaterno']);
                }
            }

        }

        if ($cc != "") {
            foreach ($cc as $key => $value) {
                if ($value['correo'] != "") {
                    $mail->addCc($value['correo'], $value['nombre'] . " " . $value['apaterno']);
                }
            }
        }

        //$mail->addCc("eatzin@geotest.com.mx","");
        //$mail->addBcc("hmolina@geotest.com.mx","");
        //$mail->addBcc("dperez@geotest.com.mx", "Daniel Perez Bello");
        $mail->addBcc("erp@geotest.com.mx","Copia de Seguridad ERP");

        /*
        $mail->addBcc("vehiculos@geotest.com.mx","");
        $mail->addBcc("capacitacion@geotest.com.mx","");
        $mail->addBcc("auxiliarrh@geotest.com.mx","");
        $mail->addBcc("facturacion@geotest.com.mx","");
        $mail->addBcc("remesas@geotest.com.mx","");
        $mail->addBcc("compras@geotest.com.mx","");
        $mail->addBcc("sgarcia@geotest.com.mx","");
        $mail->addBcc("eguevara@geotest.com.mx","");
        $mail->addBcc("jgarcia@geotest.com.mx","");
        $mail->addBcc("jchacon@geotest.com.mx","");
        $mail->addBcc("fportilla@geotest.com.mx","");
        $mail->addBcc("citurbide@geotest.com.mx","");
         */

        //$mail->addBcc("eliseo@ehecatl.com.mx","");

        $mail->setSubject($subject);

        $config = array('throwRcptExceptions' => false);
        //$transport = new Zend_Mail_Transport_Smtp('smtphost', $config));

        $transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'name'              => 'mail.geotest.com.mx',
            'host'              => 'mail.geotest.com.mx',
            'port'              => 465,
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'erp@geotest.com.mx',
                'password' => 'ERP-gg&si@16%',
            ),
            'port'              => 25, // Notice port change for TLS is 587
        ));

        /*
        $ical = <<<ICALENDAR_DATA
        BEGIN:VCALENDAR
        PRODID:-//Seu sistema//Sua organizacao//EN
        VERSION:2.0
        CALSCALE:GREGORIAN
        METHOD:REQUEST
        BEGIN:VEVENT
        DTSTART:{$dtStart}
        DTEND:{$dtEnd}
        DTSTAMP:{$timestamp}
        UID:{$uid}
        SUMMARY:Sucesso Total
        DESCRIPTION:Forrózão hoje. Vamos ralá nossos bucho!
        CREATED:{$dtCreated}
        LAST-MODIFIED:{$dtCreated}
        LOCATION:Forró pé sujo
        SEQUENCE:0
        STATUS:CONFIRMED
        TRANSP:OPAQUE
        ORGANIZER:MAILTO:adlermedrado@gmail.com
        BEGIN:VALARM
        ACTION:DISPLAY
        DESCRIPTION:Lembrete do evento
        TRIGGER:-P0DT0H10M0S
        END:VALARM
        END:VEVENT
        END:VCALENDAR
        ICALENDAR_DATA;
         */

        $transport->setOptions($options);
        $sent = true;
        try {
            $transport->send($mail);
        } catch (\Zend\Mail\Transport\Exception\ExceptionInterface $e) {
            $sent = false;
        }
        return $sent;
    }

    public function getCorreoByServiceType($servicio)
    {
        $rol_id = "";
        switch ($servicio) {
            case '3':
                $rol_id = "7";
                break;

            case '1':
                $rol_id = "6";
            break;
            case '2':
                $rol_id = "6";
            break;

        }
        return $this->getUsuarioPersonalMapper()->getByRolArray($rol_id);
    }

    public function getCorreosByRol($rol)
    {
        return $this->getUsuarioPersonalMapper()->getByRolInArray($rol);
    }

    public function getformpersonalAction()
    {
        $id_presupuesto        = $this->params('id');
        $request               = $this->getRequest();
        $servs                 = $this->params()->frompost("serv");
        $item_detalle_personal = new ItemDetallePersonalEntity();
        if (!empty($servs)) {
            foreach ($servs as $id_serv => $cantidad) {
                foreach ($cantidad as $key => $value) {
                    if ($value != null) {
                        $item_detalle_personal->setCantidad($value);
                        $personal  = $this->getCatPersonalMapper()->getById($id_serv);
                        $costo_uni = $personal->getPu();
                        $item_detalle_personal->setCostoUn($costo_uni);
                        $item_detalle_personal->setPresupuestoDetalleId($id_presupuesto);
                        $item_detalle_personal->setCatPersonalId($id_serv);
                        $this->getItemDetallePersonalMapper()->save($item_detalle_personal);
                    }
                }
            }
        }

        $this->layout('layout/blank');
    }

    public function getformestudioAction()
    {
        $id_presupuesto        = $this->params('id');
        $request               = $this->getRequest();
        $servs                 = $this->params()->frompost("serv");
        $item_detalle_estudios = new ItemDetalleEstudiosEntity();
        if (!empty($servs)) {
            foreach ($servs as $id_serv => $cantidad) {
                foreach ($cantidad as $key => $value) {
                    if ($value != null) {
                        $item_detalle_estudios->setCantidad($value);
                        $servicio  = $this->getCatServEstudiosMapper()->getById($id_serv);
                        $costo_uni = $servicio->getPu();
                        $item_detalle_estudios->setCostoUn($costo_uni);
                        $item_detalle_estudios->setPresupuestoDetalleId($id_presupuesto);
                        $item_detalle_estudios->setCatServEstudiosId($id_serv);
                        $this->getItemDetalleEstudiosMapper()->save($item_detalle_estudios);
                    }
                }
            }
        }

        $this->layout('layout/blank');
    }


    public function scriptAction()
    {
        echo "Script Folios no actualizados OT vs Presupuesto <br /><br />";

        /*$ordenes = $this->getMapper("OrdenTrabajoMapper")->fetchAll();
        $cadena = "";
        foreach ($ordenes as $key => $value) {

            $presupuesto = $this->getMapper("PresupuestoMapper")->getUltimoHijoAprobado( $value->getPresupuestoId() );
            $cadena =   " * Orden ID => " . $value->getId() . 
                        " * Orden Folio => " .$value->getFolio() . 
                        " * Presupuesto ID => " .$value->getPresupuestoId() . 
                        " * Ultimo Actualizado ID => " . $presupuesto[0]['id'] . 
                        " * fecha => " . $presupuesto[0]['fecha_aprobado'] . 
                        "<br />";

            if ( $presupuesto[0]['id'] != "" ) {
                if ($value->getPresupuestoId() != $presupuesto[0]['id']) {
                    echo $cadena;
                }
            }
        }*/


        $gastos = $this->getMapper("GastosObraMapper")->fetchAllArrayApproved($filters = "");

        /*echo "<pre>";
            print_r($gastos);
        echo "</pre>";*/

        foreach ($gastos as $key => $value) {
            if ( $value['ot_i_d'] == "" ) {
                echo "*G.id : " . $value['id'] . " *G.pre : " . $value['presupuesto'] . " *Ot.Folio : ". $value['ot_folio'] . " *Ot.id : " . $value['ot_i_d'] . " *( G.OT ) : ". $value['ot_id'] . "<br /><br />";
                $entity = $this->getMapper("GastosObraMapper")->getEntityById( $value['id'] );
                $entity->setOtId( $value['ot_i_d']  );
                //$this->getMapper("GastosObraMapper")->updateSaveEntity( $entity );
            }
            
        }



        $this->layout('layout/blank');
    }


 

    public function getVerificacionesAction()
    {
        $out         = "";
        $equipo_arr  = $this->array_ready($this->getMapper("VerificacionMapper")->fetchAllEquipo(0));
        $vehicle_arr = $this->array_ready($this->getMapper("VerificacionMapper")->fetchAllEquipo(1));

        $out = array_merge($equipo_arr, $vehicle_arr);

        /*echo "<pre>";
        print_r($out);
        echo "</pre>";*/

        header('Content-Type: application/json');
        echo json_encode($out);
        $this->layout('layout/blank');
    }

    public function array_ready($data)
    {
        $out = array();
        if ($data != "") {
            foreach ($data as $key => $value) {
                $titulo = "";
                if ($value['is_vehicle'] != 1) {
                    $titulo = $value['tipo'] . " - Equipo : " . $value['nombre'] . ", No. serie: " . $value['serie_equipo'];
                } else {
                    $titulo = $value['tipo'] . " - Vehículo : " . $value['nombre'] . ", No. Placas: " . $value['placas'] . ", No. Serie: " . $value['num_serie'] . ", Motivo: " . $value['motivo'];
                }

                $out[] = array(
                    'id'    => $value['id'],
                    'title' => $titulo,
                    'url'   => "#",
                    "class" => $value['class'],
                    'start' => strtotime($value['start']) . '000',
                    'end'   => strtotime($value['end']) . '000',
                );
            }
        }
        return $out;
    }

    public function updateinsumoAction()
    {
        $id        = $this->params()->fromPost('id');
        $concepto  = $this->params()->fromPost('concepto');
        $unidad    = $this->params()->fromPost('unidad');
        $costo     = $this->params()->fromPost('costo');
        $categoria = $this->params()->fromPost('categoria');

        if (!empty($concepto) && !empty($unidad) && !empty($costo) && !empty($categoria)) {
            $costo_unitario = $this->getMapper("CostoUnitarioMapper")->getById($id);
            $costo_unitario->setConcepto($this->mayus($concepto));
            $costo_unitario->setUnidad($this->mayus($unidad));
            $costo_unitario->setCosto(number_format((float) $costo, 2, '.', ''));
            $costo_unitario->setIdCategoria($categoria);
            $this->getMapper('CostoUnitarioMapper')->save($costo_unitario);

            $data = $this->getMapper("CostoUnitarioCuentaMapper")->getAllArrById($id);
            if ($data != null) {
                foreach ($data as $key => $value) {
                    $pu_update = $this->getMapper('CostoUnitarioMapper')->getSumaByCuenta($value['id_cuenta']);
                    $serv      = $this->getCatServEstudiosMapper()->getById($value['id_cuenta']);
                    $serv->setPu($pu_update['resultado']);
                    $this->getCatServEstudiosMapper()->save($serv);
                }
            }

            //$resp = $pu_update;
            $resp = "ok";
        } else {
            $resp = "falta";
        }

        header('Content-Type: application/json');
        echo json_encode($resp);
        $this->layout('layout/blank');
    }

    public function mayus($variable)
    {
        $variable = strtr(strtoupper(trim($variable)), "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
        return $variable;
    }

    public function getempleadoAction()
    {
        $id   = $this->params()->fromPost("id");
        $data = $this->getMapper("UsuarioPersonalMapper")->getByIdArr($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getOrdenesTrabajoAction()
    {
        $id   = $this->params()->fromPost("id");
        $data = $this->getMapper("OrdenTrabajoMapper")->getByIdDataOT($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getasistenciasportrabajadorAction()
    {
        $id   = $this->params()->fromPost("id");
        $data = $this->getmapper("AsistenciaPersonalAsignadoMapper")->getByPrenominaId($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getEncargadosdeOtAction()
    {
        $id   = $this->params()->fromPost("id");
        $data = $this->getMapper("OrdenTrabajoMapper")->getByIdDataOT($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    /* public function getencargadosdeotAction(){
    $id = $this->params()->fromPost("id");
    //$data = $this->getMapper("OrdenTrabajoMapper")->getByIdDataOTCompensacion($id);
    $data = $this->getMapper("OrdenTrabajoMapper")->getByIdDataOTCompensacion($id);
    header('Content-Type: application/json');
    echo json_encode($data1);
    $this->layout('layout/blank');
    }
     */
    public function getempresadataAction()
    {
        $id   = $this->params()->fromQuery("id");
        $data = $this->getMapper("EmpresaMapper")->getDataArrayById($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getpartidasAction()
    {
        $partidas = $this->getPresupuestoDetalleMapper()->getForPatidas();
        header('Content-Type: application/json');
        echo json_encode($partidas);
        $this->layout('layout/blank');

    }

    public function getproveedoresAction()
    {
        $partidas = $this->getMapper("CatProveedorMapper")->fetchAll();
        header('Content-Type: application/json');
        echo json_encode($partidas);
        $this->layout('layout/blank');

    }

    public function getservicioslaboratorioAction()
    {
        $id_presupuesto = $this->params('id');
        $data           = $this->getItemDetalleLabMapper()->fetchAllArray($id_presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getserviciosequipoAction()
    {
        $id_presupuesto = $this->params('id');
        $data           = $this->getItemDetalleEquipoMapper()->fetchAllArray($id_presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getserviciospersonalAction()
    {
        $id_presupuesto = $this->params('id');
        $data           = $this->getItemDetallePersonalMapper()->fetchAllArray($id_presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getserviciosestudioAction()
    {
        $id_presupuesto = $this->params('id');
        $data           = $this->getItemDetalleEstudiosMapper()->fetchAllArray($id_presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getpresupuestovobosAction()
    {
        $id_presupuesto = $this->params('id');
        $data           = $this->getPresupuestoVoboMapper()->getAllByIdPresupuesto($id_presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function presupuestovoboAction()
    {
        $id_user        = $this->getIdUserAuth();
        $presupuesto_id = $this->params()->fromPost("presupuesto_id");
        $comentario     = $this->params()->fromPost("comentario");
        $vobo           = $this->params()->fromPost("vobo");

        $presupuesto_vobo = new PresupuestoVoboEntity;
        $presupuesto_vobo->setIdPresupuesto($presupuesto_id);
        $presupuesto_vobo->setComentario($comentario);
        $presupuesto_vobo->setIdUsuario($id_user);

        $presupuesto_vobo->setVobo($vobo);

        $status = ($vobo == 1) ? 3 : 4;

        $this->getPresupuestoMapper()->updateStatus($presupuesto_id, $status);

        //$to = $this->getUsuarioPersonalMapper()->getByRolArray("4");
        $from    = "";
        $cc      = "";
        $subject = "";

        if ($vobo == 1) {
            $message = "El presupuesto # " . $presupuesto_id . " fue aprobado por la Gerencia.";
            $message .= "<br /> <span>Comentario: </span>";
            $message .= "<br /> " . $comentario;
            $subject = "Presupuesto aprobado por la Gerencia";
            $attach  = "";
        } else {
            $message = "El presupuesto # " . $presupuesto_id . " tiene un nuevo comentario.";
            $message .= "<br /><br /><strong> <span>Comentario: </span></strong>";
            $message .= "<br /> " . $comentario;
            $subject = "Nuevo comentario al presupuesto # " . $presupuesto_id;
            $attach  = "";
        }

        /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        /////// /////// ENVIO DE CORREO A LOS USUARIOS CON ROL CTRL VEHICULAR ////////
        /////// /////// /////// /////// /////// /////// /////// /////// /////// ///////
        // OBTIENE TODOS LOS USUARIOS CON EL ROL DE INFRAESTRUCTURA
        $cc       = $this->getUsuarioPersonalMapper()->getByRolInArray(array("4","24"));
        $msg_mail = utf8_encode($message);
        $this->getHelper("GlobalsFunctionHelper")->sendMail($to = "", "", $cc, $msg_mail, $subject, $attach = "");

        // ENVIO DE CORREO CON FUNCION ANTERIOR
        //$this->sendMail($to,$from,$cc,$message,$subject,$attach);

        $this->getPresupuestoVoboMapper()->save($presupuesto_vobo);

        if ($status == 3) {
            $accion = "Vobo Aprobado";
            $modulo = "VoBo";
        }elseif ($status == 4) {
            $accion = "Vobo Pendiente de Correción";
            $modulo = "VoBo";
        }

        $this->Registro($modulo,$accion,$presupuesto_id);

        header('Content-Type: application/json');
        echo json_encode("comentario anexado");
        $this->layout('layout/blank');
    }

    public function anexarcomentariowizzarddataAction()
    {   
        $id_user        = $this->getIdUserAuth();
        $id_wizzard_data = $this->params()->fromPost("id_wizzard_data");
        $comentario     = $this->params()->fromPost("comentario");
        
        $WizzardDataVoboEntity = new WizzardDataVoboEntity();
        $WizzardDataVoboEntity->setIdWizzardData($id_wizzard_data);
        $WizzardDataVoboEntity->setComentario($comentario);
        $WizzardDataVoboEntity->setIdUsuario($id_user);

        $this->getMapper("WizzardDataVoboMapper")->save($WizzardDataVoboEntity);

        header('Content-Type: application/json');
        echo json_encode("comentario anexado");
        $this->layout('layout/blank');

    }

    public function getLastFolioOt($presupuesto_id)
    {
        $presupuesto = $this->getMapper("PresupuestoMapper")->getById($presupuesto_id);
        $tipo        = explode("-", $presupuesto['folio']);
        $str_tipo    = "";

        /*if ($tipo['2'] == "LOF") {
        $str_tipo = "LF";
        }elseif ($tipo['2'] == "LOV") {
        $str_tipo = "LV";
        }elseif ($tipo['2'] == "LC") {
        $str_tipo = "LC";
        }elseif ($tipo['2'] == "LCV") {
        $str_tipo = "LCV";
        }elseif ($tipo['2'] == "LO") {
        $str_tipo = "LO";
        }*/

        //$modulo = ( $tipo['0'] == "EPS" )? "FolioOtEpsMapper" : "FolioOtMapper";
        //$modulo_entity = ( $tipo['0'] == "EPS" )? new FolioOtEpsEntity() : new FolioOtEntity();
        //$tipo = ( $tipo['0'] == "EPS" )? "E".$str_tipo : $str_tipo ;

        $str_tipo = $tipo[2];
        $modulo   = "FolioOtMapper";
        //$modulo_entity = new FolioOtEntity();

        $siguiente = $this->getMapper("FolioOtMapper")->getByTipo($str_tipo);
        $cantidad  = 0;

        if ($siguiente == null) {
            $folio = new FolioOtEntity();
            $folio->setTipo($str_tipo);
            $folio->setSiguiente("1");
            $this->getMapper($modulo)->save($folio);
            $cantidad = 1;
        } else {
            $cantidad = $siguiente->getSiguiente() + 1;
            $siguiente->setSiguiente($siguiente->getSiguiente() + 1);
            $this->getMapper($modulo)->save($siguiente);
        }

        return $str_tipo . "-" . str_pad($cantidad, 4, "0", STR_PAD_LEFT);
    }

    public function getcategoriaAction()
    {
        $tipo_id     = $this->params()->fromPost("tipo");
        $catregorias = $this->getCategoriaEquipoMapper()->fetchAllByType($tipo_id);
        $data        = "";
        foreach ($catregorias as $key => $value) {
            $data[] = array("id" => $value->getId(), "concepto" => $value->getConcepto());
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getcategorybytypereqAction()
    {
        $req_type    = $this->params("id");
        $catregorias = $this->getMapper("RelCatalogoReqMapper")->getByTypeReq($req_type);

        $data = "";
        foreach ($catregorias as $key => $value) {
            $data[] = array("id" => $value['id_catalogo'], "concepto" => $value['concepto']);
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getequipoAction()
    {
        $tipo_id = $this->params()->fromPost("tipo");
        if ($tipo_id == 1) {
            $tipo_id = 0;
        }
        $categoria_id = $this->params()->fromPost("categoria");
        $data         = $this->getEquipoVehiculoMapper()->fetchAllByTypeCategoria($tipo_id, $categoria_id);

        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getequipobycatAction()
    {
        $tipo_id = $this->params()->fromPost("tipo");
        if ($tipo_id == 1) {
            $tipo_id = 0;
        }
        $categoria_id = $this->params()->fromPost("categoria");
        $data         = $this->getEquipoVehiculoMapper()->fetchAllByOnlyTypeCategoria($categoria_id);

        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function setcompensacionAsignacionAction()
    {
        $compensacion_asigancion = new CompensacionAsignacionEntity();
        $compensacion_asigancion->setIdCompensacion($this->params()->fromPost("compensacion"));
        $compensacion_asigancion->setIdAsignacion($this->params()->fromPost("asignacion"));
        $rs           = $this->getMapper('CompensacionAsignacionMapper')->save($compensacion_asigancion);
        $compensacion = $this->getMapper('CompensacionMapper')->getById($this->params()->fromPost("compensacion"));
        if ($rs != null) {
            $resp = $compensacion->getConcepto();
        } else {
            $resp = "error";
        }
        header('Content-Type: application/json');
        echo json_encode($resp);
        $this->layout('layout/blank');
    }

    public function deleteCompensacionAsignacionAction()
    {
        $id_compensacion = $this->params()->fromPost("compensacion");
        $id_asignacion   = $this->params()->fromPost("asignacion");
        $compensacion    = $this->getMapper('CompensacionMapper')->getById($this->params()->fromPost("compensacion"));
        $rs              = $this->getMapper('CompensacionAsignacionMapper')->deleteByIdCompensacion($id_compensacion, $id_asignacion);
        if ($rs != null) {
            $resp = $compensacion->getConcepto();
        } else {
            $resp = "error";
        }
        header('Content-Type: application/json');
        echo json_encode($resp);
        $this->layout('layout/blank');
    }

    public function showCotizacionByItemAction()
    {
        $item_id = $this->params()->fromPost("item_id");
        $data    = $this->getMapper('CotizacionMaterialMapper')->getCotizacionByItem($item_id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function filterequipoAction()
    {

        $clave     = ($this->params()->fromPost("clave")) ? $this->params()->fromPost("clave") : "";
        $categoria = ($this->params()->fromPost("categoria")) ? $this->params()->fromPost("categoria") : "";
        $nombre    = ($this->params()->fromPost("nombre")) ? $this->params()->fromPost("nombre") : "";

        $filters = array(
            "clave"       => $clave,
            "categoria"   => $categoria,
            "nombre"      => $nombre,
            "descripcion" => "",
        );

        //$data = $this->getEquipoVehiculoMapper()->fetchAllResource( 0 , $filters );

        $data = $this->getMapper('AlmacenMapper')->getEquiposAgrupadosFilter(0, $filters);

        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getcostosunitariosAction()
    {
        $data = $this->getMapper("CostoUnitarioMapper")->fetchAllConceptos();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function deletecostounicuentaAction()
    {
        $msj       = "";
        $id        = $this->params()->fromPost("id");
        $id_cuenta = $this->params()->fromPost("id_cuenta");
        $pu        = $this->params()->fromPost("pu");
        $servs     = $this->getCatServEstudiosMapper()->getById($id_cuenta);
        if ($servs != null) {
            $servs->setPu($pu);
            $this->getCatServEstudiosMapper()->save($servs);
            $data = $this->getMapper("CostoUnitarioCuentaMapper")->deleteById($id);
            $msj  = ($data != null) ? "Insumo eliminado correctamente y precio unitario actualizado!." : "";
        }

        header('Content-Type: application/json');
        echo json_encode($msj);
        $this->layout('layout/blank');
    }

    public function deletecostouniAction()
    {
        $id       = $this->params()->fromPost("id");
        $cuentas  = $this->getMapper("CostoUnitarioCuentaMapper")->getAllById($id);
        $concepto = $this->getMapper("CostoUnitarioMapper")->getById($id);
        if ($cuentas == null) {
            $data    = $this->getMapper("CostoUnitarioMapper")->deleteById($id);
            $message = array('msj' => 'Concepto ' . $concepto->getConcepto() . ' a sido eliminado correctamente!.');
        } else {
            $cuenta  = (count($cuentas) > 1) ? 'cuentas' : 'cuenta';
            $message = array('msj' => 'No es posible elimiar el concepto ' . $concepto->getConcepto() . ' se encuentra relacionado a ' . count($cuentas) . ' ' . $cuenta . '!..');
        }

        header('Content-Type: application/json');
        echo json_encode($message);
        $this->layout('layout/blank');
    }

    public function deleteequipoAction()
    {
        $id = $this->params("id");
        if ($this->getMapper("RegistroAsignacionMapper")->getByEquipoId($id) == null) {
            $this->getMapper("EquipoVehiculoMapper")->deleteById($id);
            $message = array(true);
        } else {
            $message = array(false);
        }

        header('Content-Type: application/json');
        echo json_encode($message);
        $this->layout('layout/blank');
    }

    public function getdatabyconceptoAction()
    {
        $concepto = $this->params()->fromPost("concepto");
        $data     = $this->getMapper("CostoUnitarioMapper")->getByConceptoArr($concepto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getexistenciaAction()
    {
        $id   = $this->params()->fromQuery("id");
        $data = $this->getMapper("ExistenciaMapper")->getByIdArray($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function updateexistenciaAction()
    {

        $existencia = $this->getMapper("ExistenciaMapper")->getById($this->params()->fromPost("id_existencia"));
        $existencia->setMarca($this->params()->fromPost("marca"));
        $existencia->setNumserie($this->params()->fromPost("serie"));
        $existencia->setObservaciones($this->params()->fromPost("observaciones"));

        $this->getMapper('ExistenciaMapper')->save($existencia);
        echo json_encode("true");
        $this->layout('layout/blank');

    }

    public function tranferirequipoAction()
    {
        $equipo = $this->params()->fromPost("equipo");
        $obra   = $this->params()->fromPost("obra");
        $obraorigen  = $this->params()->fromPost("id");
        $ot = $this->getMapper("OrdenTrabajoMapper")->getOtDataById($obra);
        $origen = $this->getMapper("OrdenTrabajoMapper")->getOtDataById($obraorigen);
        $destino = $this->getMapper("OrdenTrabajoMapper")->getOtDataById($obra);
        $result = true;
        if ($equipo != "") {
            foreach ($equipo as $key => $value) {
                $asignacion = $this->getMapper("VehiculoAsignadoMapper")->getById($value);
                $asignacion->setOrdenId($obra);
                $this->getMapper("VehiculoAsignadoMapper")->save($asignacion);
                        $registro_asignacion = new RegistroAsignacionEntity();
                        $registro_asignacion->setIdEquipoVehiculo( $asignacion->getVehiculoId()  );
                        $registro_asignacion->setSerie($asignacion->getSerieEquipo() );
                        $registro_asignacion->setOt($obra);
                        $registro_asignacion->setInicio($ot[0]['fecha_ini']);
                        $registro_asignacion->setFin($ot[0]['fecha_fin']);
                        $registro_asignacion->setIdUsuario($this->getIdUserAuth());
                        $registro_asignacion->setIsPerson( "0");
                        $registro_asignacion->setTipo("Transferencia");
                        $registro_asignacion->setOrigen($origen[0]['folio']);
                        $registro_asignacion->setDestino($ot[0]['folio']);
                        $this->getMapper('RegistroAsignacionMapper')->save($registro_asignacion);   
            }
            $this->Registro("Ordenes de Trabajo", "Resignación de Equipo",$obra);
        } else {
            $result = false;
        }
        header('Content-Type: application/json');
        echo json_encode($result);
        $this->layout('layout/blank');

    }

    public function getinsumodetailAction()
    {
        $insumo = $this->params("id");
        $data   = $this->getMapper('CostoUnitarioMapper')->getByIdCuentaArr($insumo);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getinsumodetailbypresupustoAction()
    {
        $insumo      = $this->params()->fromQuery("insumo");
        $presupuesto = $this->params()->fromQuery("presu");
        $data = $this->getMapper('RelInsumoItemMapper')->getByIdCuentaArr($insumo, $presupuesto);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    /*public function deleteformatAction()
    {
        $message = "";
        $id = $this->params("id");
        if ($id != "") {
            $formato = $this->getMapper("WizzardMapper")->getArrayById( $id );
            if ($formato != ""){
                $capturas = $this->getMapper("WizzardDataMapper")->getArrayByFormatId($id);
                if ($capturas == ""){
                    $this->getMapper("WizzardMapper")->deleteById($id);
                    $message = array("msg"=>"Formato Eliminado ( ".$formato[0]['name']." ) elimando con exito!.","type" => "success");
                }else{
                    $message = array("msg"=>"Lo siento no es posible eliminar el formato ( ".$formato[0]['name']." ) ya que cuenta con capturas almacenadas.","type" => "danger");
                }
            }else{
                $message = array("msg"=>"El formato ya fue eliminado","type"=>"danger");
            }
            return $this->redirect()->toRoute('wizzard', array("action" => "wizzardlist"));
        }

    }*/

    public function savewizzardAction($id = "")
    {
        $result = false;
        if ($this->params()->fromPost("content")){
            $name = $this->params()->fromPost("name");
            if($this->getMapper("WizzardMapper")->getByName($name)){
                $entity = new WizzardEntity();
                $entity->setName($name);
                $categoria = $this->params()->fromPost("categoria");
                $entity->setCategoria( $categoria );
                $content = $this->params()->fromPost("content");
                $entity->setContent( $content );
                $orientacion = $this->params()->fromPost("orientacion");
                $entity->setOrientacion( $orientacion );
                $leyenda = $this->params()->fromPost("leyenda");
                $entity->setLeyenda($leyenda);
                $hrs = $this->params()->fromPost("hrs");
                $entity->setHrs($hrs);
                $tipo_muestra = $this->params()->fromPost("tipo_muestra");
                $entity->setTipoMuestra($tipo_muestra);
                $cat_muestra = $this->params()->fromPost("cat_muestra");
                $entity->setCatMuestra($cat_muestra);
                $tipo = $this->params()->fromPost("tipo");
                $entity->setTipo($tipo);
                $WizzarData = $this->getMapper("WizzardMapper")->save($entity);
                $NewIdWizzard = $WizzarData->getGeneratedValue();
                $normacheck = $this->params()->fromPost("normacheck");
                if ( $normacheck != null){
                    foreach ($normacheck as $key => $value) {
                        $WizzardNormasAplicablesEntity = new WizzardNormasAplicablesEntity();
                        $WizzardNormasAplicablesEntity->setWizzardId($NewIdWizzard);
                        $WizzardNormasAplicablesEntity->setNormasAplicablesId($value);
                        $this->getMapper("WizzardNormasAplicablesMapper")->save($WizzardNormasAplicablesEntity);
                    }
                }
                $procedimientoscheck = $this->params()->fromPost("procedimientoscheck");
                if ($procedimientoscheck != null){
                    foreach ($procedimientoscheck as $key => $value) {
                        $WizzardTipoEnsayeEntity = new WizzardTipoEnsayeEntity();
                        $WizzardTipoEnsayeEntity->setWizzardId($NewIdWizzard);
                        $WizzardTipoEnsayeEntity->setTipoEnsayeId($value);
                        $this->getMapper("WizzardTipoEnsayeMapper")->save($WizzardTipoEnsayeEntity);
                    }
                }
                $this->Registro("Wizzard", "Creación de Formato",$NewIdWizzard);
                $result = true;
            }
        }
        header('Content-Type: application/json');
        echo json_encode($result);
        //exit(0);
        $this->layout('layout/blank');
    }

    public function getnormasAction()
    {
        $id     = $this->params()->fromPost("id");
        $result = $this->getMapper("WizzardNormasAplicablesMapper")->getByWizzardId($id);
        header('Content-Type: application/json');
        echo json_encode($result);
        $this->layout('layout/blank');
    }

    public function getproceAction()
    {
        $id     = $this->params()->fromPost("id");
        $result = $this->getMapper("WizzardTipoEnsayeMapper")->getByWizzardId($id);
        header('Content-Type: application/json');
        echo json_encode($result);
        $this->layout('layout/blank');
    }

    public function getcatforformatAction()
    {
        $cat_formato     = $this->params()->fromPost("id");
        $result = $this->getMapper("FormatosTipoCatMapper")->getCatForFormat($cat_formato);
        header('Content-Type: application/json');
        echo json_encode($result);
        $this->layout('layout/blank');
    }

    public function getcatfortypeandformatAction()
    {
        $cat_formato     = $this->params()->fromPost("id");
        $tipo_muestra    = $this->params()->fromPost("tipo_muestra");
        $result = $this->getMapper("FormatosTipoCatMapper")->getCatForTypeAndFormat($cat_formato, $tipo_muestra);
        header('Content-Type: application/json');
        echo json_encode($result);
        $this->layout('layout/blank');
    }

    public function getwizzardinfoAction()
    {
        $id   = $this->params("id");
        $data = $this->getMapper('WizzardDataMapper')->getJsonArrayById($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getwizzardcontentAction()
    {
        $id   = $this->params("id");
        $data = $this->getMapper("WizzardMapper")->getArrayById($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getformatidAction()
    {
        if ($this->params()->fromPost("id")) {
            $id     = $this->params()->fromPost("id");
            $entity = $this->getMapper("WizzardMapper")->getByIdEntity($id);
            $entity->setLeyenda($this->params()->frompost("leyenda"));
            $entity->setHrs($this->params()->frompost("hrs"));
            $entity->setCategoria($this->params()->frompost("categoria"));
            $entity->setTipoMuestra($this->params()->frompost("tipo_muestra"));
            $entity->setCatMuestra($this->params()->frompost("cat_muestra"));
            $entity->setTipo($this->params()->frompost("tipo"));
            $this->getMapper("WizzardMapper")->save($entity);
        } else {
            $id   = $this->params()->fromQuery("id");
            $data = $this->getMapper('WizzardMapper')->getDataById($id);
            header('Content-Type: application/json');
            echo json_encode($data);
            $this->layout('layout/blank');
        }
    }

    public function changedisponibilidadAction()
    {
        $id = $this->params()->fromPost("id");

        $entity = $this->getMapper("WizzardMapper")->getByIdEntity($id);
        $entity->setDisponibilidad($this->params()->frompost("disponibilidad"));
        $this->getMapper("WizzardMapper")->save($entity);

        if ($this->params()->frompost("disponibilidad") == 0) {
            $data = "No disponible";
        }else if ($this->params()->frompost("disponibilidad") == 1) {
            $data = "Disponible";
        }else{
            $data = "-";
        }
        
        header('Content-Type: application/json');
        echo json_encode($data);
        exit(0);
    }

     public function formatstatusAction()
    {
        $resp = "";
        $id  = $this->params()->fromPost("id");
        if ($this->params()->fromPost("id")){ 
         $wdata = $this->params()->fromPost("wdata");
         $varsu = $this->getMapper("WizzardDataMapper")->getById($wdata)->getFormatoId();
         $tipo = $this->getMapper("WizzardMapper")->getByIdEntity($varsu)->getTipo(); 
         if($tipo == 1){
                 //obtiene la prueba relacionada al formato
                $entity = $this->getMapper("PruebasMapper")->getById($id);
                    //revisa si hay status
                    if( $this->params()->fromPost("status") ){
                        //guarda el estatus
                        $status = $this->params()->fromPost("status");
                        $entity->setStatus($status);
                        //dependiento el estatus que se guardo se anañen ciertas cosas
                        if($status === "20"){
                            //si es motivo de suspensión solo añadira el comentario
                            $entity->setMotivoSuspension($this->params()->fromPost("motivo"));
                            $this->Registro("Ensayes", "Ensaye Suspendido",$id);
                        }else if($status === "6"){
                            //si es motivo de cancelación solo añadira el comentario
                            $entity->setMotivoCancelacion($this->params()->fromPost("motivo"));  
                            $this->Registro("Ensayes", "Ensaye Cancelado",$id);  
                        }else if($status ==="27"){
                            //Si ya es validado se guarda la fecha, hora y usuario que lo hizo
                            $id_user = $this->getIdUserAuth();
                            date_default_timezone_set("America/Mexico_City");
                            $entity->setFechaValidacion(date("Y-m-d H:i:s") );
                            $entity->setValidacionUser($id_user);
                            $this->Registro("Ensayes", "Ensaye validado",$id);
                        }else if($status ==="26"){
                            //Se envia a validación y guarda la fecha de el ultimo guardado
                            $entity->setStatus($status);  
                            date_default_timezone_set("America/Mexico_City");
                            $entity->setFechaGuardadoFinal(date("Y-m-d H:i:s") );
                            $this->Registro("Ensayes", "Ensaye Revisado",$id);
                        }else if($status === "30"){
                            //obtiene los datos del formato relacionado a la prueba
                            $WizzardDataEntity = $this->getMapper("WizzardDataMapper")->getById($entity->getFormatoId());
                                //nuevo formato con la misma información que el anterior
                                $NewWizzardDataEntity = new WizzardDataEntity();
                                $NewWizzardDataEntity->setFormatoId( $WizzardDataEntity->getFormatoId() );
                                $NewWizzardDataEntity->setData( $WizzardDataEntity->getData() );
                                $NewWizzardDataEntity->setUserId( $WizzardDataEntity->getUserId() );
                                $NewWizzardDataEntity->setPruebaId( $WizzardDataEntity->getPruebaId() );
                                //$NewWizzardDataEntity->setEspecimen( $WizzardDataEntity->getEspecimen() );
                                //$NewWizzardDataEntity->setObservacion( $WizzardDataEntity->getObservacion() );
                                $wizzardDataData = $this->getMapper("WizzardDataMapper")->save( $NewWizzardDataEntity );
                                $idFormato = $wizzardDataData->getGeneratedValue();
                                $this->Registro("Ensayes", "Ensaye ".$id." con modificaciones del cliente, nuevo ensaye ".$idFormato,$idFormato);
                            //El Id del nuevo se le asigna a la prueba 
                            $entity->setFormatoId($idFormato);
                            $asignadosFormat = $this->getMapper("WizzardDataMapper")->getArrayByPruebaId($id);
                            $NoasignadosFormat = count($asignadosFormat)-1;
                            $entity->setNumeroPrueba($id."-Modif-".$NoasignadosFormat);                    

                        }
                        $resp = "cambio status";
                    }
                    //revisa si hay estimado
                    if( $this->params()->fromPost("estimado") ){
                        $resp = "cambio estimado";
                        //agrega el estimado al campo
                        $entity->setEstimado($this->params()->fromPost("estimado"));                        
                    }
                    //revisa si hay facturado
                    if( $this->params()->fromPost("facturado") ){
                        //agrega el facturado al campo
                        $entity->setFacturado($this->params()->fromPost("facturado"));
                        $resp = "cambio facturado";
                    }
                $this->getMapper("PruebasMapper")->save($entity);
         }else{
            //si es 2
            $totalformats = $this->getMapper("PruebasMapper")->getPruebasByFormatoFull($wdata);
            $status = $this->params()->fromPost("status");
                $strat = "";
            if($status == "26"){
                $strat = "25";
            }else if($status == "36"){
                $strat = "26";
            }else if($status == "27"){
                $strat = "36";
            }else if($status == "28"){
                $strat = "27";
            }else if ($status == "4"){
                $strat = "";
            }
            $formatssaved = $this->getMapper("PruebasMapper")->getPruebasByFormato($wdata,$strat);
            if(count($totalformats) == count($formatssaved)){
              if($formatssaved != null){
                foreach ($formatssaved as $key => $value) {
                     $entity = $this->getMapper("PruebasMapper")->getById($value['id']);  
                      if( $this->params()->fromPost("status") ){
                        $entity->setStatus($status);
                        if($status === "20"){
                            //si es motivo de suspensión solo añadira el comentario
                            $entity->setMotivoSuspension($this->params()->fromPost("motivo"));
                        }
                        if($status === "6"){
                            //si es motivo de cancelación solo añadira el comentario
                            $entity->setMotivoCancelacion($this->params()->fromPost("motivo"));    
                        }
                        if($status ==="27"){
                            //Si ya es validado se guarda la fecha, hora y usuario que lo hizo
                            $id_user = $this->getIdUserAuth();
                            date_default_timezone_set("America/Mexico_City");
                            $entity->setFechaValidacion(date("Y-m-d H:i:s") );
                            $entity->setValidacionUser($id_user);
                        } 
                        if($status ==="26"){
                            //Se envia a validación y guarda la fecha de el ultimo guardado
                            //$entity->setStatus($status);  
                            date_default_timezone_set("America/Mexico_City");
                            $entity->setFechaGuardadoFinal(date("Y-m-d H:i:s"));
                        }
                      } 
                      //revisa si hay estimado
                      if( $this->params()->fromPost("estimado") ){
                            $resp = "cambio estimado";
                            //agrega el estimado al campo
                            $entity->setEstimado($this->params()->fromPost("estimado"));                        
                       }
                        //revisa si hay facturado
                       if( $this->params()->fromPost("facturado") ){
                            //agrega el facturado al campo
                            $entity->setFacturado($this->params()->fromPost("facturado"));
                            $resp = "cambio facturado";
                       }
                       $this->getMapper("PruebasMapper")->save($entity);
                }
              }
                    $resp = "ya guardaron todos";
            }else{
                    $resp = "1";
            }
         }
        } 
        header('Content-Type: application/json');
        echo json_encode($resp);
        $this->layout('layout/blank');
    }

    public function getrecepcionidAction()
    {
        if ($this->params()->fromPost("id")) {
                //Toma ID
                $id = $this->params()->fromPost("id");
                //$id = $this->params("id");
                $idMuestra     = $this->params()->fromPost("muestra");
                $EnsayosDeMuestra = $this->getMapper("EnsayosMapper")->relacionEnsayoMuestraById($idMuestra);
                    $entityMuestra = $this->getMapper("MuestrasMapper")->getById($idMuestra);
                    $entityMuestra->setUbicacionMuestra($this->params()->frompost("ubicacion"));
                    $this->getMapper("MuestrasMapper")->save($entityMuestra);

                    $pruebasId = $this->getMapper("PruebasMapper")->getLabs($EnsayosDeMuestra);
                    $idFormato = 0;
                    foreach ($pruebasId as $key => $value) { 
                                $tipo = $this->getMapper("WizzardMapper")->getByIdEntity( $value['ensaye_id'] )->getTipo();  
                                if($tipo == 1){
                                    //crea el formato para que se capture
                                    $WizzardDataEntity = new WizzardDataEntity();
                                    $WizzardDataEntity->setFormatoId( $value['ensaye_id'] );
                                    //$WizzardDataEntity->setData( json_encode($inputs) );
                                    $WizzardDataEntity->setUserId( $value['laboratorista_id'] );
                                    $WizzardDataEntity->setPruebaId( $value['id'] );
                                    $wizzardDataData = $this->getMapper("WizzardDataMapper")->save( $WizzardDataEntity );
                                    $idFormato = $wizzardDataData->getGeneratedValue();
                                }else{
                                    if($key == 0){
                                        //crea el formato para que se capture
                                        $WizzardDataEntity = new WizzardDataEntity();
                                        $WizzardDataEntity->setFormatoId( $value['ensaye_id'] );
                                        //$WizzardDataEntity->setData( json_encode($inputs) );
                                        $WizzardDataEntity->setUserId( $value['laboratorista_id'] );
                                        $WizzardDataEntity->setPruebaId( $value['id'] );
                                        $wizzardDataData = $this->getMapper("WizzardDataMapper")->save( $WizzardDataEntity );
                                        $idFormato = $wizzardDataData->getGeneratedValue();
                                    }
                                }                 
                                        //Actualiza el estatus del formato y guarda el formato que se genero para capturar
                                        $PruebasEntity = $this->getMapper("PruebasMapper")->getById($value['id']);
                                        $PruebasEntity->setStatus(24);
                                        //$PruebasEntity->setFormatoId($wizzardDataData->getGeneratedValue());
                                        $PruebasEntity->setFormatoId($idFormato);
                                        $this->getMapper("PruebasMapper")->save($PruebasEntity);
                    }

                    $entityRecepcion = $this->getMapper("RecepcionMuestraMapper")->getById($id);
                    $entityRecepcion->setRecibido($this->params()->frompost("recibido"));
                    $entityRecepcion->setStatus(22);
                    $entityRecepcion->setFechaRecibido(date("Y-m-d"));
                    $this->getMapper("RecepcionMuestraMapper")->save($entityRecepcion);
                    $this->Registro("Recepcion de Muestra","Se recibio la muestra",$id);
            $data = "si guardo";
            header('Content-Type: application/json');
            echo json_encode($idMuestra);
            $this->layout('layout/blank');
        } else {
            $id   = $this->params()->fromQuery("id");
            $data = $this->getMapper('RecepcionMuestraMapper')->getAllRecepMus($id);
            header('Content-Type: application/json');
            echo json_encode($data);
            $this->layout('layout/blank');
        }
    }

    public function getotdataAction()
    {
        $id   = $this->params("id");
        $data = $this->getMapper('OrdenTrabajoMapper')->getOtDataById($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function getconocimientobycategoriaAction()
    {
        $id   = $this->params("id");
        $data = $this->getMapper('CatConocimientosMapper')->getByCategoryArray($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->layout('layout/blank');
    }

    public function Registro($type,$accion,$idAccion) 
    {
        $UserId     = $this->layout()->identi->id;
        $NameUser   = $this->getMapper("UsuarioPersonalMapper")->getById( $UserId );
        $LogEntity  = new LogEntity();
        $LogEntity->setType( $type );
        date_default_timezone_set("America/Mexico_City");
        $LogEntity->setFecha( date("Y-m-d H:i:s") );
        $LogEntity->setAccion( $accion );
        $LogEntity->setUserId( $UserId );
        $LogEntity->setNameuser( $NameUser->getNombre()." ".$NameUser->getApaterno()." ".$NameUser->getAmaterno() );      
        $LogEntity->setRed($_SERVER['REMOTE_ADDR']);
        $LogEntity->setIdAccion($idAccion);
        $this->getMapper('LogMapper')->save( $LogEntity );
    }

    public function getMapper($mapper)
    {
        $sm = $this->getServiceLocator();
        return $sm->get($mapper);
    }

    public function getCategoriaEquipoMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("CategoriaEquipoMapper");
    }

    public function getEquipoVehiculoMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("EquipoVehiculoMapper");
    }

    public function getClienteMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("ClienteMapper");
    }

    public function getClienteContactoMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("ClienteContactoMapper");
    }

    public function getItemDetalleLabMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("ItemDetalleLabMapper");
    }

    public function MunicipiosMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("MunicipiosMapper");
    }

    public function getItemDetalleEquipoMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("ItemDetalleEquipoMapper");
    }

    public function getItemDetallePersonalMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("ItemDetallePersonalMapper");
    }

    public function getItemDetalleEstudiosMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("ItemDetalleEstudiosMapper");
    }

    public function getCatServEstudiosMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("CatServEstudiosMapper");
    }

    public function getCatServLaboratorioMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("CatServLaboratorioMapper");
    }

    public function getCatEquipoMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("CatEquipoMapper");
    }

    public function getCatPersonalMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("CatPersonalMapper");
    }

    public function getPresupuestoMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("PresupuestoMapper");
    }

    public function getVehiculoAsignadoMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("VehiculoAsignadoMapper");
    }

    public function getPresupuestoDetalleMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("PresupuestoDetalleMapper");
    }

    public function getPresupuestoVoboMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("PresupuestoVoboMapper");
    }

    public function getUsuarioPersonalMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("UsuarioPersonalMapper");
    }

    public function getLogMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get("LogMapper");
    }

    public function getHelper($helper)
    {
        return $this->getServiceLocator()->get($helper);
    }


}
