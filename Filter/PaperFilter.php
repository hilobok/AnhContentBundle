<?php

namespace Anh\ContentBundle\Filter;

use Anh\DoctrineResourceBundle\AbstractFilter;
use Doctrine\ORM\EntityRepository;

class PaperFilter extends AbstractFilter
{
    protected $categoryClass;

    protected $sections;

    public function __construct($categoryClass, $sections)
    {
        $this->categoryClass = $categoryClass;
        $this->sections = $sections;
    }

    public function getSortFields(array $parameters = array())
    {
        if (!isset($parameters['section'])) {
            throw new \Exception("Parameter 'section' is required for filter.");
        }

        $section = $parameters['section'];

        $fields = array();

        if ($this->sections[$section]['publishedSince']) {
            $fields['publishedSince'] = 'publishedSince';
        }

        if ($this->sections[$section]['category']) {
            $fields['category.title'] = 'category';
        }

        return $fields + array(
            'updatedAt' => 'updatedAt',
            'createdAt' => 'createdAt',
            'title' => 'title',
            'externalLinksCount' => 'Number of external links',
        );
    }

    public function getDefinition(array $parameters = array())
    {
        if (!isset($parameters['section'])) {
            throw new \Exception("Parameter 'section' is required for filter.");
        }

        $section = $parameters['section'];

        $filter = array();

        if ($this->sections[$section]['category']) {
            $filter['category'] = array(
                'type' => 'entity',
                'form' => array(
                    'class' => $this->categoryClass,
                    'property' => 'title',
                    'empty_value' => 'Any',
                    'query_builder' => function (EntityRepository $repository) use ($section) {
                        return $repository->prepareQueryBuilder([ 'section' => $section ], [ 'title' => 'asc']);
                    }
                ),
            );
        }

        return $filter + array(
            'title' => array(
                'type' => 'text',
                'operator' => function ($value) {
                    if (strpos($value, '%') === false) {
                        $value = sprintf('%%%s%%', $value);
                    }

                    return [ '%title' => array('like' => $value) ];
                },
            ),

            'isDraft' => array(
                'type' => 'checkbox',
                'empty_data' => false,
            ),

            'hasLinks' => array(
                'type' => 'choice',
                'form' => array(
                    'empty_value' => 'No matter',
                    'choices' => array(
                        1 => 'With links',
                        2 => 'Without links',
                    )
                ),
                'operator' => function ($value) {
                    switch ($value) {
                        case 1:
                            return ['%externalLinksCount' => ['>' => 0]];
                        case 2:
                            return ['externalLinksCount' => 0];
                    }
                },
            ),
        );
    }
}
