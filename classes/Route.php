<?php

class Route
{
    public string $path;
    public string $method;
    public string $controller;
    public string $appName;

    public function __construct(string $path, string $method, string $controller, string $appName)
    {
        $this->path = $path;
        $this->method = $method;
        $this->controller = $controller;
        $this->appName = $appName;
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

}