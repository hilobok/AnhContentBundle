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

    /**
     * Recursive analog of $array1 + $array2
     *
     * @param  array  $array1
     * @param  array  $array2
     * @return array
     */
    protected function merge(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (array_key_exists($key, $merged)) {
                if (is_array($merged[$key]) && is_array($value)) {
                    $merged[$key] = $this->merge($merged[$key], $value);
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
