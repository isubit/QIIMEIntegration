<?php

namespace Controllers;

class ViewResultsController extends Controller {

	protected $subTitle = "View Results";

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "You have not selected a project, therefore there are no results to view.";
			return;
		}
		if (isset($_POST['delete'])) {
			$this->hasResult = true;
			try {
				$isUploaded = (isset($_POST['uploaded']) && $_POST['uploaded']);
				if ($isUploaded) {
					$this->project->deleteUploadedFile($_POST['delete']);
				}
				else {
					if (!isset($_POST['run'])) {
						throw new \Exception("You must provide either a run id, or specify that the file is uploaded.");
					}
					if (!is_numeric($_POST['run'])) {
						throw new \Exception("Run id must be numeric");
					}
					$this->project->deleteGeneratedFile($_POST['delete'], $_POST['run']);
				}
				$this->result = "File deleted: " . htmlentities($_POST['delete']);
			}
			catch (\Exception $ex) {
				$this->isResultError = true;
				$this->result = $ex->getMessage();
			}
		}
	}
	public function retrievePastResults() {
		if (!$this->project) {
			return "<p>In order to view results, you much <a href=\"\">log ing</a> and <a href=\"\">select a project</a></p>";
		}

		$output = "<h3>{$this->project->getName()}</h3>";
		$output .= "<ul>
			<li>Owner: {$this->project->getOwner()}</li>
			<li>Unique id: {$this->project->getId()}</li>
			</ul>";

		$uploadedFiles = $this->project->retrieveAlluploadedFiles();
		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($uploadedFiles) || !empty($generatedFiles)) {
			$output .= "<hr/>You can see a preview of the file you wish to download here:<br/>
				<div class=\"file_example\" id=\"file_preview\" style=\"margin:.75em;display:none\"></div>";
		}

		return $output;
	}
	public function getInstructions() {
		return "<p>Here is the moment you've been waiting for... your results! From this page you can preview and download any of the files that
			you have generated by running scripts.</p>
			<style>div.form td{padding:.5em;border-bottom:1px #999966 solid;}</style>
			<script type=\"text/javascript\">
	function previewFile(url){
		var displayDiv = $('#file_preview');
		displayDiv.css('display', 'block');
		displayDiv.load(url);
	}</script>";
	}
	public function getForm() {
		if (!$this->project) {
			return "";
		}
		$output = "";	

		$helper = \Utils\Helper::getHelper();
		$uploadedFiles = $this->project->retrieveAlluploadedFiles();
		if (!empty($uploadedFiles)) {
			$output .= "<h3>Uploaded Files:</h3>\n";
			$uploadedFilesFormatted = $helper->categorizeArray($uploadedFiles, 'type'); 
			foreach ($uploadedFilesFormatted as $fileType => $files) {
				$output .= "<h4>uploaded files of type {$fileType}</h4><table>\n";
				foreach ($files as $file) {
					$output .= "<tr><td>" . htmlentities($file['name']) . " ({$file['status']})</td>
						<td><a class=\"button\" onclick=\"previewFile('download.php?uploaded=true&file_name={$file['name']}&as_text=true')\">Preview</a></td>
						<td><a class=\"button\" onclick=\"window.location='download.php?uploaded=true&file_name={$file['name']}'\">Download</a></td>
						<td><form method=\"POST\" onsubmit=\"return confirm('Are you sure you want to delete this file?  Action cannot be undone.')\"><input type=\"hidden\" name=\"uploaded\" value=\"true\">
							<button type=\"submit\" name=\"delete\" value=\"{$file['name']}\">Delete</button></form></td></tr>";
				}
				$output .= "</table>\n";
			}
		}

		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "<h3>Generated Files:</h3>\n";
			$generatedFilesFormatted = $helper->categorizeArray($generatedFiles, 'run_id');
			foreach ($generatedFilesFormatted as $runId => $files) {
				$output .= "<h4>files from run {$runId}</h4><table>\n";
				foreach ($files as $file) {
					$output .= "<tr><td>" . htmlentities($file['name']) . "</td>
						<td><a class=\"button\" onclick=\"previewFile('download.php?run={$file['run_id']}&file_name={$file['name']}&as_text=true')\">Preview</a></td>
						<td><a class=\"button\" onclick=\"window.location='download.php?run={$file['run_id']}&file_name={$file['name']}'\">Download</a></td>
						<td><form method=\"POST\" onsubmit=\"return confirm('Are you sure you want to delete this file?  Action cannot be undone.')\"><input type=\"hidden\" name=\"run\" value=\"{$file['run_id']}\">
							<button type=\"submit\" name=\"delete\" value=\"{$file['name']}\">Delete</button></form></td></tr>";
				}
				$output .= "</table>\n";
			}
		}

		return $output;
	}
}
