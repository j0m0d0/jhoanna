<?php
	
namespace Application\Model;

class LogLoginEntity
{
	protected $id;
	protected $type;
	protected $fecha;
	protected $user_name;
	protected $user_id;

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

    public function getUserName()
	{
		return $this->user_name;
	}
  
    public function setUserName($Value)
    {
        $this->user_name = $Value;
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
}