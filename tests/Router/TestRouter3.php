<?php

namespace Joindin\Api\Test\Router;

use BadMethodCallException;
use Joindin\Api\Request;
use Joindin\Api\Router\BaseRouter;
use Joindin\Api\Router\Route;

class TestRouter3 extends BaseRouter
{


    /**
     * {@inheritdoc}
     */
    public function dispatch(Route $route, Request $request, $db)
    {
        throw new BadMethodCallException('Method not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute(Request $request)
    {
        throw new BadMethodCallException('Method not implemented');
    }

    public function route(Request $request, $db)
    {
    }
}
