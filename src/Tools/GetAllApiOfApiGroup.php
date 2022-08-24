<?php

namespace Itwmw\Http\Api\Tools;

use Itwmw\Http\Api\Core\ApiDescription;
use Itwmw\Http\Api\Core\Exception\ApiException;

trait GetAllApiOfApiGroup
{
    protected array $apis = [];

    public function getAllApi(): array
    {
        $apis = [];

        foreach ($this->apis as $api) {
            if (!method_exists($this, $api)) {
                throw new ApiException("Api ${api} not fount");
            }
            $api = $this->$api();
            if (!$api instanceof ApiDescription) {
                throw new ApiException("Api ${api} Not a valid API");
            }
            $apis = array_merge($apis, $api->toArray());
        }

        return $apis;
    }
}
