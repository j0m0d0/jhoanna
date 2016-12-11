<?php
	
namespace Application\Model;

class DataMediaEntity
{
	protected $id;
	protected $archivo_id;
	protected $path;
	protected $fecha;
	protected $usuario_id;
	protected $comentario;

	public function getId()
	{
		return $this->id;
	}
	
	public function setId($Value)
	{
		$this->id = $Value;
	}

	public function getArchivoId()
	{
		return $this->archivo_id;
	}
	
	public function setArchivoId($Value)
	{
		$this->archivo_id = $Value;
	}

	public function getPath()
	{
		return $this->path;
	}
	
	public function setPath($Value)
	{
		$this->path = $Value;
	}

	public function getFecha()
	{
		return $this->fecha;
	}
	
	public function setFecha($Value)
	{
		$this->fecha = $Value;
	}

	public function getUsuarioId()
	{
		return $this->usuario_id;
	}
	
	public function setUsuarioId($Value)
	{
		$this->usuario_id = $Value;
	}

	public function getComentario()
	{
		return $this->comentario;
	}
	
	public function setComentario($Value)
	{
		$this->comentario = $Value;
	}


}