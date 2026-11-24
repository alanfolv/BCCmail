<?php
require_once __DIR__ . '/../dao/EmailDAO.php';
require_once __DIR__ . '/../dao/ChatDAO.php';
require_once __DIR__ . '/../models/Email.php';
require_once __DIR__ . '/../models/Chats.php';

require_once __DIR__ . '/../core/Database.php';
class PagesController {
    private $connectionInstance;
    private $pdo;

    public function __construct() {
        $this->connectionInstance = new Database();
        $this->pdo = $this->connectionInstance->getConnection();

    }
    public function init() {

    if (empty($_SESSION['user_email'])) {
        header('Location: /login');
        exit();
    }

    $database = new Database();
    $pdo = $database->getConnection();

    $emailDAO = new EmailDAO($pdo);

    $emails = $emailDAO->findInbox($_SESSION['user_email']);

    require __DIR__ . '/../views/index.html';
    }
    public function enviaremail(){
        $to = $_POST["destinatario"];
        $subject = $_POST["assunto"];
        $message = $_POST["mensagem"];
        $from = $_SESSION['user_email'];
        $bcc = $_POST["destinatario"];
        $cc = $_POST["destinatario"];
        $from_id  = $_SESSION['user_id'];

        $chat = new Chats(null, date('Y-m-d H:i:s'));
        $chatsDAO = new ChatDAO($this->pdo);
        $chatId = $chatsDAO->create($chat);

        $email = new Email(null, $from_id, $to, $subject, $message, date('Y-m-d H:i:s'),false,$chatId,false);
        $emailDAO = new EmailDAO($this->pdo);
        $emailDAO->send($email);
        
    }
    public function importantes(){
        
        $caminho = __DIR__ . "/../views/importantes.html";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;
    }
    public function enviados(){
        
        $caminho = __DIR__ . "/../views/enviados.html";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;
    }
    
    public function lixeira(){
        
        $caminho = __DIR__ . "/../views/lixeira.html";
    
        // Teste de diagnóstico:
        if (!file_exists($caminho)) {
        throw new Exception("Erro: O PHP não encontrou o arquivo no caminho: " . $caminho);
        }
        include $caminho;
    }
   public function listarEmailsJson() {

    header('Content-Type: application/json; charset=utf-8');

    // Verificar autenticação
    if (empty($_SESSION['user_email'])) {

        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Usuário não autenticado.'
        ]);

        exit;
    }

    try {

        // Conexão
        $database = new Database();
        $pdo = $database->getConnection();

        // DAO
        $emailDAO = new EmailDAO($pdo);

        // Buscar emails do usuário
        $emails = $emailDAO->findInbox(
            $_SESSION['user_email']
        );

        // Transformar objetos em arrays
        $resultado = [];

        foreach ($emails as $email) {

            $resultado[] = [
                'id' => $email->getId(),
                'from' => $email->getFrom(),
                'to' => $email->getTo(),
                'subject' => $email->getSubject(),
                'body' => $email->getBody(),
                'date' => $email->getDate(),
                'isRead' => (bool) $email->getIsRead(),
                'chatId' => $email->getChatId()
            ];

        }

        echo json_encode([
            'success' => true,
            'emails' => $resultado
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        exit;

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao consultar os emails.'
        ]);

        exit;
    }
}
       
}