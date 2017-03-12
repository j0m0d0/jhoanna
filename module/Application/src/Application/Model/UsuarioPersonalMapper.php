<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Application\Model\UsuarioPersonalEntity;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\HydratingResultSet;	

class UsuarioPersonalMapper 
{
	protected $tableName = 'usuario_personal';
	protected $dbAdapter;
	protected $sql;

	public function __construct(Adapter $dbAdapter)
	{
		$this->dbAdapter = $dbAdapter; 
		$this->sql = new Sql($dbAdapter);
		$this->sql->setTable($this->tableName);
	}

	public function fetchAllPersonal($is_baja = null)
	{
		$select = $this->sql->select();

		//si el empleado esta dado de baja
		($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;

		$select->order(array('id DESC'));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();

		$entityPrototype = new UsuarioPersonalEntity();
		$hydrator = new ClassMethods();
		$resultset = new HydratingResultSet($hydrator, $entityPrototype);
		$resultset->initialize($results);
		return $resultset;
	}

	public function fetchAll($is_baja = null)
	{
		$select = $this->sql->select();
		//si el empleado esta dado de baja
		($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
		$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
		$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
		$select->join( array("p" => "puesto"),"p.id = usuario_personal.puesto_id",array("nombre_puesto"=>"concepto"),$select::JOIN_LEFT);
		$select->where( array('correo !="" ') );
		$select->order(array('id DESC'));

		 /*$statement = $this->sql->prepareStatementForSqlObject($select);
		 $results = $statement->execute();
		 $data = array();
		 foreach ($results as $value) {
			$data [] = $value;
		 }
		 return $data;*/

		 $entityPrototype = new CatServEstudiosEntity();
		 $hydrator = new ClassMethods();
		 $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		 $resultset->initialize($results);
		 return $resultset;
		}

		public function fetchAllData($is_baja = null)
		{
			$select = $this->sql->select();
			//si el empleado esta dado de baja
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
			$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
			$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
			$select->join( array("p" => "puesto"),"p.id = usuario_personal.puesto_id",array("nombre_puesto"=>"concepto"),$select::JOIN_LEFT);
			$select->where( array('correo !="" ') );
			$select->order(array('usuario_personal.nombre'));
			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [] = $value;
			}
			return $data;
		}

		public function datosPersonalUsuario($id,$is_baja = null)
		{
			$select = $this->sql->select();
			//si el empleado esta dado de baja
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
			$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
			$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
			$select->join( array("p" => "puesto"),"p.id = usuario_personal.puesto_id",array("nombre_puesto"=>"concepto"),$select::JOIN_LEFT);
			/*$select->where( array('correo !="" ') );*/
			/*$select->order(array('usuario_personal.nombre'));*/
			$select->where( array('usuario_personal.id' => $id));
			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [] = $value;
			}
			return $data;
		}


		public function fetchAllName($is_baja = null)
		{
			$select = $this->sql->select();
			//si el empleado esta dado de baja
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
			$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
			$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
			$select->join( array("p" => "puesto"),"p.id = usuario_personal.puesto_id",array("nombre_puesto"=>"concepto"),$select::JOIN_LEFT);
			$select->where( array('correo !="" ') );
			$select->order(array('usuario_personal.nombre'));

			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [] = $value;
			}
			return $data;

