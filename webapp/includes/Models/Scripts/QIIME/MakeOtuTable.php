<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\DefaultScript;
use Models\Scripts\Parameters\VersionParameter;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\TextArgumentParameter;
use Models\Scripts\Parameters\TrueFalseParameter;
use Models\Scripts\Parameters\TrueFalseInvertedParameter;
use Models\Scripts\Parameters\NewFileParameter;
use Models\Scripts\Parameters\OldFileParameter;
use Models\Scripts\Parameters\ChoiceParameter;
use Models\Scripts\Parameters\Label;

class MakeOtuTable extends DefaultScript {

	public function initializeParameters() {
		parent::initializeParameters();
		$otuMapFile = new OldFileParameter("--otu_map_fp", $this->project);
		$otuMapFile->requireIf();
		// TODO dynamic default / no default
		$outputBiomFp = new NewFileParameter("--output_biom_fp", "_.biom");
		$outputBiomFp->requireIf();

		array_push($this->parameters,
			new Label("Required Parameters"),
			$otuMapFile,
			$outputBiomFp, 
			new Label("Optional Parameters"),
			new OldFileParameter("--taxonomy", $this->project),
			new OldFileParameter("--exclude_otus_fp", $this->project),
			new Label("Output Options"),
			new TrueFalseParameter("--verbose")
		);
	}
	public function getScriptName() {
		return "make_otu_table.py";
	}
	public function getScriptTitle() {
		return "Make OTU table";
	}
	public function getHtmlId() {
		return "make_otu_table";
	}
}
