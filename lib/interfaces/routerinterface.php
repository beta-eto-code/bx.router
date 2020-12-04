<?php


namespace BX\Router\Interfaces;

interface RouterInterface
{
    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function get(string $uri, ControllerInterface $controller): RouteContextInterface;

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function post(string $uri, ControllerInterface $controller): RouteContextInterface;

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function put(string $uri, ControllerInterface $controller): RouteContextInterface;

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function delete(string $uri, ControllerInterface $controller): RouteContextInterface;

    /**
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function default(ControllerInterface $controller): RouteContextInterface;
}
