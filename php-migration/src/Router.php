<?php
/**
 * Router simple pour l'application PHP
 */

class Router {
    private array $routes = [];
    private $notFoundHandler = null;
    
    public function addRoute(string $method, string $path, string $handler): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->convertPathToPattern($path)
        ];
    }
    
    public function setNotFoundHandler(string $handler): void {
        $this->notFoundHandler = $handler;
    }
    
    public function dispatch(string $method, string $uri): void {
        // Nettoyer l'URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        $method = strtoupper($method);
        
        // Chercher une route correspondante
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Retirer le match complet
                $this->callHandler($route['handler'], $matches);
                return;
            }
        }
        
        // Aucune route trouvée
        if ($this->notFoundHandler) {
            $this->callHandler($this->notFoundHandler, []);
        } else {
            http_response_code(404);
            echo "404 - Page non trouvée";
        }
    }
    
    private function convertPathToPattern(string $path): string {
        // Convertir :param en groupe de capture
        $pattern = preg_replace('/\:([a-zA-Z0-9_]+)/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    private function callHandler(string $handler, array $params): void {
        [$controllerName, $method] = explode('@', $handler);
        
        // Gérer les namespaces
        if (strpos($controllerName, '\\') === false && strpos($controllerName, 'Api\\') !== 0) {
            // Contrôleur normal
            $controllerClass = $controllerName;
        } else {
            // Contrôleur API ou avec namespace
            $controllerClass = $controllerName;
        }
        
        if (!class_exists($controllerClass)) {
            throw new Exception("Contrôleur introuvable: {$controllerClass}");
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            throw new Exception("Méthode introuvable: {$controllerClass}::{$method}");
        }
        
        // Appeler la méthode avec les paramètres
        call_user_func_array([$controller, $method], $params);
    }
}
?>