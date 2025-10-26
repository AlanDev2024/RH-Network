<?php
// Iniciar sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    echo json_encode(array('success' => false, 'message' => 'Acesso não autorizado.'));
    exit;
}

include 'config.php';

$database = new Database();
$db = $database->getConnection();
$empresa = new Empresa($db);

$empresa->id = isset($_GET['id']) ? $_GET['id'] : die();

if($empresa->readOne()) {
    $empresa_arr = array(
        "id" => $empresa->id,
        "nome_empresa" => $empresa->nome_empresa,
        "atuacao" => $empresa->atuacao,
        "email" => $empresa->email
    );
    
    echo json_encode(array('success' => true, 'empresa' => $empresa_arr));
} else {
    echo json_encode(array('success' => false, 'message' => 'Empresa não encontrada.'));
}
?>