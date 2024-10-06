<?php

class Route
{
    private string $path;
    private string $method;
    private string $controller;
    private string $appName;

    /** @var array<string> */
    private array $params = [];

    public function __construct(string $path, string $method, string $controller, string $appName, array $params = [])
    {
        $this->path = $path;
        $this->method = $method;
        $this->controller = $controller;
        $this->appName = $appName;
        $this->params = $params;
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