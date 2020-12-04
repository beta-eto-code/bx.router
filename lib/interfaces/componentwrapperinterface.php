<?php


namespace BX\Router\Interfaces;


interface ComponentWrapperInterface extends ControllerInterface
{
    public function __construct(string $componentName, string $templateName = '', array $params = []);
}
