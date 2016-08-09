<?php
// DIC configuration

$container = $app->getContainer();

// Twig
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];

    return new \Slim\Views\PhpRenderer($settings['template_path']);
};

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// 404
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $data = [
            'result' => 'error',
            'data' => [
              'message' => 'Cette ressource n\'existe pas'
            ]
        ];

        return $c['response']->withJson($data, 404);
    };
};

// 405
$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        $data = [
            'result' => 'error',
            'data' => [
              'message' => 'Méthode non autorisée, elle doit être l\'une des suivantes : '.implode(', ', $methods)
            ]
        ];

        return $c['response']->withHeader('Allow', implode(', ', $methods))->withJson($data, 405);
    };
};

// Errors
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $data = [
            'result' => 'error',
            'data' => [
              'message' => $exception->getMessage()
            ]
        ];
        
        $status_code = $exception->getCode() == 0 ? 500 : $exception->getCode();
      
        return $c['response']->withJson($data, $status_code);
    };
};

// Kanboard JSON-RPC client
$container['kanboardapi'] = function ($c) {
    $kanboard_settings = $c->get('settings')['kanboard'];
    
    $client = new JsonRPC\Client($kanboard_settings['endpoint']);
    $client->authentication('jsonrpc', $kanboard_settings['token']);
    
    return $client;
};
