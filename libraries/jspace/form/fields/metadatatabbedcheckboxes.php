<?php
/**
 * Supports a collection picker.
 * 
 * @author		$LastChangedBy: michalkocztorz $
 * @package		JSpace
 * @copyright	Copyright (C) 2011 Wijiti Pty Ltd. All rights reserved.
 * @license     This file is part of the JSpace component for Joomla!.

   The JSpace component for Joomla! is free software: you can redistribute it 
   and/or modify it under the terms of the GNU General Public License as 
   published by the Free Software Foundation, either version 3 of the License, 
   or (at your option) any later version.

   The JSpace component for Joomla! is distributed in the hope that it will be 
   useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with the JSpace component for Joomla!.  If not, see 
   <http://www.gnu.org/licenses/>.

 * Contributors
 * Please feel free to add your name and email (optional) here if you have 
 * contributed any source code changes.
 * Name							Email
 * Michał Kocztorz				<michalkocztorz@wijiti.com> 
 * 
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('jspace.database.table.metadata');
JFormHelper::loadFieldClass('checkboxes');


class JSpaceFormFieldMetadatatabbedcheckboxes extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var         string
	 * @since       1.6
	 */
	protected $type = 'JSpace.Metadatatabbedcheckboxes';
	
	/**
	 * 
	 * @var JSpaceTableMetadata
	 */
	protected $value;
	
	public $isMetadata = true;
	
	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   object  &$element  The JXmlElement object representing the <field /> tag for the form field object.
	 * @param   mixed   $value     The form field value to validate.
	 * @param   string  $group     The field name group control value. This acts as as an array container for the field.
	 *                             For example if the field has name="foo" and the group value is set to "bar" then the
	 *                             full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setup(&$element, $value, $group = null)
	{
		$ret = parent::setup($element, $value, $group);
		$this->element['class'] = $class . ' metadata-tabbed-checkboxes subfieldsets-formfield';
		return $ret;
	}
	
	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
	
		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';
	
		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
	
		// Get the field options.
		$options = $this->getOptions();
		
		$values = $this->value->getValues();
		if( count($values) == 1 && $values[0]=='' ) {
			$values = array();
		}
	
		$definedOptions = array();
		foreach ($options as $i => $option) {
			$definedOptions[] = (string) $option->value;
		}
// 		var_dump($values);
// 		var_dump($definedOptions);
		$otherValues = array_diff($values,$definedOptions);
// 		var_dump($otherValues);
		
		$doc = JFactory::getDocument();
		
		// Build the checkbox field output.
		$html[] = '<ul>';
		foreach ($options as $i => $option)
		{
			$isFreetextValue = substr_count($option->class, 'freetext-value')>0;
			$isExclusiveValue = substr_count($option->class, 'exclusive-value')>0;
			
			$class = '';
			if( $isExclusiveValue ) {
				$class .= 'exclusive-value-input ';
				$doc->addScript('media/com_jspace/js/formfield/exclusive_value.js');
			}
			
			if( $isFreetextValue ) {
				$doc->addScript('media/com_jspace/js/formfield/freetext_value.js');
				$class = 'freetext-value-input ';
			}
	
			// Initialize some option attributes.
			$isChecked = in_array((string) $option->value, (array) $values);
			$checked = ($isChecked ? ' checked="checked"' : '');
			$class .= !empty($option->class) ? $option->class  : '';
			$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
	
			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			
	
			if( $isFreetextValue ) {
				$html[] = '<li class="freetext-value-parent">';
				if( !$isChecked && count($otherValues)>0 ) {
					//there is at least one value that is undefined, so other field must have been selected
					$isChecked = true;
					$checked = ' checked="checked" ';
					$option->value = array_shift($otherValues);
				}
			}
			else {
				$html[] = '<li>';
			}
			$html[] = '<input type="checkbox" class="' . $class . '" data-id="' . $this->id . '" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
					. htmlspecialchars(empty($option->value)? JText::_($option->text) : $option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $onclick . $disabled . '/>';
	
			if( $isFreetextValue ) {
				$html[] = '<input type="text" maxlength="30" class="freetext-value-text" value="' . JText::_($option->value) . '" />';
			} 
			else {
				$html[] = '<label for="' . $this->id . $i . '">' . JText::_($option->text) . '</label>';
			}
			$html[] = '</li>';
		}
		$html[] = '</ul>';
	
		// End the checkbox field output.
		$html[] = '</fieldset>';
	
		return implode($html);
	}
	
	/**
	 * Returns an array of subforms associated with this fromfiels's options.
	 * Array indexed with options/values of this field.
	 * E.g.:
	 * array(
	 * 		'Video Files' 	=> array( formfield1, formfield2, ...),
	 * 		'Images' 		=> array( formfield1, formfield2, ...),
	 * )
	 * @return array
	 */
	public function getSubFieldsets() {
		$doc = JFactory::getDocument();
		$doc->addScript('media/com_jspace/js/formfield/subfieldsets_checkboxes.js');
		
		$subfieldsets = array();
		

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}
			$val = ((string) $option['value'])=='' ? (string) $option : $option['value'];
			
			 $sub = $this->form->getFieldset( 'subfieldset.' . $this->fieldname . '.' . $val );
			 if( count($sub) > 0 ) {
			 	$subfieldsets[JText::_($val)] = $sub;
			 }
		}
		
		return $subfieldsets;
	}
	
	

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();
		
		if( $this->element['insertEmptyOption'] ) {
			$options[] = JHtml::_('select.option', "", (string) JText::_("COM_JSPACE_EMPTY_SELECT_OPTION"));
		}
	
		foreach ($this->element->children() as $option)
		{
	
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}
	
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', (string) JText::_($option));
	
			// Set some option attributes.
			$tmp->class = (string) $option['class'];
	
			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
	
			// Add the option object to the result set.
			$options[] = $tmp;
		}
	
		reset($options);
	
		return $options;
	}

}









