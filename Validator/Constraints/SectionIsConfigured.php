<?php

namespace Anh\ContentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SectionIsConfigured extends Constraint
{
    public $message = "Section '%section%' is not configured.";

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'anh_content_section_is_configured_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}