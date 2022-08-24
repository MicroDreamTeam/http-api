<?php

namespace Itwmw\Http\Api\Core;

use Error;
use GuzzleHttp\Command\ToArrayInterface;
use Itwmw\Http\Api\Builder\ParamDefinition;
use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Language;

/**
 * Api参数操作类
 *
 * @method ParamDescription array()         设置参数类型为array ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription object()        设置参数类型为object ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription string()        设置参数类型为string ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription boolean()       设置参数类型为boolean ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription integer()       设置参数类型为integer ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription number()        设置参数类型为number ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription numeric()       设置参数类型为numeric ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription null()          设置参数类型为null ，如果已指定类型，再次调用同类函数为添加一个类型
 * @method ParamDescription any()           设置参数类型为any ，如果已指定类型，再次调用同类函数为添加一个类型
 *
 * @method ParamDescription uri()           设置参数请求的位置为uri
 * @method ParamDescription query()         设置参数请求的位置为query
 * @method ParamDescription header()        设置参数请求的位置为header
 * @method ParamDescription body()          设置参数请求的位置为body
 * @method ParamDescription json()          设置参数请求的位置为json
 * @method ParamDescription xml()           设置参数请求的位置为xml
 * @method ParamDescription formParam()     设置参数请求的位置为formParam
 * @method ParamDescription multipart()     设置参数请求的位置为multipart
 */
class ParamDescription implements ToArrayInterface
{
    /**
     * 参数的唯一名称
     *
     * @var string
     */
    private string $name;

    /**
     * 字段类型，可设置多种类型
     *
     * 类型用于验证和确定一个参数的结构。你可以通过提供一个简单类型的数组来使用联合类型。如果其中一个联合类型与提供的值相匹配，那么这个值就是有效的。
     *
     * 类型有：string, number, integer, boolean, object, array, numeric, null, any
     * @var string|string[]
     */
    private array|string $type;

    /**
     * 是否必填
     * @var bool
     */
    private bool $required;

    /**
     * 默认值
     * @var mixed
     */
    private mixed $default;

    /**
     * 设置为 "true"，指定参数值不能从默认值中更改。
     * @var bool
     */
    private bool $static;

    /**
     * 参数的描述
     * @var string
     */
    private string $description;

    /**
     * 用于应用参数的请求的位置
     * 可以通过命令注册自定义位置，但默认值是 uri, query, header, body, json, xml, formParam, multipart
     * @var string
     */
    private string $location;

    /**
     * 指定被建模的数据是如何发送的
     * 例如，您可能希望在响应模型中包含某些头信息，这些头信息的标准化外壳是FooBar，但实际的头信息是x-foo-bar。在这种情况下，sentAs将被设置为x-foo-bar。
     * @var string
     */
    private string $sentAs;

    /**
     * 用于运行参数值的静态方法名称的数组。
     *
     * 数组中的每个值必须是一个字符串，包含静态方法的完整类路径，或者是一个复杂的过滤信息数组。
     * 您可以使用'::'后面的完整命名空间类名来指定类的静态方法（例如FooBar::baz()）。
     * 有些过滤器需要参数才能正确过滤一个值。对于复杂的过滤器，使用一个包含指向静态方法的'method'键的哈希，以及一个包含位置参数数组的'args'键来传递给该方法。
     * 参数可以包含在过滤一个值时被替换的关键字："@value"被替换为被验证的值，"@api"被替换为参数对象。
     * @var array
     */
    private array $filters;

    /**
     * 当type类型为array时，使用此参数放置子参数类型
     * @var ParamDescription|null
     */
    private ?ParamDescription $items;

    /**
     * 当type类型为object时，使用此参数放置子参数
     * @var ParamDescription[]
     */
    private array $properties;

    /**
     * 对参数进行验证
     * @var ParamValidator
     */
    private ParamValidator $paramValidator;

    /**
     * Params constructor.
     * @param string                $name       字段名称
     * @param string|string[]       $type       字段类型，可设置多种类型
     * @param bool                  $required   是否必填
     * @param string                $location   用于应用参数的请求的位置
     * @param mixed                 $default    默认值
     * @param ParamDescription|null $items      当type类型为array时，使用此参数放置子参数类型
     * @param ParamDescription[]    $properties 当type类型为object时，使用此参数放置子参数
     */
    public function __construct(
        string $name = '',
        string|array $type = '',
        bool $required = false,
        #[ExpectedValues(values: ['uri', 'query', 'statusCode', 'reasonPhrase', 'header', 'body', 'json', 'xml', 'formParam', 'multipart', 'responseBody'])]
        string $location = '',
        mixed $default = null,
        ?ParamDescription $items = null,
        array $properties = []
    ) {
        $this->name       = $name;
        $this->type       = $type;
        $this->required   = $required;
        $this->location   = $location;
        $this->default    = $default;
        $this->items      = $items;
        $this->properties = $properties;
    }

    /**
     * 设置过滤器
     * @param array $filters
     * @return $this
     */
    public function filters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * 指定参数值不能从默认值中更改
     * @param bool $static
     * @return $this
     */
    public function setStatic(bool $static = true): static
    {
        $this->static = $static;
        return $this;
    }

    /**
     * 设置参数描述
     * @param string $description
     * @return $this
     */
    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * 设置参数是否必填
     * @param bool $required
     * @return $this
     */
    public function required(bool $required = true): ParamDescription
    {
        $this->required = $required;
        return $this;
    }

    /**
     * 设置参数名称
     * @param string $name 参数名称
     * @return $this
     */
    public function name(string $name): ParamDescription
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 设置参数默认值
     * @param $defaultValue
     * @return $this
     */
    public function default($defaultValue): ParamDescription
    {
        $this->default = $defaultValue;
        return $this;
    }

