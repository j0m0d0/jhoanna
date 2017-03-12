<?php
	
namespace Application\Model;

class ModuloEntity
{
	protected $id;
	protected $modulo;
	protected $titulo;

	public function getId()
	{
		return $this->id;
	}
	
	public function setId($Value)
	{
		$this->id = $Value;
	}

  	public function getModulo()
	{
		return $this->modulo;
	}
  
    public function setModulo($Value)
    {
        $this->modulo = $Value;
    }

    public function getTitulo()
	{
		return $this->titulo;
	}
  
    public function setTitulo($Value)
    {
        $this->titulo = $Value;
    }
}