		 /*$entityPrototype = new CatServEstudiosEntity();
		 $hydrator = new ClassMethods();
		 $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		 $resultset->initialize($results);
		 return $resultset;*/
		}

		public function fetchAllAcceso($is_baja = null)
		{
			$select = $this->sql->select();
			//si el empleado esta dado de baja
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
		 	//$select->join( array("r" => "rol"),"r.id = usuario.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
			$select->join(array("ma" => "modulo_acceso"),"ma.rol_id = usuario_personal.rol_id",array('modulo_id'));
			$select->join(array("m" => "modulo"),"m.id = ma.modulo_id",array("modulo"));
			$select->order(array('id DESC'));

			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
			//$data [$value['id']]['allow'][] = 'home';
				$data [$value['rol_id']]['allow'][] = $value ['modulo'];
			}
			return $data;

		 /*$entityPrototype = new CatServEstudiosEntity();
		 $hydrator = new ClassMethods();
		 $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		 $resultset->initialize($results);
		 return $resultset;*/
		}

		public function save(UsuarioPersonalEntity $entity)
		{
			$hydrator = new ClassMethods();
			$data = $hydrator->extract($entity);

			if ($entity->getId()) {
				 // update action
				$action = $this->sql->update();
				$action->set($data);
				$action->where(array('id' => $entity->getId()));
			}else{
				 // insert action
				$action = $this->sql->insert();
				 //unset($data['id']);
				$action->values($data);
			}
			$statement = $this->sql->prepareStatementForSqlObject($action);
			$result = $statement->execute();

			if (!$entity->getId()) {
				 //$entity->setIdentity($result->getGeneratedValue());
			}
			return $result;
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		public function deUsuarioAPersonal($id_user)
		{
		//$hydrator = new ClassMethods();
		 //$data = $hydrator->extract($entity);

			if ($id_user) {
				 // update action
				$action = $this->sql->update();
				$action->set(array("correo"=> null,"password"=>null));
				$action->where(array('id' => $id_user));
			}else{
				 // insert action
				$action = $this->sql->insert();
				 //unset($data['id']);
				$action->values($data);
			}
			$statement = $this->sql->prepareStatementForSqlObject($action);
			$result = $statement->execute();

			 /*if (!$entity->getId()) {
				 //$entity->setIdentity($result->getGeneratedValue());
			 }
			 return $result;*/
			}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
			public function getById($id)
			{
				$statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('id' => $id) ) );
				$result = $statement->execute()->current();
				if (!$result) {
					return null;
				}

				$hydrator = new ClassMethods();
				$entity = new UsuarioPersonalEntity();
				$hydrator->hydrate($result, $entity);

				return $entity;
			}

			public function empleadosById($id, $is_baja = null)
			{
				$select = $this->sql->select();
				//si el empleado esta dado de baja
				($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
		 //$select->where(array('correo = "" or correo = isnull'));
		 //$select->where(array("correo = '' or correo is null"));
				$select->where( array('id' => $id));
				$select->order(array('nombre'));


				$statement = $this->sql->prepareStatementForSqlObject($select);
				$results = $statement->execute();

				$entityPrototype = new UsuarioPersonalEntity();
				$hydrator = new ClassMethods();
				$resultset = new HydratingResultSet($hydrator, $entityPrototype);
				$resultset->initialize($results);
				return $resultset;
			}


			public function getByIdArr($id)
			{
				$statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('id' => $id) ) );
				$result = $statement->execute()->current();
				if (!$result) {
					return null;
				}

		  /*$hydrator = new ClassMethods();
		  $usuario = new UsuarioEntity();
		  $hydrator->hydrate($result, $usuario);*/

		  return $result;
		}

		public function getByIdDataArr($id)
	{
		$select = $this->sql->select();
		$select->where(array('usuario_personal.id' => $id));
		//$select->where(array('is_baja' => 0));
		//$select->join(array('suc'=>'sucursal'), 'suc.id = usuario_personal.id_sucursal',array('sucursal'));
		$select->join(array('p'=>'puesto'), 'p.id = usuario_personal.puesto_id',array('puesto'=>'concepto','id_superior'));
		$select->join(array('r'=>'rol'), 'usuario_personal.rol_id = r.id',array('nombre_rol'));
		$select->join(array('a'=>'area_usuario'), 'a.id = usuario_personal.id_area',array('concepto'));

		$statement = $this->sql->prepareStatementForSqlObject($select);

		$result = $statement->execute()->current();
		if (!$result) {
			return null;
		}

	  /*$hydrator = new ClassMethods();
	  $usuario = new UsuarioEntity();
	  $hydrator->hydrate($result, $usuario);*/

	  return $result;
	}


		public function getByIdArea($id)
		{
			$statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('id_area' => $id) ) );
			$result = $statement->execute()->current();
			if (!$result) {
				return null;
			}

			$hydrator = new ClassMethods();
			$entity = new UsuarioPersonalEntity();
			$hydrator->hydrate($result, $entity);

			return $entity;
		}

		public function getByIdAreaArr($id, $is_baja = null)
		{
		  /*$statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('id_area' => $id) ) );
		  $result = $statement->execute()->current();
		  if (!$result) {
			  return null;
			}*/

		  /*$hydrator = new ClassMethods();
		  $entity = new UsuarioPersonalEntity();
		  $hydrator->hydrate($result, $entity);

		  return $entity;*/
		  $select = $this->sql->select();
		  ($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0) AND notificacion = 1' ) ) ;
		  $select->where( array('id_area' => $id) );


		  $statement = $this->sql->prepareStatementForSqlObject($select);
		  $results = $statement->execute();
		  $data = array();
		  foreach ($results as $value) {
		  	$data [] = $value;
		  }
		  return $data;
		}

		public function deleteById($id) 
		{
			$delete = $this->sql->delete()->where(array('id' => $id));

			$statement = $this->sql->prepareStatementForSqlObject($delete);
			return $statement->execute();
		}

		public function getByCorreo($correo)
		{
			$statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('correo' => $correo) ) );
			$result = $statement->execute()->current();
			if (!$result) {
				return null;
			}

			$hydrator = new ClassMethods();
			$entity = new UsuarioPersonalEntity();
			$hydrator->hydrate($result, $entity);

			return $entity;
		}

		public function getByIdRol($id_rol)
		{
			$statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('rol_id' => $id_rol) ) );
			$result = $statement->execute()->current();
			if (!$result) {
				return null;
			}

			$hydrator = new ClassMethods();
			$entity = new UsuarioPersonalEntity();
			$hydrator->hydrate($result, $entity);

			return $entity;
		}

		public function getByIdPuesto($puesto_id)
		{
			$statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('puesto_id' => $puesto_id) ) );
			$result = $statement->execute()->current();
			if (!$result) {
				return null;
			}

			$hydrator = new ClassMethods();
			$entity = new UsuarioPersonalEntity();
			$hydrator->hydrate($result, $entity);

			return $entity;
		}


		public function getByRolArray($rol_id, $is_baja = null)
		{
			$select = $this->sql->select();
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
			$select->where( array('rol_id' => $rol_id) );

			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [] = $value;
			}
			return $data;
		}

		public function getByRolInArray( $rol_id, $is_baja = null) 
		{
			$select = $this->sql->select();
			
			foreach ($rol_id as $key => $value) {
				$select->where( array("usuario_personal.rol_id" => $value) ,\Zend\Db\Sql\Predicate\PredicateSet::OP_OR );
				($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0) AND notificacion = 1' ) ) ;//
			}
		$statement = $this->sql->prepareStatementForSqlObject($select);
		 $results = $statement->execute();
		 $data = array();
			 foreach ($results as $value) {
			 	$data [] = $value;
			 }
		 	return $data;
		}

		public function getByRolInArrayMail( $rol_id, $is_baja = null) 
		{
			/*$select = $this->sql->select();*/
			$select = $this->sql->select()->columns(array("correo"));
			foreach ($rol_id as $key => $value) {
				$select->where( array("usuario_personal.rol_id" => $value) ,\Zend\Db\Sql\Predicate\PredicateSet::OP_OR );
				($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0) AND notificacion = 1' ) ) ;
			}

			
			
			$statement = $this->sql->prepareStatementForSqlObject($select);
		 /*echo "<pre>";
		 print_r($statement);
		 echo "</pre>";*/
		 $results = $statement->execute();
		 $data = array();
		 foreach ($results as $value) {
		 	$data [] = $value;
		 }
		 return $data;
		}

		public function getMailByRol( $rol_id, $is_baja = null )
		{
			$select = $this->sql->select()->columns(array("correo"));
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0) AND notificacion = 1' ) ) ;
			$select->where( array('rol_id' => $rol_id) );

			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [] = $value['correo'];
		//$data [] = $value;
			}
			return $data; 
		}

		public function getMailByRoles($rol_id, $is_baja = null )
		{
			$select = $this->sql->select();
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0) AND notificacion = 1' ) ) ;
			$select->where( array('usuario_personal.rol_id' => $rol_id) );
			$select->where( array('correo !="" ') );
			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [] = $value;
			}
			return $data;
		}

		public function personalWizzard($is_baja = null)
		{
			$select = $this->sql->select();
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
			$select->join(array('wd'=>'wizzard_data'), 'wd.user_id = usuario_personal.id',array('user_id'),'right');
			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [$value['id']] = $value;
			}
			return $data;
		}


		public function disponibilidad($filters, $is_baja = null)
		{
			$select = $this->sql->select();
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
			$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
			$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
			$select->join( array("pa" => "personal_asignado"),"pa.personal_id = usuario_personal.id",array("*"),'left');
		 //$select->join( array("p" => "presupuesto"),"p.id = pa.orden_id",array("folio"),'left');
			$select->join( array("ot" => "orden_trabajo"),"ot.id = pa.orden_id",array("folio_ot" => "folio"),'left');
		 //$select->order(array('id DESC'));

			if ($filters['folio'] !="") {
				$select->where(array('ot.folio'=> $filters['folio'] )); 
			}

			if ( $filters['status'] == "temporal" ) {
				$select->where(array("pa.definitivo = '0' "));
			}elseif ( $filters['status'] == "definitivo" ) {
				$select->where(array("pa.definitivo = '1' "));
			}elseif ( $filters['status'] == "libre" ) {
				$select->where(array("pa.definitivo is null"));
			}

		 /*if  ($filters['certificacion'] !="") {
			$select->where(array('pa.definitivo'=> $filters['status'] )); 
		}*/

		if ($filters['fecha_ini'] !="") {
			$select->where(array("pa.fecha_inicio > ".$filters['fecha_ini'] ));
		}

		if ($filters['fecha_fin'] !="") {
			$select->where(array("pa.fecha_fin < ". $filters['fecha_fin'] ));
		}

		if ($filters['nombre'] !="") {
			$select->where(array('usuario_personal.nombre'=> $filters['nombre'] )); 
		}
		if ($filters['a_paterno'] !="") {
			$select->where(array('usuario_personal.apaterno'=> $filters['a_paterno'] ));
		}

		if ($filters['a_materno'] !="") {
			$select->where(array('usuario_personal.amaterno'=> $filters['a_materno'] ));
		}


		$select->where(array('a.concepto'=>'Laboratorio'));
		$select->order(array('usuario_personal.nombre ASC'));
		$select->order(array('pa.definitivo ASC'));



		$statement = $this->sql->prepareStatementForSqlObject($select);

		$results = $statement->execute();
		$data = array();
		foreach ($results as $value) {
			$data [] = $value;
		}
		return $data;

		 /*$entityPrototype = new CatServEstudiosEntity();
		 $hydrator = new ClassMethods();
		 $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		 $resultset->initialize($results);
		 return $resultset;*/
		}


		public function empleados($is_baja = null)
		{
			$select = $this->sql->select();
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
		 //$select->where(array('correo = "" or correo = isnull'));
			$select->where(array("correo = '' or correo is null"));
			$select->order(array('nombre'));

			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();

			$entityPrototype = new UsuarioPersonalEntity();
			$hydrator = new ClassMethods();
			$resultset = new HydratingResultSet($hydrator, $entityPrototype);
			$resultset->initialize($results);
			return $resultset;
		}

		public function getPersonalByAreaNotVehAsig($area="Laboratorio")
		{
			$query = "select 
			distinct(up.id),
			au.concepto,
			rv.responsable, 
			va.id_asignacion, 
			up.nombre,
			up.apaterno,
			up.amaterno
			from usuario_personal as up
			left join area_usuario as au
			on au.id = up.id_area
			left join req_vehiculo as rv
			on rv.responsable = up.id
			left join vehiculo_asignado as va
			on va.solicitud_id = rv.id
			where au.concepto = 'Laboratorio' AND va.id_asignacion is null AND (is_baja is null or is_baja = 0)
			Order by up.nombre;";
			$statement = $this->dbAdapter->query($query);
			$results = $statement->execute();
			return $results;
		}

	/*public function getPersonalWithVehicle($area="Laboratorio")
	{
		$query = "select 
						distinct(up.id),
						au.concepto,
						rv.responsable, 
						va.id_asignacion, 
						up.nombre,
						up.apaterno,
						up.amaterno
					from usuario_personal as up
						left join area_usuario as au
							on au.id = up.id_area
						left join req_vehiculo as rv
							on rv.responsable = up.id
						left join vehiculo_asignado as va
							on va.solicitud_id = rv.id
					where au.concepto = 'Laboratorio' AND va.id_asignacion is null
					Order by up.nombre;";


		$statement = $this->dbAdapter->query($query);
		$results = $statement->execute();
		return $results;
	}*/

	public function getPersonalByArea($area=null, $is_baja = null)
	{
		$select = $this->sql->select();
		($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
		$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
		$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
		$select->join( array("p" => "puesto"),"p.id = usuario_personal.puesto_id",array("nombre_puesto"=>"concepto"),$select::JOIN_LEFT);
		if($area != null){
			$select->where(array('a.concepto'=>$area));	
		}	

		$select->order(array('usuario_personal.nombre'));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		$data = array();
		foreach ($results as $value) {
			$data [] = $value;
		}
		return $data;

		 /*$entityPrototype = new UsuarioPersonalEntity();
		 $hydrator = new ClassMethods();
		 $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		 $resultset->initialize($results);
		 return $resultset;*/
	}

	public function getPersonalFilters($filters = "",$area = null, $is_baja = null)
	{
		$select = $this->sql->select();
		($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)' ) ) ;
		$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
		$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
		$select->join( array("p" => "puesto"),"p.id = usuario_personal.puesto_id",array("nombre_puesto"=>"concepto"),$select::JOIN_LEFT);
		if($area != null){
			$select->where(array('a.concepto'=>$area));	
		}	

		if($filters != ""){
			if($filters['id'] != ""){
				//$select->where(array('usuario_personal.id'=>$filters['id']));
				$select->where(array("usuario_personal.id LIKE '%".$filters['id']."%'"));
			}
			if($filters['personal'] != ""){
				$select->where(array('usuario_personal.id'=>$filters['personal']));
				//$select->where(array("usuario_personal.id LIKE '%".$filters['id']."%'"));
			}
			if($filters['puesto'] != ""){
				$select->where(array('usuario_personal.puesto_id'=>$filters['puesto']));
				//$select->where(array("usuario_personal.id LIKE '%".$filters['id']."%'"));
			}
			if($filters['area'] != ""){
				$select->where(array('usuario_personal.id_area'=>$filters['area']));
				//$select->where(array("usuario_personal.id LIKE '%".$filters['id']."%'"));
			}
			if($filters['nivel'] != ""){
				$select->where(array('usuario_personal.nivel'=>$filters['nivel']));
				//$select->where(array("usuario_personal.id LIKE '%".$filters['id']."%'"));
			}
			if($filters['correo'] != ""){
				//$select->where(array('usuario_personal.id'=>$filters['id']));
				$select->where(array("usuario_personal.correo LIKE '%".$filters['correo']."%'"));
			}
		}

		$select->order(array('usuario_personal.nombre'));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		$data = array();
		foreach ($results as $value) {
			$data [] = $value;
		}
		return $data;

		 /*$entityPrototype = new UsuarioPersonalEntity();
		 $hydrator = new ClassMethods();
		 $resultset = new HydratingResultSet($hydrator, $entityPrototype);
		 $resultset->initialize($results);
		 return $resultset;*/
	}

		public function getByRolandModulo($rol,$modulo,$area, $is_baja = null)
		{
			$select = $this->sql->select();
			($is_baja == 1) ? $select->where( array('is_baja' => 1 )) : $select->where( array('(is_baja is null or is_baja = 0)  AND notificacion = 1' ) ) ;
		 //$select->join( array("r" => "rol"),"r.id = usuario_personal.rol_id",array("nombre_rol"),$select::JOIN_LEFT);
		 //$select->join( array("a" => "area_usuario"),"a.id = usuario_personal.id_area",array("nombre_area"=>"concepto"),$select::JOIN_LEFT);
		 //$select->join( array("p" => "puesto"),"p.id = usuario_personal.puesto_id",array("nombre_puesto"=>"concepto"),$select::JOIN_LEFT);
			$select->join(array("ma" => "modulo_acceso"),"ma.rol_id = usuario_personal.rol_id",array("id","rol_id","modulo_id"),$select::JOIN_LEFT);
			$select->where( array('correo !="" ') );
			$select->where(array('usuario_personal.id_area'=>$area));
			$select->where(array('ma.modulo_id'=>$modulo));
			$select->where(array('ma.rol_id'=>$rol));
			$select->order(array('usuario_personal.nombre'));

			$statement = $this->sql->prepareStatementForSqlObject($select);
			$results = $statement->execute();
			$data = array();
			foreach ($results as $value) {
				$data [] = $value;
			}
			return $data;
		}
	}
