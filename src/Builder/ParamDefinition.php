<?php

namespace Itwmw\Http\Api\Builder;

use Error;
use Itwmw\Http\Api\Core\ParamDescription;

/**
 * 参数构建类
 *
 * @method ParamDescription array(string $name)         创建一个参数，类型为array
 * @method ParamDescription object(string $name)        创建一个参数，类型为object
 * @method ParamDescription string(string $name)        创建一个参数，类型为string
 * @method ParamDescription boolean(string $name)       创建一个参数，类型为boolean
 * @method ParamDescription integer(string $name)       创建一个参数，类型为integer
 * @method ParamDescription numeric(string $name)       创建一个参数，类型为numeric
 * @method ParamDescription number(string $name)        创建一个参数，类型为number
 * @method ParamDescription null(string $name)          创建一个参数，类型为null
 * @method ParamDescription any(string $name)           创建一个参数，类型为any
 */
class ParamDefinition
{
    private array $params = [];

    public function __call($name, $arguments)
    {
        $types = ['array', 'object', 'string', 'boolean', 'integer', 'number', 'numeric', 'null', 'any'];
        if (in_array($name, $types)) {
            if (empty($arguments)) {
                throw new \ArgumentCountError('Too few arguments to function ' . basename(static::class) . '::' . $name . '(), 0 passed');
            }
            $params         = new ParamDescription($arguments[0], $name);
            $this->params[] = $params;
            return $params;
        }
        throw new Error('Call to undefined method ' . basename(static::class) . '::' . $name . '()');
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
