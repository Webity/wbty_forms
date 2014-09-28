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
class Wbty_formsModelresponses extends WbtyModelList
{

    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                			'form_id', 'a.form_id',
								'submission_time', 'a.submission_time',
								'ip_address', 'a.ip_address',
								'spam', 'a.spam',
					

            );
        }

        parent::__construct($config);
    }

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		
		
		
		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_wbty_forms');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'desc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__wbty_forms_responses` AS a');

		$query->select('f.name');
		$query->leftjoin('#__wbty_forms_forms as f ON f.id = a.form_id');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
        
		
		$query->select('( SELECT COUNT(id) as response_values FROM #__wbty_forms_response_values WHERE response_id = a.id AND state=1) as response_values');
        

        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }
        
        

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('( a.form_id LIKE '.$search.' )');
			}
		}
		

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
		    $query->order($db->escape($orderCol.' '.$orderDirn));
        }

		return $query;
	}

    public function getItems() {
        $store = $this->getStoreId();

        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        // partial copy to allow post processing and cache control
        $items = parent::getItems();
        if ($items) {
            foreach ($items as $key=>$item) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);

                $query->select('rv.value as value, fo.value as label')
                    ->from('#__wbty_forms_response_values AS rv')
                    ->leftjoin('#__wbty_forms_form_fields as ff ON ff.id = rv.field_id')
                    ->leftjoin('#__wbty_forms_field_options as fo ON fo.field_id = ff.id AND fo.name LIKE \'label\'')
                    ->where('rv.response_id='.(int)$item->id);

                $items[$key]->fields = $db->setQuery($query, 0, 4)->loadObjectList();
            }
        }

        $this->cache[$store] = $items;

        return $this->cache[$store];
    }

}
