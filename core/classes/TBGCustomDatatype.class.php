<?php

	class TBGCustomDatatype extends TBGIdentifiableClass
	{
		
		protected $_b2dbtablename = 'TBGCustomDatatypesTable';

		const DROPDOWN_CHOICE_TEXT = 1;
		const INPUT_TEXT = 2;
		const INPUT_TEXTAREA_MAIN = 3;
		const INPUT_TEXTAREA_SMALL = 4;
		const RADIO_CHOICE = 5;
		const CHECKBOX_CHOICES = 6;
		const RELEASES_LIST = 7;
		const RELEASES_CHOICE = 8;
		const COMPONENTS_LIST = 9;
		const COMPONENTS_CHOICE = 10;
		const EDITIONS_LIST = 11;
		const EDITIONS_CHOICE = 12;
		const STATUS_CHOICE = 13;
		const USER_CHOICE = 14;
		const TEAM_CHOICE = 15;
		const CUSTOMER_CHOICE = 16;
		const USER_OR_TEAM_CHOICE = 17;
		const DROPDOWN_CHOICE_TEXT_COLORED = 18;
		const DROPDOWN_CHOICE_TEXT_COLOR = 19;
		const DROPDOWN_CHOICE_TEXT_ICON = 20;

		protected static $_types = null;

		/**
		 * This custom types options (if any)
		 *
		 * @var array
		 */
		protected $_options = null;

		/**
		 * The custom types key
		 *
		 * @var string
		 */
		protected $_key = null;

		/**
		 * The custom types description
		 *
		 * @var string
		 */
		protected $_description = null;

		/**
		 * The custom types instructions
		 *
		 * @var string
		 */
		protected $_instructions = null;

		/**
		 * Returns all custom types available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_types === NULL)
			{
				self::$_types = array();
				if ($items = B2DB::getTable('TBGCustomFieldsTable')->getAll())
				{
					foreach ($items as $row_id => $row)
					{
						self::$_types[$row->get(TBGCustomFieldsTable::FIELD_KEY)] = TBGContext::factory()->TBGCustomDatatype($row_id, $row);
					}
				}
			}
			return self::$_types;
		}

		public static function getFieldTypes()
		{
			$i18n = TBGContext::getI18n();
			$types = array();
			$types[self::DROPDOWN_CHOICE_TEXT] = $i18n->__('Dropdown list with custom text choices');
			/*$types[self::DROPDOWN_CHOICE_TEXT_COLORED] = $i18n->__('Dropdown list with custom colored text choices');
			$types[self::DROPDOWN_CHOICE_TEXT_COLOR] = $i18n->__('Dropdown list with custom color and text choices');
			$types[self::DROPDOWN_CHOICE_TEXT_ICON] = $i18n->__('Dropdown list with custom text choices and icons');*/
			$types[self::INPUT_TEXT] = $i18n->__('Single line text input');
			$types[self::INPUT_TEXTAREA_MAIN] = $i18n->__('Textarea in issue main area');
			$types[self::INPUT_TEXTAREA_SMALL] = $i18n->__('Textarea (small) in issue details list');
			$types[self::RADIO_CHOICE] = $i18n->__('Radio choices');
			$types[self::CHECKBOX_CHOICES] = $i18n->__('Checkbox choices');
			$types[self::RELEASES_LIST] = $i18n->__('Add one or more releases from the list of available releases');
			$types[self::RELEASES_CHOICE] = $i18n->__('Select a release from the list of available releases');
			$types[self::COMPONENTS_LIST] = $i18n->__('Add one or more components from the list of available components');
			$types[self::COMPONENTS_CHOICE] = $i18n->__('Select a component from the list of available components');
			$types[self::EDITIONS_LIST] = $i18n->__('Add one or more editions from the list of available editions');
			$types[self::EDITIONS_CHOICE] = $i18n->__('Select a edition from the list of available editions');
			$types[self::STATUS_CHOICE] = $i18n->__('Dropdown list with statuses');
			$types[self::USER_CHOICE] = $i18n->__('Find and pick a user');
			$types[self::TEAM_CHOICE] = $i18n->__('Find and pick a team');
			$types[self::CUSTOMER_CHOICE] = $i18n->__('Find and pick a customer');
			$types[self::USER_OR_TEAM_CHOICE] = $i18n->__('Find and pick a user or a team');

			return $types;

		}

		public function preSave()
		{
			$this->_key = strtolower(str_replace(' ', '', $name));
		}
		
		/**
		 * Delete a custom type by id
		 *
		 * @param integer $id
		 */
		public function _preDelete()
		{
			$key = B2DB::getTable('TBGCustomFieldsTable')->getKeyFromId($id);
			TBGIssueCustomFieldsTable::getTable()->doDeleteByFieldId($id);
			B2DB::getTable('TBGCustomFieldOptionsTable')->doDeleteByFieldKey($key);
		}

		public static function isNameValid($name)
		{
			$key = strtolower(str_replace(' ', '', $name));
			$builtin_types = TBGDatatypeBase::getAvailableFields(true);
			if (!in_array($key, $builtin_types))
			{
				return !(bool) TBGCustomFieldsTable::getTable()->countByKey($key);
			}
			else
			{
				return false;
			}
		}

		public static function doesKeyExist($key)
		{
			return array_key_exists($key, self::getAll());
		}

		/**
		 * Get a custom type by its key
		 *
		 * @param string $key
		 *
		 * @return TBGCustomDatatype
		 */
		public static function getByKey($key)
		{
			$row = B2DB::getTable('TBGCustomFieldsTable')->getByKey($key);
			if ($row)
			{
				return TBGContext::factory()->TBGCustomDatatype($row->get(TBGCustomFieldsTable::ID), $row);
			}
			return null;
		}

		public static function getChoiceFieldsAsArray()
		{
			return array(self::CHECKBOX_CHOICES, self::DROPDOWN_CHOICE_TEXT, self::DROPDOWN_CHOICE_TEXT_COLOR, self::DROPDOWN_CHOICE_TEXT_COLORED, self::DROPDOWN_CHOICE_TEXT_ICON, self::RADIO_CHOICE);
		}

		/**
		 * Constructor
		 * 
		 * @param B2DBrow $row [optional] A B2DBrow to use
		 */
		public function _construct(B2DBRow $row)
		{
			$this->_description = $this->_description ?: $this->_name;
		}

		protected function _populateOptions()
		{
			if ($this->_options === null)
			{
				$this->_options = TBGCustomDatatypeOption::getAllByKey($this->_key);
			}
		}

		public function getOptions()
		{
			if ($this->hasCustomOptions())
			{
				$this->_populateOptions();
				return $this->_options;
			}
		}

		public function createNewOption($name, $value, $itemdata = null)
		{
			$option = TBGCustomDatatypeOption::createNew($this->_itemtype, $this->_key, $name, $value, $itemdata);
			$this->_options = null;
			return $option;
		}

		/**
		 * Return this custom types key
		 *
		 * @return string
		 */
		public function getKey()
		{
			return $this->_key;
		}

		public function getType()
		{
			return $this->_itemtype;
		}

		/**
		 * Return the description for this custom type
		 *
		 * @return string
		 */
		public function getTypeDescription()
		{
			$types = self::getFieldTypes();
			return $types[$this->_itemtype];
		}

		public function hasCustomOptions()
		{
			return (bool) in_array($this->getType(), self::getChoiceFieldsAsArray());
		}

		/**
		 * Get the custom types description
		 */
		public function getDescription()
		{
			return $this->_description;
		}

		/**
		 * Set the custom types description
		 *
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_description = $description;
		}
		
		/**
		 * Get the custom types instructions
		 */
		public function getInstructions()
		{
			return $this->_instructions;
		}

		/**
		 * Set the custom types instructions
		 *
		 * @param string $instructions
		 */
		public function setInstructions($instructions)
		{
			$this->_instructions = $instructions;
		}

		/**
		 * Whether or not this custom type has any instructions
		 *
		 * @return boolean
		 */
		public function hasInstructions()
		{
			return (bool) $this->_instructions;
		}

		/**
		 * Set the custom type name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

		/**
		 * Whether or not this custom data type is visible for this issue type
		 *
		 * @param integer $issuetype_id
		 *
		 * @return bool
		 */
		public function isVisibleForIssuetype($issuetype_id)
		{
			return true;
		}

	}

