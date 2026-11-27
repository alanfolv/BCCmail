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

    error_log("========== DEBUG SESSAO ==========");
    error_log("SESSION ID: " . session_id());
    error_log("SESSION DATA: " . print_r($_SESSION, true));
    error_log("=================================");

    if (empty($_SESSION['user_email'])) {
        header('Location: /login');
        exit();
    }

    $emailDAO = new EmailDAO($this->pdo);

    $emails = $emailDAO->findInbox($_SESSION['user_email']);

    require __DIR__ . '/../views/index.html';
}
    public function enviaremail()
    {
        // Verificar autenticação
        if (empty($_SESSION['user_email'])) {
            header('Location: /login');
            exit;
        }

        // Aceitar somente requisições POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método não permitido.');
        }

        // Dados do formulário
        $from = trim($_SESSION['user_email']);
        $to = trim($_POST['destinatario'] ?? '');
        $subject = trim($_POST['assunto'] ?? '');
        $message = trim($_POST['mensagem'] ?? '');

        // Validar campos
        if (empty($to) || empty($subject) || empty($message)) {
            http_response_code(400);
            exit('Preencha todos os campos obrigatórios.');
        }

        try {

            // Verificar se remetente e destinatário existem
            $sql = "SELECT email FROM users WHERE email IN (:from_email, :to_email)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':from_email' => $from,
                ':to_email' => $to
            ]);

            $usuariosEncontrados = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (
                !in_array($from, $usuariosEncontrados, true) ||
                !in_array($to, $usuariosEncontrados, true)
            ) {
                http_response_code(422);
                exit('Remetente ou destinatário não cadastrado.');
            }

            // Iniciar transação
            $this->pdo->beginTransaction();

            // Criar conversa
            $chat = new Chats(
                null,
                date('Y-m-d H:i:s')
            );

            $chatsDAO = new ChatDAO($this->pdo);

            $chatId = $chatsDAO->create($chat);

            // Criar objeto Email
            // A ordem dos parâmetros segue o construtor utilizado no hydrate().

            $email = new Email(
                null,
                $from,
                $to,
                $subject,
                $message,
                date('Y-m-d H:i:s'),
                false,
                $chatId
            );

            // Salvar e-mail
            $emailDAO = new EmailDAO($this->pdo);

            $emailDAO->send($email);

            // Confirmar operação
            $this->pdo->commit();

            header('Location:/index');
            exit;

        } catch (Throwable $e) {

            // Desfazer operações em caso de erro
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log('Erro ao enviar e-mail: ' . $e->getMessage());

            http_response_code(500);

            echo 'Não foi possível enviar o e-mail.';
        }
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
// ==========================================
// LISTAR EMAILS ENVIADOS (JSON)
// ==========================================

public function listarEnviadosJson()
{
    header('Content-Type: application/json; charset=utf-8');

    if (empty($_SESSION['user_email'])) {
        $this->responderJson([
            'success' => false,
            'message' => 'Usuário não autenticado.'
        ], 401);
    }

    try {

        $emailDAO = new EmailDAO($this->pdo);

        // Buscar emails enviados pelo usuário
        $emails = $emailDAO->findSent($_SESSION['user_email']);

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

        $this->responderJson([
            'success' => true,
            'total' => count($resultado),
            'emails' => $resultado
        ]);

    } catch (Throwable $e) {

        error_log('Erro ao listar enviados: ' . $e->getMessage());

        $this->responderJson([
            'success' => false,
            'message' => 'Erro ao consultar emails enviados.'
        ], 500);
    }
}


// ==========================================
// LISTAR EMAILS FAVORITOS (JSON)
// ==========================================

public function listarFavoritosJson()
{
    header('Content-Type: application/json; charset=utf-8');

    if (empty($_SESSION['user_email']) || empty($_SESSION['user_id'])) {
        $this->responderJson([
            'success' => false,
            'message' => 'Usuário não autenticado.'
        ], 401);
    }

    try {

        $emailDAO = new EmailDAO($this->pdo);

        $userId = (int) $_SESSION['user_id'];

        // Buscar favoritos do usuário
        $emails = $emailDAO->findFavorites($userId);

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
                'chatId' => $email->getChatId(),
                'isFavorite' => true
            ];

        }

        $this->responderJson([
            'success' => true,
            'total' => count($resultado),
            'emails' => $resultado
        ]);

    } catch (Throwable $e) {

        error_log('Erro ao listar favoritos: ' . $e->getMessage());

        $this->responderJson([
            'success' => false,
            'message' => 'Erro ao consultar emails favoritos.'
        ], 500);
    }
}


