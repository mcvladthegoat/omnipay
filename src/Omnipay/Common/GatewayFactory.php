<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\Common;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Client as HttpClient;
use Omnipay\Common\Exception\GatewayNotFoundException;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class GatewayFactory
{
    public static function create($type, ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        $type = Helper::getGatewayClassName($type);

        if (!class_exists($type)) {
            throw new GatewayNotFoundException("Class '$type' not found");
        }

        $gateway = new $type($httpClient, $httpRequest);

        return $gateway;
    }

    /**
     * Get a list of supported gateways, in friendly format (e.g. PayPal_Express)
     */
    public static function find($directory = null)
    {
        $result = array();

        // find all gateways in the Billing directory
        $directory = dirname(__DIR__);
        $it = new RecursiveDirectoryIterator($directory);
        foreach (new RecursiveIteratorIterator($it) as $file) {
            $filepath = $file->getPathName();
            if ('Gateway.php' === substr($filepath, -11)) {
                // determine class name
                $type = substr($filepath, 0, -11);
                $type = str_replace(array($directory, DIRECTORY_SEPARATOR), array('', '_'), $type);
                $type = trim($type, '_');
                $class = Helper::getGatewayClassName($type);

                // ensure class exists and is not abstract
                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    if (!$reflection->isAbstract() and
                        $reflection->implementsInterface('\\Omnipay\\Common\\GatewayInterface')) {
                        $result[] = $type;
                    }
                }
            }
        }

        return $result;
    }
}
