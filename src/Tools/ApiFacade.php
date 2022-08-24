<?php

namespace Itwmw\Http\Api\Tools;

use Throwable;
use Itwmw\Http\Api\ApiClient;
use Itwmw\Http\Api\Core\BaseConfig;
use Itwmw\Http\Api\Core\Exception\ApiException;

class ApiFacade
{
    protected static BaseConfig $config;

    /**
     * 写入配置信息
     * @param BaseConfig $config
     */
    public static function setConfig(BaseConfig $config): void
    {
        self::$config = $config;
    }

    public static function __callStatic($name, $arguments)
    {
        try {
            $client = ApiClient::instance(self::$config);
            return $client->$name(...$arguments);
        } catch (Throwable $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
