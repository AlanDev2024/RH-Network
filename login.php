<?php
// Iniciar sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se já estiver logado, redireciona para index
if(isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: index.php');
    exit;
}

// Credenciais padrão
$usuario_default = 'admin';
$senha_default = 'admin123';

$erro = '';

if($_POST) {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    // Verificação simples direta (fallback)
    if($usuario === $usuario_default && $senha === $senha_default) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario_default;
        $_SESSION['nome'] = 'Administrador';
        header('Location: index.php');
        exit;
    }
    
    // Tentar autenticar via banco de dados
    try {
        include 'config.php';
        $database = new Database();
        $db = $database->getConnection();
        
        if($db) {
            $query = "SELECT * FROM usuarios WHERE usuario = :usuario";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            
            if($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verificar a senha
                if(password_verify($senha, $user['senha'])) {
                    $_SESSION['logado'] = true;
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['nome'] = $user['nome'];
                    header('Location: index.php');
                    exit;
                }
            }
        }
    } catch(Exception $e) {
        // Ignorar erros do banco e usar fallback
    }
    
    $erro = "Usuário ou senha inválidos!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RH Network</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #e0e1e1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgb(204, 214, 246);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .logo p {
            color: #666;
            font-size: 1.1em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #000;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #1a035f;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .credentials {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }

        .credentials h3 {
            color: #060606ff;
            margin-bottom: 10px;
            font-size: 1em;
        }

        .credentials p {
            color: #0b0b0bff;
            font-size: 0.9em;
            margin: 5px 0;
        }

        .debug-info {
            margin-top: 15px;
            padding: 10px;
            background: #fff3cd;
            border-radius: 4px;
            font-size: 0.8em;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Gestão de contatos de RH Empresarial</h1>
            <p>Faça login para continuar</p>
        </div>

        <?php if(!empty($erro)): ?>
            <div class="alert">
                <?php echo $erro; ?>
                <div class="debug-info">
                    <strong>Debug:</strong> Verifique se o usuário e senha estão corretos.
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" value="admin" required autofocus>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" value="admin123" required>
            </div>

            <button type="submit" class="btn-login">Entrar no Sistema</button>
        </form>

        <?php if(isset($_POST['usuario'])): ?>
        <div class="debug-info">
            <strong>Debug Info:</strong><br>
            Usuário enviado: <?php echo htmlspecialchars($_POST['usuario']); ?><br>
            Senha enviada: <?php echo htmlspecialchars($_POST['senha']); ?><br>
            Sessão: <?php echo session_id(); ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Foco no campo de senha quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('senha').focus();
        });
    </script>
</body>
</html>