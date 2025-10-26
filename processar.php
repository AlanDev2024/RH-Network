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

$response = array('success' => false, 'message' => '');

if($_POST) {
    $empresa->nome_empresa = $_POST['nome_empresa'];
    $empresa->atuacao = $_POST['atuacao'];
    $empresa->email = $_POST['email'];
    
    if(isset($_POST['id']) && !empty($_POST['id'])) {
        // Atualizar empresa
        $empresa->id = $_POST['id'];
        if($empresa->update()) {
            $response['success'] = true;
            $response['message'] = 'Empresa atualizada com sucesso!';
        } else {
            $response['message'] = 'Erro ao atualizar empresa.';
        }
    } else {
        // Cadastrar nova empresa
        if($empresa->create()) {
            $response['success'] = true;
            $response['message'] = 'Empresa cadastrada com sucesso!';
        } else {
            $response['message'] = 'Erro ao cadastrar empresa. O e-mail já pode estar em uso.';
        }
    }
}

echo json_encode($response);
?>