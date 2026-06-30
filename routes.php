<?php

require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';
require_once __DIR__ . '/app/Controllers/RelatoriosController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

/*
 * Bloco de autenticacao.
 * As actions de login/entrar sao publicas; dashboard exige sessao.
 */
if ($controller === 'auth') {

    $authController = new AuthController();

    switch ($action) {
        case 'login':
            $authController->exibirLogin();
            break;
        case 'entrar':
            $authController->entrar();
            break;
        case 'dashboard':
            exigirAutenticacao();
            $authController->dashboard();
            break;
        case 'logout':
            $authController->logout();
            break;

        default:
            http_response_code(404);
            echo 'Ação de autenticação não encontrada';
    }
    exit;
}

/*
 * A partir daqui todas as rotas exigem usuario autenticado.
 *
 * Paginas visuais  -> controller=frontend
 * Operacoes de dados -> controller=pessoas | tipos | atendimentos | dashboard
 */
exigirAutenticacao();

switch ($controller) {
    case 'frontend':
        $obj = new FrontendController();
        break;
    case 'dashboard':
        $obj = new DashboardController();
        break;
    case 'usuarios':
        $obj = new UsuariosController();
        break;
    case 'pessoas':
        $obj = new PessoasController();
        break;
    case 'tipos':
        $obj = new TiposAtendimentosController();
        break;
    case 'atendimentos':
        $obj = new AtendimentosController();
        break;
    case 'relatorios':
        $obj = new RelatoriosController();
        break;
    default:
        http_response_code(404);
        exit('Controller não encontrado');
}

if (!method_exists($obj, $action)) {
    http_response_code(404);
    exit('Ação não encontrada');
}

$obj->$action();
