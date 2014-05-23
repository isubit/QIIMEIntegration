<?php

namespace Models\Scripts\Parameters;

class HelpParameter extends DefaultParameter {

	private $script;

	public function __construct(\Models\Scripts\ScriptI $script) {
		$this->script = $script;
	}
	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm() {
		return "<a href=\"public/manual/{$this->script->getHtmlId()}.txt\" target=\"_blank\" class=\"button\">See manual page</a>";
	}
}