<?php

namespace Itwmw\Http\Api\Core;

use Itwmw\Http\Api\Core\Interfaces\ApiGroupInterface;
use Itwmw\Http\Api\Core\Interfaces\MiddlewareInterface;

class BaseConfig
{
    /**
     * Web服务名称
     * @var string
     */
    public string $name = '';

    /**
     * 服务描述与之兼容的版本标识
     * @var string
     */
    public string $apiVersion = '';

    /**
     * 网络服务的基础URL
     * @var string
     */
    public string $baseUrl = '';

    /**
     * 网络服务描述
     * @var string
     */
    public string $description = '';

    /**
     * 有效的Api组完整命名空间，需要实现{@see ApiGroupInterface} 接口
     * @var array
     */
    public array $apiGroups = [];

    /**
     * 请求中间件完整命名空间,需要实现{@see MiddlewareInterface}
     * @var array
     */
    public array $middlewares = [];

    /**
     * 每次请求需要附带的协议头，每对协议头的格式必须为：[key => value],如：
     *```
     *public array $headers = [
     *    'X-Requested-With' => 'XMLHttpRequest'
     *]
     * ```
     * @var array
     */
    public array $headers = [];
}
