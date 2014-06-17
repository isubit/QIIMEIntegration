<?php

namespace Controllers;

class ViewResultsController extends Controller {

	public function getSubTitle() {
		return "View Results";
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "You have not selected a project, therefore there are no results to view.";
		}
	}
	public function retrievePastResults() {
		if (!$this->project) {
			return "<p>In order to view results, you much <a href=\"\">log ing</a> and <a href=\"\">select a project</a></p>";
		}

		$helper = \Utils\Helper::getHelper();
		$output = "<h3>{$this->project->getName()}</h3>";
		$output .= "<ul>
			<li>Owner: {$this->project->getOwner()}</li>
			<li>Unique id: {$this->project->getId()}</li>
			</ul>";

		$uploadedFiles = $this->project->retrieveAlluploadedFiles();
		if (!empty($uploadedFiles)) {
			$output .= "<h3>Uploaded Files:</h3><div class=\"accordion\">\n";
			$uploadedFilesFormatted = $helper->categorizeArray($uploadedFiles, 'type', 'name'); 
			foreach ($uploadedFilesFormatted as $fileType => $fileNames) {
				$output .= "<h4>{$fileType} files</h4><div><ul>\n";
				foreach ($fileNames as $fileName) {
					$output .= "<li>" . htmlentities($fileName) . "</li>\n";
				}
				$output .= "</ul></div>\n";
			}
			$output .= "</div>";
		}
		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "You can see a preview of the file you wish to download here:<br/>
				<div class=\"file_example\" id=\"file_preview\"></div>";
		}

		return $output;
	}
	public function renderInstructions() {
		return "<p>Here is the moment you've been waiting for... your results! From this page you can preview and download any of the files that
			you have generated by running scripts.</p>";
	}
	public function renderForm() {
		if (!$this->project) {
			return "";
		}
		$helper = \Utils\Helper::getHelper();

		$output = "";	
		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "<h3>Generated Files:</h3>\n";
			$generatedFilesFormatted = $helper->categorizeArray($generatedFiles, 'run_id');
			foreach ($generatedFilesFormatted as $runId => $files) {
				$output .= "<h4>files from run {$runId}</h4><table>\n";
				foreach ($files as $file) {
					$output .= "<tr><td>" . htmlentities($file['name']) . "</td>
						<td><a class=\"button\" onclick=\"previewFile('download.php?run={$file['run_id']}&file_name={$file['name']}&as_text=true')\">Preview</a></td>
						<td><a class=\"button\" onclick=\"window.location='download.php?run={$file['run_id']}&file_name={$file['name']}'\">Download</a></td></tr>";
				}
				$output .= "</table>\n";
			}
		}

		return $output;
	}
	public function renderHelp() {
		return "";
	}
	public function renderSpecificStyle() {
		return "div.file_examples{margin:.75em;display:none}div.form td{padding:.5em;border-bottom-width:1px}";
	}
	public function renderSpecificScript() {
		return "function previewFile(url){
			var displayDiv = $('#file_preview');
			displayDiv.css('display', 'block');
			displayDiv.load(url);}";
	}
	public function getScriptLibraries() {
		return array();
	}
}
