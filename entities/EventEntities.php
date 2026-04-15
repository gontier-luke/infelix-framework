<?php
namespace Entities;

class EventEntities
{
    /** @var int */
    private int $id;
    /** @var string */
    private string $name;
    /** @var string */
    private string $content;
    /** @var array<array<string, string>> */
    private array $images;
    /** @var array<string, string> */
    private array $parameters;
    /** @var array<string, string> */
    private array $directRouting;
    /** @var array<array<string, string>> */
    private array $parametersRouting;

    public function __construct(int $id, string $name, string $content, array $images, array $parameters, array $directRouting = [], array $parametersRouting = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->images = $images;
        $this->parameters = $parameters;
        $this->directRouting = $directRouting;
        $this->parametersRouting = $parametersRouting;
    }
}