<?php
	
namespace Application\Model;

class AreaUsuarioEntity
{
	protected $id;
	protected $concepto;

	public function getId()
	{
		return $this->id;
	}
	
	public function setId($Value)
	{
		$this->id = $Value;
	}

  	public function getConcepto()
	{
		return $this->concepto;
	}
  
    public function setConcepto($Value)
    {
        $this->concepto = $Value;
    }	
}