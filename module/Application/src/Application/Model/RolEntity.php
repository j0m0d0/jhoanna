<?php
	
namespace Application\Model;

class RolEntity
{
	protected $id;
	protected $nombre_rol;

	public function getId()
	{
		return $this->id;
	}
	
	public function setId($Value)
	{
		$this->id = $Value;
	}

  	public function getNombreRol()
	{
		return $this->nombre_rol;
	}
  
    public function setNombreRol($Value)
    {
        $this->nombre_rol = $Value;
    }	
}