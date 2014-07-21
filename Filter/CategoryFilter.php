<?php

namespace Anh\ContentBundle\Filter;

use Anh\DoctrineResourceBundle\AbstractFilter;

class CategoryFilter extends AbstractFilter
{
    protected $sections;

    public function __construct($sections)
    {
        foreach ($sections as $section => $config) {
            if ($config['category']) {
                $this->sections[] = $section;
            }
        }
    }

    public function getSortFields(array $parameters = array())
    {
        return array(
            'section' => 'section',
            'title' => 'title',
            'updatedAt' => 'updatedAt',
            'createdAt' => 'createdAt',
        );
    }

    public function getDefinition(array $parameters = array())
    {
        $filter = array();

        if (count($this->sections) > 1) {
            $filter['section'] = array(
                'type' => 'choice',
                'form' => array(
                    'empty_value' => 'All',
                    'choices' => array_combine($this->sections, $this->sections),
                ),
            );
        }

        return $filter + array(
            'title' => array(
                'type' => 'text',
                'operator' => function($value) {
                    if (strpos($value, '%') === false) {
                        $value = sprintf('%%%s%%', $value);
                    }

                    return [ '%title' => array('like' => $value) ];
                },
            ),
        );
    }
}
