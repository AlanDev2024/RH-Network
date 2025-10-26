<?php
// Iniciar sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Database {
    private $host = "localhost";
    private $db_name = "cadastro_empresas";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Silencioso - o sistema tem fallback
            error_log("Erro de conexão: " . $exception->getMessage());
        }
        return $this->conn;
    }
}

class Empresa {
    private $conn;
    private $table_name = "empresas";

    public $id;
    public $nome_empresa;
    public $atuacao;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nome_empresa=:nome_empresa, atuacao=:atuacao, email=:email";
        $stmt = $this->conn->prepare($query);

        $this->nome_empresa = htmlspecialchars(strip_tags($this->nome_empresa));
        $this->atuacao = htmlspecialchars(strip_tags($this->atuacao));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":nome_empresa", $this->nome_empresa);
        $stmt->bindParam(":atuacao", $this->atuacao);
        $stmt->bindParam(":email", $this->email);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY data_cadastro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nome_empresa=:nome_empresa, atuacao=:atuacao, email=:email WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->nome_empresa = htmlspecialchars(strip_tags($this->nome_empresa));
        $this->atuacao = htmlspecialchars(strip_tags($this->atuacao));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome_empresa", $this->nome_empresa);
        $stmt->bindParam(":atuacao", $this->atuacao);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nome_empresa = $row['nome_empresa'];
            $this->atuacao = $row['atuacao'];
            $this->email = $row['email'];
            return true;
        }
        return false;
    }
}

// Função para verificar se o usuário está logado
function verificarLogin() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: login.php');
        exit;
    }
}

// Função para logout
function logout() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Limpar todas as variáveis de sessão
    $_SESSION = array();
    
    // Destruir a sessão
    session_destroy();
    
    header('Location: login.php');
    exit;
}
?>