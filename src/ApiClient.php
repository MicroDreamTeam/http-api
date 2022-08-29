<?php

namespace Itwmw\Http\Api;

use Closure;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Serializer;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Itwmw\Http\Api\Core\BaseConfig;
use Itwmw\Http\Api\Core\Exception\ApiException;
use Itwmw\Http\Api\Core\Interfaces\ApiGroupInterface;
use Itwmw\Http\Api\Core\Interfaces\MiddlewareInterface;

class ApiClient extends GuzzleClient
{
    protected HttpClient $httpClient;

    protected Description $description;

    protected static object $instance;

    protected BaseConfig $config;

    public static function instance(
        BaseConfig $config,
        callable $commandToRequestTransformer = null,
        callable $responseToResultTransformer = null
    ): ApiClient {
        if (!isset(self::$instance)) {
            self::$instance = new static($config,$commandToRequestTransformer,$responseToResultTransformer);
        }
        return self::$instance;
    }

    public function __construct(
        BaseConfig $config,
        callable $commandToRequestTransformer = null,
        callable $responseToResultTransformer = null
    ) {
        $this->config = $config;

        $this->httpClient = new HttpClient([
            'base_uri' => $this->config->baseUrl,
            'handler'  => $this->getMiddleware(),
        ]);

        $this->description = new Description([
            'name'        => $this->config->name,
            'apiVersion'  => $this->config->apiVersion,
            'description' => $this->config->description,
            'operations'  => $this->getOperations()
        ]);

        if (is_null($commandToRequestTransformer)) {
            $commandToRequestTransformer = [$this, 'commandToRequestTransformer'];
        }

        if (is_null($responseToResultTransformer)) {
            $responseToResultTransformer = [$this, 'responseToResultTransformer'];
        }

        parent::__construct(
            $this->httpClient,
            $this->description,
            $commandToRequestTransformer,
            $responseToResultTransformer
        );
    }

    private function getOperations(): array
    {
        $apis = [];
        foreach ($this->config->apiGroups as $apiGroup) {
            if (!is_subclass_of($apiGroup, ApiGroupInterface::class)) {
                throw new ApiException('Not a valid Api Group');
            }
            $apis = array_merge($apis, (new $apiGroup())->getAllApi());
        }
        return $apis;
    }

    public function getMiddleware(): HandlerStack
    {
        $handler = HandlerStack::create();
        foreach ($this->config->middlewares as $middleware) {
            if (!is_subclass_of($middleware, MiddlewareInterface::class)) {
                throw new ApiException('Not a valid middleware');
            }
            $handler->push($this->handler($middleware));
        }
        return $handler;
    }

    public function commandToRequestTransformer(CommandInterface $command): RequestInterface
    {
        $serializer = new Serializer($this->description);
        $request    = $serializer($command);
        foreach ($this->config->headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }
        return $request;
    }

    public function responseToResultTransformer(ResponseInterface $response, RequestInterface $request, CommandInterface $command): string
    {
        return $response->getBody()->getContents();
    }

    private function handler(string $class): Closure
    {
        return function (callable $handler) use ($class) {
            return new $class($handler);
        };
    }
}