// ==========================================
// ADICIONAR EMAIL AOS FAVORITOS (JSON)
// ==========================================

public function adicionarFavoritoJson()
{
    header('Content-Type: application/json; charset=utf-8');

    if (empty($_SESSION['user_email']) || empty($_SESSION['user_id'])) {
        $this->responderJson([
            'success' => false,
            'message' => 'Usuário não autenticado.'
        ], 401);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->responderJson([
            'success' => false,
            'message' => 'Método não permitido.'
        ], 405);
    }

    try {

        $dados = $this->dadosRequisicao();

        $emailId = filter_var(
            $dados['email_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$emailId || $emailId <= 0) {
            $this->responderJson([
                'success' => false,
                'message' => 'ID do email inválido.'
            ], 400);
        }

        $emailDAO = new EmailDAO($this->pdo);

        $userId = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'];

        // Verificar se já é favorito
        $jaFavorito = $emailDAO->isFavorite($userId, $emailId);

        // Adicionar aos favoritos
        $adicionado = $emailDAO->addFavorite(
            $userId,
            $userEmail,
            $emailId
        );

        // Se não adicionou e não era favorito,
        // o email não existe ou não pertence ao usuário.
        if (!$adicionado && !$jaFavorito) {
            $this->responderJson([
                'success' => false,
                'message' => 'Email inexistente ou sem permissão para favoritar.'
            ], 404);
        }

        $this->responderJson([
            'success' => true,
            'message' => $jaFavorito
                ? 'Este email já está nos favoritos.'
                : 'Email adicionado aos favoritos.',
            'email_id' => $emailId,
            'isFavorite' => true
        ]);

    } catch (Throwable $e) {

        error_log('Erro ao adicionar favorito: ' . $e->getMessage());

        $this->responderJson([
            'success' => false,
            'message' => 'Não foi possível adicionar o email aos favoritos.'
        ], 500);
    }
}


// ==========================================
// REMOVER EMAIL DOS FAVORITOS (JSON)
// ==========================================

public function removerFavoritoJson()
{
    header('Content-Type: application/json; charset=utf-8');

    if (empty($_SESSION['user_email']) || empty($_SESSION['user_id'])) {
        $this->responderJson([
            'success' => false,
            'message' => 'Usuário não autenticado.'
        ], 401);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->responderJson([
            'success' => false,
            'message' => 'Método não permitido.'
        ], 405);
    }

    try {

        $dados = $this->dadosRequisicao();

        $emailId = filter_var(
            $dados['email_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$emailId || $emailId <= 0) {
            $this->responderJson([
                'success' => false,
                'message' => 'ID do email inválido.'
            ], 400);
        }

        $emailDAO = new EmailDAO($this->pdo);

        $userId = (int) $_SESSION['user_id'];

        // Remover dos favoritos
        $emailDAO->removeFavorite($userId, $emailId);

        $this->responderJson([
            'success' => true,
            'message' => 'Email removido dos favoritos.',
            'email_id' => $emailId,
            'isFavorite' => false
        ]);

    } catch (Throwable $e) {

        error_log('Erro ao remover favorito: ' . $e->getMessage());

        $this->responderJson([
            'success' => false,
            'message' => 'Não foi possível remover o email dos favoritos.'
        ], 500);
    }
}


// ==========================================
// AUXILIAR: RESPONDER JSON
// ==========================================

private function responderJson(array $dados, int $status = 200)
{
    http_response_code($status);

    echo json_encode(
        $dados,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}


// ==========================================
// AUXILIAR: LER DADOS DA REQUISIÇÃO
// ==========================================

private function dadosRequisicao(): array
{
    $dados = $_POST;

    $conteudo = file_get_contents('php://input');

    if (!empty($conteudo)) {

        $json = json_decode($conteudo, true);

        if (is_array($json)) {
            $dados = array_merge($dados, $json);
        }
    }

    return $dados;
}
       
}