<?php

namespace Anh\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

trait InjectOptionsTrait
{
    protected function injectOptions(Request $request, array $options)
    {
        $request->attributes->set('_anh_resource', $this->merge(
            $request->attributes->get('_anh_resource', array()),
            $options
        ));
    }

    protected function merge(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
