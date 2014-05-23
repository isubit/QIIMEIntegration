<?php

namespace Models\Scripts;

interface ScriptI {

	public function __construct(\Models\Project $project);

	// Dumb accessors
	public function getParameters();
	// (script specific)
	public function getScriptName();
	public function getScriptTitle();
	public function getHtmlId();
	public function renderHelp();
	public function initializeParameters();

	// output
	public function renderAsForm();
	public function renderCommand();

	// input
	public function acceptInput(array $input);
}
