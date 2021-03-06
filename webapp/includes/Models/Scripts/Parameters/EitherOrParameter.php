<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class EitherOrParameter extends DefaultParameter {
	protected $default;
	protected $alternative;

	protected $currentSelection = NULL;
	protected $currentNonSelection = NULL;

	protected $displayName;

	public function __construct($default, $alternative, $displayName = "") {
		$this->default = $default;
		$this->alternative = $alternative;

		$this->name = "__{$default->getName()}__{$alternative->getName()}__";
		if ($displayName) {
			$this->displayName = $displayName;
		}
		else {
			$this->displayName = $this->default->getName() . " or " . $this->alternative->getName();
		}
	}

	public function renderForOperatingSystem() {
		if (!$this->value) {
			return "";
		}
		if ($this->value == $this->default->getName()) {
			return $this->default->renderForOperatingSystem();
		}
		if ($this->value == $this->alternative->getName()) {
			return $this->alternative->renderForOperatingSystem();
		}
	}

	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		$disabledString = ($disabled) ? " disabled" : "";
		$checkedArray = array('','','');
		if (!$this->value) {
			$checkedArray[0] = " checked";
		}
		else if ($this->value == $this->default->getName()) {
			$checkedArray[1] = " checked";
		}
		else if ($this->value == $this->alternative->getName()) {
			$checkedArray[2] = " checked";
		}

		$output = "<table class=\"either_or\"><tr><td colspan=\"2\"><label for=\"{$this->name}\">{$this->displayName}
			<a class=\"param_help\" id=\"{$this->getJsVar($script->getJsVar())}\">&amp;</a><br/>";
		$output .= "<input type=\"radio\" name=\"{$this->name}\" value=\"\"{$checkedArray[0]}{$disabledString}>Neither</label></td></tr>";
		$output .= "<tr>" . 
			"<td><label for=\"{$this->name}\"><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->default->getName()}\"{$checkedArray[1]}{$disabledString}>
				{$this->default->renderForForm($disabled, $script)}</label></td>" . 
				"<td><label for=\"{$this->name}\"><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->alternative->getName()}\"{$checkedArray[2]}{$disabledString}>
				{$this->alternative->renderForForm($disabled, $script)}</label></td>" . 
			"</tr></table>";
		return $output;
	}
	public function renderFormScript($formJsVar, $disabled) {
		if ($disabled) {
			return "";
		}
		$code = parent::renderFormScript($formJsVar, $disabled);
		$jsVar = $this->getJsVar($formJsVar);
		$code .= "\tmakeEitherOr({$jsVar});{$jsVar}.change();\n";
		$code .= $this->default->renderFormScript($formJsVar, $disabled);
		$code .= $this->alternative->renderFormScript($formJsVar, $disabled);
		return $code;
	}
	public function isValueValid($value) {
		if (!$value) {
			return true;
		}
		if ($this->default->getName() == $value) {
			return true;
		}
		if ($this->alternative->getName() == $value) {
			return true;
		}
		return false;
	}

	public function setValue($value) {
		parent::setValue($value);
		if ($this->value == $this->default->getName()) {
			$this->currentSelection = $this->default;
			$this->currentNonSelection = $this->alternative;
		}
		else {
			$this->currentSelection = $this->alternative;
			$this->currentNonSelection = $this->default;
		}
	}

	public function getUnselectedValue() {
		if (!$this->value || !$this->currentNonSelection) {
			return "";
		}
		return $this->currentNonSelection->getName();
	}

	public function acceptInput(array $input) {
		parent::acceptInput($input);
		if (!isset($input[$this->name]) || !$input[$this->name]) {
			$this->setValue("");
			return;
		}

		$this->setValue($input[$this->name]);
		if (!isset($input[$this->value])) {
			throw new ScriptException("Since {$this->name} is set to {$this->value}, that parameter must be specified.");
		}
		if (isset($input[$this->getUnselectedValue()])) {
			throw new ScriptException("Since {$this->name} is set to {$this->value}, {$this->getUnselectedValue()} is not allowed.");
		}
		$this->currentSelection->acceptInput($input);
	}
}
