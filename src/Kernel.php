<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use function dirname;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        if ($this->environment == 'github') {
            return './cache';
        }
        return '/var/cache/api';
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        if ($this->environment == 'github') {
            return './log';
        }
        return '/var/log/api';
    }

    /**
     * @param ContainerConfigurator $container
     * @return void
     */
    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_' . $this->environment . '.yaml');
            return;
        }

        $path = dirname(__DIR__) . '/config/services.php';
        if (is_file($path)) {
            (require $path)($container->withPath($path), $this);
        }
    }

    /**
     * @param RoutingConfigurator $routes
     * @return void
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
            return;
        }

        $path = dirname(__DIR__) . '/config/routes.php';
        if (is_file($path)) {
            (require $path)($routes->withPath($path), $this);
        }
    }
}
