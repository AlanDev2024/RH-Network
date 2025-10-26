<?php
// Script para criar usuário admin com senha correta
$senha = 'admin123';
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

echo "Senha: " . $senha . "<br>";
echo "Hash: " . $senha_hash . "<br>";

// SQL para inserir no banco
echo "<br>SQL para executar no phpMyAdmin:<br>";
echo "INSERT INTO usuarios (usuario, senha, nome) VALUES ('admin', '" . $senha_hash . "', 'Administrador');";
?>