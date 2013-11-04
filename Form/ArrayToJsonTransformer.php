<?php

namespace Anh\ContentBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToJsonTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if ($value === null) {
            return '';
        }

        return json_encode($value);
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        return json_decode($value, true);
    }
}
