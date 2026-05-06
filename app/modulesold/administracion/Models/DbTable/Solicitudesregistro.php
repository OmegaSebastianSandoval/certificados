<?php 
/**
* clase que genera la insercion y edicion  de Solicitudes de Registro en la base de datos
*/
class Administracion_Model_DbTable_Solicitudesregistro extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'solicitudes_registro';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'id';

	/**
	 * insert recibe la informacion de un Solicitudes de Registro y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$razon_social = $data['razon_social'];
		$rut_cc_ce_pasaporte = $data['rut_cc_ce_pasaporte'];
		$dv = $data['dv'];
		$email = $data['email'];
		$nombre_contacto = $data['nombre_contacto'];
		$cargo = $data['cargo'];
		$estado = $data['estado'];
		$query = "INSERT INTO solicitudes_registro( razon_social, rut_cc_ce_pasaporte, dv, email, nombre_contacto, cargo, estado) VALUES ( '$razon_social', '$rut_cc_ce_pasaporte', '$dv', '$email', '$nombre_contacto', '$cargo', '$estado')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un Solicitudes de Registro  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$razon_social = $data['razon_social'];
		$rut_cc_ce_pasaporte = $data['rut_cc_ce_pasaporte'];
		$dv = $data['dv'];
		$email = $data['email'];
		$nombre_contacto = $data['nombre_contacto'];
		$cargo = $data['cargo'];
		$estado = $data['estado'];
		$query = "UPDATE solicitudes_registro SET  razon_social = '$razon_social', rut_cc_ce_pasaporte = '$rut_cc_ce_pasaporte', dv = '$dv', email = '$email', nombre_contacto = '$nombre_contacto', cargo = '$cargo', estado = '$estado' WHERE id = '".$id."'";
		$res = $this->_conn->query($query);
	}
}