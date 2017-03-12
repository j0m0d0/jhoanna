<?php
	
namespace Application\Model;

class LogEntity
{
	protected $id;
	protected $type;
	protected $fecha;
	protected $accion;
	protected $id_accion;
	protected $user_id;
	protected $name_user;
	protected $red;

	public function getId()
	{
		return $this->id;
	}
	
	public function setId($Value)
	{
		$this->id = $Value;
	}

	/////////////////////////////////

  	public function getType()
	{
		return $this->type;
	}
  
    public function setType($Value)
    {
        $this->type = $Value;
    }

    /////////////////////////////////

    public function getFecha()
	{
		return $this->fecha;
	}
  
    public function setFecha($Value)
    {
        $this->fecha = $Value;
    }

    /////////////////////////////////

    public function getAccion()
	{
		return $this->accion;
	}
  
    public function setAccion($Value)
    {
        $this->accion = $Value;
    }

    /////////////////////////////////

    public function getIdAccion()
	{
		return $this->id_accion;
	}
  
    public function setIdAccion($Value)
    {
        $this->id_accion = $Value;
    }

    /////////////////////////////////

    public function getUserId()
	{
		return $this->user_id;
	}
  
    public function setUserId($Value)
    {
        $this->user_id = $Value;
    }	

    /////////////////////////////////

    public function getNameUser()
	{
		return $this->name_user;
	}
  
    public function setNameUser($Value)
    {
        $this->name_user = $Value;
    }

    /////////////////////////////////

    public function getRed()
	{
		return $this->red;
	}
  
    public function setRed($Value)
    {
        $this->red = $Value;
    }

    /////////////////////////////////	

    
}