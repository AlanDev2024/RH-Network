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

$response = array('success' => false, 'message' => '');

if($empresa->delete()) {
    $response['success'] = true;
    $response['message'] = 'Empresa excluída com sucesso!';
} else {
    $response['message'] = 'Erro ao excluir empresa.';
}

echo json_encode($response);
?>