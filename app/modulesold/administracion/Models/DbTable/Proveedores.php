<?php
/**
 * clase que genera la insercion y edicion  de proveedor en la base de datos
 */
class Administracion_Model_DbTable_Proveedores extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'proveedores';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'proveedor_id';

	/**
	 * insert recibe la informacion de un proveedor y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$proveedor_comprador = $data['proveedor_comprador'];
		$proveedor_numeroOr = $data['proveedor_numeroOr'];
		$proveedor_numeroLinea = $data['proveedor_numeroLinea'];
		$proveedor_codigoProducto = $data['proveedor_codigoProducto'];
		$proveedor_fechaAprobacionOR = $data['proveedor_fechaAprobacionOR'];
		$proveedor_fechaAprobacionSolicitud = $data['proveedor_fechaAprobacionSolicitud'];
		$proveedor_proveedor = $data['proveedor_proveedor'];
		$proveedor_nit = $data['proveedor_nit'];
		$proveedor_emailProveedor = $data['proveedor_emailProveedor'];
		$proveedor_creacionProveedor = $data['proveedor_creacionProveedor'];
		$proveedor_solicitudOferta = $data['proveedor_solicitudOferta'];
		$proveedor_visitaTecnica = $data['proveedor_visitaTecnica'];
		$proveedor_recepcionPreguntas = $data['proveedor_recepcionPreguntas'];
		$proveedor_envioRespuestas = $data['proveedor_envioRespuestas'];
		$proveedor_recepcionOferta = $data['proveedor_recepcionOferta'];
		$proveedor_evaluacionTecnica = $data['proveedor_evaluacionTecnica'];
		$proveedor_reevaluacion = $data['proveedor_reevaluacion'];
		$proveedor_adjudicacion = $data['proveedor_adjudicacion'];
		$proveedor_fechaContrato = $data['proveedor_fechaContrato'];
		$query = "INSERT INTO proveedores( proveedor_comprador, proveedor_numeroOr, proveedor_numeroLinea, proveedor_codigoProducto, proveedor_fechaAprobacionOR, proveedor_fechaAprobacionSolicitud, proveedor_proveedor, proveedor_nit, proveedor_emailProveedor, proveedor_creacionProveedor, proveedor_solicitudOferta, proveedor_visitaTecnica, proveedor_recepcionPreguntas, proveedor_envioRespuestas, proveedor_recepcionOferta, proveedor_evaluacionTecnica, proveedor_reevaluacion, proveedor_adjudicacion, proveedor_fechaContrato) VALUES ( '$proveedor_comprador', '$proveedor_numeroOr', '$proveedor_numeroLinea', '$proveedor_codigoProducto', '$proveedor_fechaAprobacionOR', '$proveedor_fechaAprobacionSolicitud', '$proveedor_proveedor', '$proveedor_nit', '$proveedor_emailProveedor', '$proveedor_creacionProveedor', '$proveedor_solicitudOferta', '$proveedor_visitaTecnica', '$proveedor_recepcionPreguntas', '$proveedor_envioRespuestas', '$proveedor_recepcionOferta', '$proveedor_evaluacionTecnica', '$proveedor_reevaluacion', '$proveedor_adjudicacion', '$proveedor_fechaContrato')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un proveedor  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$proveedor_comprador = $data['proveedor_comprador'];
		$proveedor_numeroOr = $data['proveedor_numeroOr'];
		$proveedor_numeroLinea = $data['proveedor_numeroLinea'];
		$proveedor_codigoProducto = $data['proveedor_codigoProducto'];
		$proveedor_fechaAprobacionOR = $data['proveedor_fechaAprobacionOR'];
		$proveedor_fechaAprobacionSolicitud = $data['proveedor_fechaAprobacionSolicitud'];
		$proveedor_proveedor = $data['proveedor_proveedor'];
		$proveedor_nit = $data['proveedor_nit'];
		$proveedor_emailProveedor = $data['proveedor_emailProveedor'];
		$proveedor_creacionProveedor = $data['proveedor_creacionProveedor'];
		$proveedor_solicitudOferta = $data['proveedor_solicitudOferta'];
		$proveedor_visitaTecnica = $data['proveedor_visitaTecnica'];
		$proveedor_recepcionPreguntas = $data['proveedor_recepcionPreguntas'];
		$proveedor_envioRespuestas = $data['proveedor_envioRespuestas'];
		$proveedor_recepcionOferta = $data['proveedor_recepcionOferta'];
		$proveedor_evaluacionTecnica = $data['proveedor_evaluacionTecnica'];
		$proveedor_reevaluacion = $data['proveedor_reevaluacion'];
		$proveedor_adjudicacion = $data['proveedor_adjudicacion'];
		$proveedor_fechaContrato = $data['proveedor_fechaContrato'];
		$query = "UPDATE proveedores SET  proveedor_comprador = '$proveedor_comprador', proveedor_numeroOr = '$proveedor_numeroOr', proveedor_numeroLinea = '$proveedor_numeroLinea', proveedor_codigoProducto = '$proveedor_codigoProducto', proveedor_fechaAprobacionOR = '$proveedor_fechaAprobacionOR', proveedor_fechaAprobacionSolicitud = '$proveedor_fechaAprobacionSolicitud', proveedor_proveedor = '$proveedor_proveedor', proveedor_nit = '$proveedor_nit', proveedor_emailProveedor = '$proveedor_emailProveedor', proveedor_creacionProveedor = '$proveedor_creacionProveedor', proveedor_solicitudOferta = '$proveedor_solicitudOferta', proveedor_visitaTecnica = '$proveedor_visitaTecnica', proveedor_recepcionPreguntas = '$proveedor_recepcionPreguntas', proveedor_envioRespuestas = '$proveedor_envioRespuestas', proveedor_recepcionOferta = '$proveedor_recepcionOferta', proveedor_evaluacionTecnica = '$proveedor_evaluacionTecnica', proveedor_reevaluacion = '$proveedor_reevaluacion', proveedor_adjudicacion = '$proveedor_adjudicacion', proveedor_fechaContrato = '$proveedor_fechaContrato' WHERE proveedor_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	public function insertIfNotExists($data)
	{
		$nit = $data['nit'];
		$fechaAprobacionOR = $data['fechaAprobacionOR'];

		// Verificar si ya existe
		$existing = $this->getList("proveedor_nit = '$nit' AND proveedor_fechaAprobacionOR = '$fechaAprobacionOR'", "")[0];
		if ($existing) {
			return $existing->proveedor_id;  // Ya existe, devolver ID
		}

		// Insertar si no existe
		return $this->insert($data);
	}

	/**
	 * Busca un proveedor por NIT
	 */
	public function getByNit($nit)
	{
		// return $this->getList("proveedor_nit = '$nit' AND proveedor_emailProveedor is not null AND proveedor_proveedor is not null AND proveedor_nit is not null AND proveedor_comprador is not null", "proveedor_fechaAprobacionOR DESC")[0];
		$escapedNit = $this->_conn->getConnection()->real_escape_string($nit);
		return $this->getList("proveedor_nit = '$escapedNit' AND proveedor_emailProveedor IS NOT NULL AND proveedor_proveedor IS NOT NULL AND proveedor_comprador IS NOT NULL", "proveedor_fechaAprobacionOR DESC")[0];

	}
}