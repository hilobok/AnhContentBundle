<?php

namespace Anh\ContentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SectionIsConfiguredValidator extends ConstraintValidator
{
    protected $sections;

    public function __construct($sections)
    {
        $this->sections = array_keys($sections);
    }

    public function validate($section, Constraint $constraint)
    {
        if (!in_array($section, $this->sections)) {
            $this->context->addViolation(
                $constraint->message,
                array(
                    '%section%' => $section
                )
            );
        }
    }
}