<?php

namespace Anh\ContentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CategoryIsRequired extends Constraint
{
    public $message = "Category is required in section '%section%'.";

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'anh_content_category_is_required_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
