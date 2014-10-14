<?php

namespace Anh\ContentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CategoryIsRequiredValidator extends ConstraintValidator
{
    protected $sections;

    public function __construct($sections)
    {
        $this->sections = $sections;
    }

    public function validate($paper, Constraint $constraint)
    {
        $section = $paper->getSection();

        if ($this->sections[$section]['category'] && is_null($paper->getCategory())) {
            $this->context->addViolation(
                $constraint->message,
                array(
                    '%section%' => $section
                )
            );
        }
    }
}
