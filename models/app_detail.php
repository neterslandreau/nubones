<?php
App::import('Model', 'Users.Detail');
class AppDetail extends Detail {
    public $useTable = 'details';
    public $name = 'AppDetail';
	public $alias = 'Detail';
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}

/**
 * Create the default fields for a user
 *
 * @param string $userId User ID
 * @return void
 */
	public function createDefaults($userId) {
		$entries = array(
			array(
				'field' => 'User.firstname',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.middlename',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.lastname',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.abbr-country-name',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.abbr-region',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.country-name',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.location',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.postal-code',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.region',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'),
			array(
				'field' => 'User.timeoffset',
				'value' => '',
				'input' => 'text',
				'data_type' => 'string'));

		$i = 0;
		$data = array();
		foreach ($entries as $entry) {
			$data[$this->alias] = $entry;
			$data[$this->alias]['user_id'] = $userId;
			$data[$this->alias]['position'] = $i++;
			$this->create();
			$this->save($data);
		}
	}

/**
 * Save details for named section
 * 
 * @var string $userId User ID
 * @var array $data Data
 * @var string $section Section name
 * @return boolean True on successful validation and saving of the virtual fields
 *
	public function saveSection($userId = null, $data = null, $section = null) {
		if (!empty($this->sectionSchema[$section])) {
			$tmpSchema = $this->_schema;
			$this->_schema = $this->sectionSchema[$section];
		}

		if (!empty($this->sectionValidation[$section])) {
			$tmpValidate = $this->validate;
			$data = $this->set($data);
			$this->validate = $this->sectionValidation[$section];
			if (!$this->validates()) {
				return false;
			}
			$this->validate = $tmpValidate;
		}

		if (isset($tmpSchema)) {
			$this->_schema = $tmpSchema;
		}

		if (!empty($data) && is_array($data)) {
			foreach($data as $model => $details) {
				if ($model == $this->alias) {
					// Save the details
					foreach($details as $key => $value) {
						// Quickfix for date inputs - TODO Try to use $this->deconstruct()?
						if (is_array($value) && array_keys($value) == array('month', 'day', 'year')) {
							$value = $value['year'] . '-' . $value['month'] . '-' .  $value['day']; 
						}
						$newDetail = array();
						$field = $section . '.' . $key;
						$detail = $this->find('first', array(
							'recursive' => -1,
							'conditions' => array(
								'user_id' => $userId,
								'field' => $field),
							'fields' => array('id', 'field')));
						if (empty($detail)) {
							$this->create();
							$newDetail[$model]['user_id'] = $userId;
						} else {
							$newDetail[$model]['id'] = $detail['Detail']['id'];
						}

						$newDetail[$model]['field'] = $field;
						$newDetail[$model]['value'] = $value;
						$newDetail[$model]['input'] = '';
						$newDetail[$model]['data_type'] = '';
						$newDetail[$model]['label'] = '';
						$this->save($newDetail, false);
					}
				} elseif (isset($this->{$model})) {
					// Save other model data
					$toSave[$model] = $details;
					if (!empty($userId)) {
						if ($model == 'User') {
							$toSave[$model]['id'] = $userId;
						} elseif ($this->{$model}->hasField('user_id')) {
							$toSave[$model]['user_id'] = $userId;
						}
					}
					$this->{$model}->save($toSave, false);
				}
			}
		}
		return true;
	}
/* */
}
