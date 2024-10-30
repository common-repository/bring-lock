<?php


// FUNCTION LOCK PAINEL
add_action( 'init', 'bring_lock_loja' );
function bring_lock_loja() {
	//Processo de verificação se existe as KEYS
   add_action( 'rest_api_init', 'bring_endpoint_lock' );
   function bring_endpoint_lock() {
    register_rest_route( 'lock-woo', '/lock/(?P<chave>.+)', array(
        'methods' => 'GET',
        'callback' => 'bring_callback_lock',
		'permission_callback' => '__return_true',
    ));
}
   function bring_callback_lock($request){
	   $key = sanitize_key($request['chave']);
		if (metadata_exists('term', 1, 'bring_key')){
			if (strtoupper(get_term_meta(1,'bring_key',true)) == strtoupper($key)){
				update_term_meta( 1, 'bring_status', 'true' );
				return esc_html("Painel Bloqueado");
			}else{
			return esc_html("Painel nao bloqueado, chave incorreta");
			}
		}else{
			// se não existe, criando uma nova META
		add_term_meta(1,'bring_key','chaveteste'); //string KEY
		add_term_meta(1,'bring_status','false'); // Key Status Lock
			return esc_html("Chave meta criada");
		}
   }
}

// FUNCTION UNLOCK PAINEL
add_action( 'init', 'bring_unlock_loja' );
function bring_unlock_loja() {
	//Processo de verificação se existe as KEYS
   add_action( 'rest_api_init', 'bring_endpoint_unlock' );
   function bring_endpoint_unlock() {
    register_rest_route( 'lock-woo', '/unlock/(?P<chave>.+)', array(
        'methods' => 'GET',
        'callback' => 'bring_callback_unlock',
		'permission_callback' => '__return_true',
    ));
}
   function bring_callback_unlock($request){
	   $key = sanitize_key($request['chave']);
		if (metadata_exists('term', 1, 'bring_key')){
			if (strtoupper(get_term_meta(1,'bring_key',true)) == strtoupper($key)){
				update_term_meta( 1, 'bring_status', 'false' );
				return esc_html("Painel Desbloqueado");
			}else{
			return esc_html("Painel nao desbloqueado, chave incorreta");
			}
		}else{
			// se não existe, criando uma nova META
		add_term_meta(1,'bring_key','chaveteste'); //string KEY
		add_term_meta(1,'bring_status','false'); // Key Status Lock
			return esc_html("Chave meta criada");
		}
   }
}

// Quando estiver logado no painel administrativo, Verificar se esta bloqueado, fazer logout e redirecionar de volta
add_action('init','bring_lock_check_lock');
function bring_lock_check_lock(){
	if (is_admin() && str_contains(wp_get_referer(), wp_login_url()) ){
		if (metadata_exists('term', 1, 'bring_key')){
			if (get_term_meta(1,'bring_status',true) == "true"){
				// Vai fechar a sessão e redirecionar para o painel de login com um parametro GET NA URL
   					wp_clear_auth_cookie(); //clears cookies regarding WP Auth
					header('Location: '.wp_login_url().'?lock=true'); // REDIRECIONAMENTO com parametro GET lock=true
			}else{
			// NAO EXECUTA NADA
			}
		}else{
			// se não existe, criando uma nova META
		add_term_meta(1,'bring_key','chaveteste'); //string KEY
		add_term_meta(1,'bring_status','false'); // Key Status Lock
			return "Chave meta criada";
		}
	}
}

// Verificação se no Painel Administrativo contem o parametro GET lock=true, se sim exibir alerta
add_action('init','bring_lock_login_block_msg');
function bring_lock_login_block_msg(){
	if (str_contains(wp_login_url(), $_SERVER['SERVER_NAME'] ) ){
		$query = sanitize_text_field($_SERVER['QUERY_STRING']);
		if ($query=="lock=true"){
		add_action( 'login_footer', 'bring_lock_start_msg_lock' );	
		}
		
	}
}
function bring_lock_start_msg_lock(){
	echo "<script>painel_block();</script>";
	
}



?>