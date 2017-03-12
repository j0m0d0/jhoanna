<?php
 namespace Application\Model;

 use Zend\Db\Adapter\Adapter;
 use Application\Model\ModuloEntity;
 use Zend\Stdlib\Hydrator\ClassMethods;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Select;
 use Zend\Db\ResultSet\HydratingResultSet;	
 
 class ModuloMapper
 {
     protected $tableName = 'modulo';
     protected $dbAdapter;
     protected $sql;

     public function __construct(Adapter $dbAdapter)
     {
         $this->dbAdapter = $dbAdapter;
         $this->sql = new Sql($dbAdapter);
         $this->sql->setTable($this->tableName);
     }

     public function fetchAll()
     {
         $select = $this->sql->select();
         $select->order(array('id DESC'));

         $statement = $this->sql->prepareStatementForSqlObject($select);
         $results = $statement->execute();

         $entityPrototype = new ModuloEntity();
         $hydrator = new ClassMethods();
         $resultset = new HydratingResultSet($hydrator, $entityPrototype);
         $resultset->initialize($results);
         return $resultset;
     }
	 
	 public function save(ModuloEntity $entity)
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
	 
	 public function getById($id)
	 {
	      $statement = $this->sql->prepareStatementForSqlObject( $this->sql->select()->where( array('id' => $id) ) );
	      $result = $statement->execute()->current();
	      if (!$result) {
	          return null;
	      }

	      $hydrator = new ClassMethods();
	      $entity = new ModuloEntity();
	      $hydrator->hydrate($result, $entity);

	      return $entity;
	  }

	  public function deleteById($id)
	   {
	       $delete = $this->sql->delete()->where(array('id' => $id));

	       $statement = $this->sql->prepareStatementForSqlObject($delete);
	       return $statement->execute();
	   }

	   public function fetchAllArr()
     {
         $select = $this->sql->select();
         $select->order(array('id DESC'));

         $statement = $this->sql->prepareStatementForSqlObject($select);
         $results = $statement->execute();

         $data = array();
         foreach ($results as $value) {
         	$data [] = $value['modulo'];
         }
         return $data;
     }
 }