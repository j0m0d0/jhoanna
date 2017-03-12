<?php
	
namespace Application\Model;

class UsuarioPersonalEntity
{
	protected $id;
	protected $nombre;
	protected $apaterno;
	protected $amaterno;
	protected $direccion;
	protected $telefono;
    protected $correo;
	protected $password;
    protected $img_path;
    protected $fecha_imss;
    protected $laboratorio;
    protected $nivel;
    protected $licencia;
    protected $maneja;
    protected $puesto_id;
    protected $rol_id;
    protected $categoria_equipo_id;
    protected $area_nomeclatura;
    protected $id_area;
    protected $notificacion;
    protected $is_baja;
    protected $ultima_baja;
 /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

 /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

 /**
     * @return the $nombre
     */
    public function getNombre()
    {
        return $this->nombre;
    }

 /**
     * @param field_type $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

 /**
     * @return the $apaterno
     */
    public function getApaterno()
    {
        return $this->apaterno;
    }

 /**
     * @param field_type $apaterno
     */
    public function setApaterno($apaterno)
    {
        $this->apaterno = $apaterno;
    }

 /**
     * @return the $amaterno
     */
    public function getAmaterno()
    {
        return $this->amaterno;
    }

 /**
     * @param field_type $amaterno
     */
    public function setAmaterno($amaterno)
    {
        $this->amaterno = $amaterno;
    }

 /**
     * @return the $direccion
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

 /**
     * @param field_type $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

 /**
     * @return the $telefono
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

 /**
     * @param field_type $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

 /**
     * @return the $correo
     */
    public function getCorreo()
    {
        return $this->correo;
    }

 /**
     * @param field_type $correo
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;
    }

 /**
     * @return the $puesto_id
     */
    public function getPuestoId()
    {
        return $this->puesto_id;
    }

 /**
     * @param field_type $puesto_id
     */
    public function setPuestoId($puesto_id)
    {
        $this->puesto_id = $puesto_id;
    }

 /**
     * @return the $categoria_equipo_id
     */
    public function getCategoriaEquipoId()
    {
        return $this->categoria_equipo_id;
    }

 /**
     * @param field_type $categoria_equipo_id
     */
    public function setCategoriaEquipoId($categoria_equipo_id)
    {
        $this->categoria_equipo_id = $categoria_equipo_id;
    }


    public function getImgPath()
    {
        return $this->img_path;
    }

 /**
     * @param field_type $categoria_equipo_id
     */
    public function setImgPath($value)
    {
        $this->img_path = $value;
    }


    public function getFechaImss()
    {
        return $this->fecha_imss;
    }

 /**
     * @param field_type $categoria_equipo_id
     */
    public function setFechaImss($value)
    {
        $this->fecha_imss = $value;
    }

    public function getLaboratorio()
    {
        return $this->laboratorio;
    }

    public function setLaboratorio($value)
    {
        $this->laboratorio = $value;
    }

    public function getNivel()
    {
        return $this->nivel;
    }

    public function setNivel($value)
    {
        $this->nivel = $value;
    }

    public function getLicencia()
    {
        return $this->licencia;
    }

    public function setLicencia($value)
    {
        $this->licencia = $value;
    }

    public function getManeja()
    {
        return $this->maneja;
    }

    public function setManeja($value)
    {
        $this->maneja = $value;
    }

    public function getRolId()
    {
        return $this->rol_id;
    }

    public function setRolId($value)
    {
        $this->rol_id = $value;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;
    }

    public function getAreaNomeclatura()
    {
        return $this->area_nomeclatura;
    }

    public function setAreaNomeclatura($value)
    {
        $this->area_nomeclatura = $value;
    }

    public function getIdArea()
    {
        return $this->id_area;
    }

    public function setIdArea($value)
    {
        $this->id_area = $value;
    }

    public function getNotificacion()
    {
        return $this->notificacion;
    }

    public function setNotificacion($value)
    {
        $this->notificacion = $value;
    }
    
    public function getIsBaja()
    {
        return $this->is_baja;
    }

    public function setIsBaja($is_baja)
    {
        $this->is_baja = $is_baja;

        return $this;
    }

      public function getUltimaBaja()
    {
        return $this->ultima_baja;
    }

    public function setUltimaBaja($value)
    {
        $this->ultima_baja = $value;
    }

    
}