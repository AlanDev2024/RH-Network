<?php
include 'config.php';
verificarLogin();

// Processar logout
if(isset($_GET['logout'])) {
    logout();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RH Network</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Header com informações do usuário -->
        <div class="header">
            <h1>Gestão de contatos de RH Empresarial</h1>
            <div class="user-info">
                <span>👋 Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</span>
                <a href="?logout=true" class="btn-logout">Sair</a>
            </div>
        </div>

        <!-- Formulário de Cadastro -->
        <div class="form-container">
            <h2 style="margin-bottom: 20px; color: #000;">➕ Nova Empresa</h2>
            <form id="empresaForm" method="POST">
                <input type="hidden" id="empresaId" name="id">
                
                <div class="form-group">
                    <label for="nome_empresa">Nome da Empresa:</label>
                    <input type="text" id="nome_empresa" name="nome_empresa" placeholder="Digite o nome da empresa" required>
                </div>

                <div class="form-group">
                    <label for="atuacao">Atuação:</label>
                    <input type="text" id="atuacao" name="atuacao" placeholder="Digite a área de atuação" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="Digite o e-mail da empresa" required>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary" id="btnCadastrar">Cadastrar</button>
                    <button type="button" class="btn btn-warning hidden" id="btnCancelar">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- Mensagens -->
        <div id="message"></div>

        <!-- Lista de Empresas -->
        <div class="table-container">
            <h2 style="padding: 15px; background: #f8f9fa; margin: 0; border-bottom: 1px solid #dee2e6; color: #000;">📋 Empresas Cadastradas</h2>
            <table id="tabelaEmpresas">
                <thead>
                    <tr>
                        <th>🏢 Nome da Empresa</th>
                        <th>🎯 Atuação</th>
                        <th>📧 E-mail</th>
                        <th>⚙️ Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    if($db) {
                        $empresa = new Empresa($db);
                        $stmt = $empresa->read();
                        $num = $stmt->rowCount();

                        if($num > 0) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                extract($row);
                                echo "<tr id='empresa-{$id}'>";
                                echo "<td><strong>" . htmlspecialchars($nome_empresa) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($atuacao) . "</td>";
                                echo "<td><a href='mailto:" . htmlspecialchars($email) . "' style='color: #667eea; text-decoration: none;'>" . htmlspecialchars($email) . "</a></td>";
                                echo "<td class='actions'>";
                                echo "<button class='btn btn-enviar' onclick='enviarEmail(\"" . htmlspecialchars($email) . "\")'>Enviar E-mail</button>";
                                echo "<button class='btn btn-warning' onclick='editarEmpresa({$id})'>Alterar</button>";
                                echo "<button class='btn btn-danger' onclick='excluirEmpresa({$id})'>Excluir</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr>";
                            echo "<td colspan='4' class='empty-state'>";
                            echo "<div style='padding: 40px; text-align: center;'>";
                            echo "<h3 style='color: #6c757d; margin-bottom: 10px;'>Nenhuma empresa cadastrada</h3>";
                            echo "<p style='color: #6c757d;'>Use o formulário acima para cadastrar a primeira empresa.</p>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr>";
                        echo "<td colspan='4' style='text-align: center; color: #dc3545; padding: 20px;'>";
                        echo "⚠️ Erro ao conectar com o banco de dados. Verifique a configuração.";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>