    /**
     * 指定被建模的数据是如何发送的
     * @param string $sentAs
     * @return $this
     */
    public function sendAs(string $sentAs): ParamDescription
    {
        $this->sentAs = $sentAs;
        return $this;
    }

    /**
     * 添加对象成员
     * @param callable|string|ParamDescription $name       字段名称或参数对象，闭包会传递一个{@see ParamDefinition}对象
     * @param string|string[]                  $type       字段类型，可设置多种类型
     * @param bool                             $required   是否必填
     * @param string                           $location   用于应用参数的请求的位置
     * @param mixed|null                       $default    默认值
     * @param ParamDescription|null            $items      当type类型为array时，使用此参数放置子参数类型
     * @param ParamDescription[]               $properties 当type类型为object时，使用此参数放置子参数
     * @return $this
     */
    public function addProperties(
        ParamDescription|callable|string $name,
        array|string $type = '',
        bool $required = false,
        string $location = '',
        mixed $default = null,
        ?ParamDescription $items = null,
        array $properties = []
    ): ParamDescription {
        if ($name instanceof ParamDescription) {
            $this->properties[] = $name;
        } elseif (is_callable($name)) {
            $params = new ParamDefinition();
            call_user_func($name, $params);
            $this->properties = array_merge($this->properties, $params->getParams());
        } else {
            $param              = new static(...func_get_args());
            $this->properties[] = $param;
        }
        return $this;
    }

    /**
     * @param string|null $pattern   当类型是一个字符串时，你可以指定一个值必须匹配的regex模式
     * @param array|null  $enum      当类型是字符串时，你可以指定一个可接受值的列表。
     * @param int|null    $minItems  阵列中允许的最小项目数
     * @param int|null    $maxItems  一个数组中允许的最大项目数
     * @param int|null    $minLength 一个字符串的最小长度
     * @param int|null    $maxLength 一个字符串的最大长度
     * @param int|null    $minimum   一个整数的最小值
     * @param int|null    $maximum   一个整数的最大值
     * @param string|null $format    在序列化或取消序列化时用于哄骗一个值的正确格式的格式。你可以指定一个过滤器数组或一个格式，但不能同时指定。
     * 支持的值：date-time, date, time, timestamp, date-time-http, 和 boolean-string
     */
    public function validate(
        #[Language('RegExp')]
        ?string $pattern = null,
        ?array $enum = null,
        ?int $minItems = null,
        ?int $maxItems = null,
        ?int $minLength = null,
        ?int $maxLength = null,
        ?int $minimum = null,
        ?int $maximum = null,
        #[ExpectedValues(values: ['date-time', 'date', 'time', 'timestamp', 'date-time-http', 'boolean-string'])]
        ?string $format = null
    ): static {
        $this->paramValidator = new ParamValidator(...func_get_args());
        return $this;
    }

    /**
     * 设置数组成员类型
     * @param string|ParamDescription $name       字段名称或参数对象
     * @param string|string[]         $type       字段类型，可设置多种类型
     * @param bool                    $required   是否必填
     * @param string                  $location   用于应用参数的请求的位置
     * @param mixed|null              $default    默认值
     * @param ParamDescription|null   $items      当type类型为array时，使用此参数放置子参数类型
     * @param ParamDescription[]      $properties 当type类型为object时，使用此参数放置子参数
     * @return $this
     */
    public function setItems(
        ParamDescription|string $name,
        array|string $type = '',
        bool $required = false,
        string $location = '',
        mixed $default = null,
        ?ParamDescription $items = null,
        array $properties = []
    ): ParamDescription {
        if ($name instanceof ParamDescription) {
            $this->items = $name;
        } else {
            $param       = new static(...func_get_args());
            $this->items = $param;
        }
        return $this;
    }

    public function __call($name, $arguments)
    {
        $types     = ['array', 'object', 'string', 'boolean', 'integer', 'number', 'numeric', 'null', 'any'];
        $locations = ['uri', 'query', 'header', 'body', 'json', 'xml', 'formParam', 'multipart'];
        if (in_array($name, $types)) {
            if (empty($this->type)) {
                $this->type = $name;
            } else {
                if (!is_array($this->type)) {
                    $this->type = [$this->type];
                }
                $this->type[] = $name;
                $this->type   = array_unique($this->type);
            }
            return $this;
        }

        if (in_array($name, $locations)) {
            $this->location = $name;
            return $this;
        }

        throw new Error('Call to undefined method ' . basename(static::class) . '::' . $name . '()');
    }

    public function toArray(): array
    {
        $data = [
            'type'        => $this->type ?? null,
            'required'    => $this->required ?? null,
            'default'     => $this->default ?? null,
            'static'      => $this->static ?? null,
            'description' => $this->description ?? null,
            'location'    => $this->location ?? null,
            'sentAs'      => $this->sentAs ?? null,
            'filters'     => $this->filters ?? null,
        ];

        if (!empty($this->items)) {
            $items = $this->items->toArray();
            if (!empty($items)) {
                $items         = $items[0];
                $data['items'] = $items;
            }
        }
        
        if (!empty($this->properties)) {
            $properties = [];
            foreach ($this->properties as $property) {
                $properties = array_merge($properties, $property->toArray());
            }
            $data['properties'] = $properties;
        }

        if (isset($this->paramValidator)) {
            $data = array_merge($data, $this->paramValidator->toArray());
        }

        $paramsData = array_filter($data,fn($value) => !is_null($value));

        if (empty($this->name)) {
            return [$paramsData];
        }

        return [$this->name => $paramsData];
    }
}
