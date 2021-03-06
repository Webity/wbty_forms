<?php

/**
 * @version     0.2.0
 * @package     com_wbty_forms
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Webity <info@makethewebwork.com> - http://www.makethewebwork.com
 */
defined('_JEXEC') or die;

jimport('wbty_components.models.wbtymodellist');

/**
 * Methods supporting a list of Wbty_forms records.
 */
class Wbty_formsModelresponse_values extends WbtyModelList {

    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                			'field_id', 'a.field_id',
								'value', 'a.value',
					

            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null) {
        
        // Initialise variables.
        $app = JFactory::getApplication();
		
		$this->response_id=JRequest::getVar('response_id',0,'get');
		
		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = $app->input->get('limitstart', 0, '', 'int');
        $this->setState('list.start', $limitstart);

        // List state information.
        parent::populateState('a.field_id', 'asc');
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                    'list.select',
                    'a.*'
                )
        );
        $query->from('`#__wbty_forms_response_values` AS a');

        
        
		
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }
		
		$search = JFactory::getApplication()->input->get('response_values', array(), 'post', 'ARRAY');
		if ($search) {
			$this->processSearch($query, $search, array());
		}
        

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('( a.field_id LIKE '.$search.' )');
			}
		}
		
		if ($this->response_id) {
			$query->where('a.response_id = '.(int)$this->response_id);
		}
        
        // Add the list ordering clause.
        $orderCol   = $this->state->get('list.ordering');
        $orderDirn  = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol.' '.$orderDirn));
        }

        return $query;
    }

}
