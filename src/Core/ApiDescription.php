<?php

namespace Itwmw\Http\Api\Core;

use Exception;
use GuzzleHttp\Command\ToArrayInterface;
use Itwmw\Http\Api\Builder\ParamDefinition;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * Api操作
 */
class ApiDescription implements ToArrayInterface
{
    /**
     * 业务内容的简短概述
     * @var string
     */
    public string $summary;

    /**
     * 对该操作的较长描述
     * @var string
     */
    public string $notes;

    /**
     * 关于该操作提供更多信息的参考URL
     * @var string
     */
    public string $documentationUrl;

    /**
     * 用来处理响应的模型名称
     * @var string
     */
    public string $responseModel;

    /**
     * 是否为废弃API
     * @var bool
     */
    public bool $deprecated;

    /**
     * 通过名称从另一个操作扩展。父操作必须在子操作之前定义。
     * @var string
     */
    public string $extends;
    
    /**
     * 任何与操作相关的数据
     * @var array
     */
    public array $data = [];

    /**
     * 提供给未明确定义的操作的任何参数的验证和序列化规则。
     * @var ParamDescription
     */
    public ParamDescription $additionalParameters;

    /**
     * @param string $name 接口名称
     * @param string $httpMethod 操作时使用的HTTP方法,如GET、POST、PUT、DELETE、PATCH等
     * @param string $uri URI模板，可以创建一个相对或绝对的URL
     * @param array  $parameters Api 参数
     * @param array{code:int,reason:string,class:Exception} $errorResponses 执行该命令时可能发生的错误，数组中的每一项都是一个对象可以包含
     * - code（错误的HTTP响应状态代码）
     * - reason（错误的原因短语或描述）
     * - class（当遇到这个错误时将会引发的异常类)
     */
    public function __construct(
        public string $name,
        #[ExpectedValues(values: ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'])]
        public string $httpMethod,
        public string $uri,
        public array $parameters = [],
        public array $errorResponses = []
    ) {
    }

    /**
     * 添加参数
     *
     * @param callable $handler 闭包会传递一个{@see ParamDefinition}对象，
     * @return $this
     */
    public function addParams(callable $handler): ApiDescription
    {
        $params = new ParamDefinition();
        call_user_func($handler, $params);
        $this->parameters = array_merge($this->parameters, $params->getParams());
        return $this;
    }

    public function toArray(): array
    {
        $apiData = [
            'httpMethod'       => $this->httpMethod,
            'uri'              => $this->uri,
            'responseModel'    => $this->responseModel,
            'notes'            => $this->notes,
            'summary'          => $this->summary,
            'documentationUrl' => $this->documentationUrl,
            'deprecated'       => $this->deprecated,
            'data'             => $this->data,
            'errorResponses'   => $this->errorResponses
        ];

        if (!empty($this->parameters)) {
            $parametersArray = [];
            foreach ($this->parameters as $parameter) {
                $parametersArray = array_merge($parametersArray, $parameter->toArray());
            }
            $apiData['parameters'] = $parametersArray;
        }

        if (!empty($this->additionalParameters)) {
            $additionalParameters            = $this->additionalParameters->toArray();
            $apiData['additionalParameters'] = $additionalParameters;
        }
        
        $apiData = array_filter($apiData);
        return [
            $this->name => $apiData
        ];
    }
}
