<?php

namespace Itwmw\Http\Api\Core;

use JetBrains\PhpStorm\ExpectedValues;

class ParamValidator
{
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
    public function __construct(
        public ?string $pattern = null,
        public ?array $enum = null,
        public ?int $minItems = null,
        public ?int $maxItems = null,
        public ?int $minLength = null,
        public ?int $maxLength = null,
        public ?int $minimum = null,
        public ?int $maximum = null,
        #[ExpectedValues(values: ['date-time', 'date', 'time', 'timestamp', 'date-time-http', 'boolean-string'])]
        public ?string $format = null
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'pattern'   => $this->pattern,
            'enum'      => $this->enum,
            'minItems'  => $this->minItems,
            'maxItems'  => $this->maxItems,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
            'minimum'   => $this->minimum,
            'maximum'   => $this->maximum,
            'format'    => $this->format
        ], fn ($item) => !is_null($item));
    }
}
