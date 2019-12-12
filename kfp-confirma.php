<?php
/**
 * Plugin Name: KFP Confirma
 * Description: Confirma la dirección de correo de un usuario a la recepción de una URL
 * Author: KungFuPress
 * Author URI: https://kungfupress.com
 * Version: 0.1
 * 
 * @package kfp_confirma
 */

// Evita que se llame directamente a este fichero sin pasar por WordPress.
defined( 'ABSPATH' ) || die();

// Agrega action hook para procesar la petición vía URL de un usuario no logeado.
// La URL envíada sería algo como:
// https://mi-sitio.com/admin.post.php?action=kfp-confirma&token=LM3XXP5467
add_action( 'admin_post_nopriv_kfp-confirma', 'kfp_confirma_correo' );

/**
 * Comprueba que el token existe y en ese caso confirma el correo electrónico
 * poniendo la fecha actual en el campo confirma_correo
 * Se asume que existe la tabla (prefijo)_usuario con los campos mencionados
 * Se asume que el token es único
 *
 * @return void
 */
function kfp_confirma_correo() {
	global $wpdb;
	if ( ! isset( $_GET['token'] ) ) {
		$mensaje = "Faltan datos para confirmar el correo";
	}
	$token = sanitize_text_field( wp_unslash( $_GET['token'] ) );
	$id_usuario = $wpdb->get_var( 
		$wpdb->prepare( 
			"SELECT id from `{$wpdb->prefix}aspirante` WHERE token = '%s'", 
			array ($token) 
		)
	);
	if ($id_usuario > 0 ) {
		$tabla = $wpdb->prefix . 'aspirante';
		$datos = array( 'confirma_correo' => date( 'Y-m-d H:i:s' ) );
		$condicion = array('id' => $id_usuario);
		$wpdb->update( $tabla, $datos, $condicion );
		$mensaje = "Se ha confirmado su dirección de correo satisfactoriamente";
	} else {
		$mensaje = "Token incorrecto solicite nueva confirmación";
	}
	echo $mensaje;
}
