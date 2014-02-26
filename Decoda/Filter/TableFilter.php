<?php

namespace Anh\ContentBundle\Decoda\Filter;

use Decoda\Decoda;
use Decoda\Filter\TableFilter as BaseTableFilter;

class TableFilter extends BaseTableFilter
{
    public function __construct()
    {
        $this->_tags['td'] = array(
            'htmlTag' => 'td',
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_BOTH,
            'parent' => array('tr'),
            'attributes' => array(
                'default' => self::NUMERIC,
                'colspan' => self::NUMERIC,
                'rowspan' => self::NUMERIC
            ),
            'mapAttributes' => array(
                'default' => 'colspan'
            )
        );
    }
}