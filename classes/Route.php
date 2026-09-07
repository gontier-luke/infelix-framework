<?php

namespace Classes;

class Route
{
    public string $path;
    public string $method;
    public string $controller;
    public string $appName;

    /** @var array<string> */
    private array $params = [];

    /** @var bool */
    public bool $isAdminRoute = false;
    
    /** @var bool */
    public bool $isActive = true;

    public function __construct(string $path, string $method, string $controller, string $appName, array $params = [], ?bool $isActive = true)
    {
        $this->path = $path;
        $this->method = $method;
        $this->controller = $controller;
        $this->appName = $appName;
        $this->params = $params;
        $pathParts = explode('/', $path);
        $this->isAdminRoute = isset($pathParts[1]) && $pathParts[1] === 'admin';
        $this->isActive = $isActive;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAppName(): string
    {
        return $this->appName;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'method' => $this->method,
            'controller' => $this->controller,
            'appName' => $this->appName,
            'params' => $this->params
        ];
    }

}