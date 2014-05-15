<?php

namespace Controllers;

class LoginController extends Controller {

	protected $subTitle = "Login";

	public function parseInput() {
		if (!isset($_POST['username'])) {
			return;
		}
		$this->hasResult = true;
		$username = $_POST['username'];
		$userExists = $this->database->userExists($username);
		
		if ($_POST['create']) {
			if ($userExists) {
				$this->isResultError = true;
				$this->result = "That username is already taken.  Did you mean to log in?";
			}
			else {
				$this->createUser($username);
			}
		}
		else {
			if ($userExists) {
				$this->logIn($username);
			}
			else {
				$this->isResultError = true;
				$this->result = "We found no record of your username.  Would you like to create one?";
			}
		}
	}

	private function logIn($username) {
		$_SESSION = array();
		$_SESSION['username'] = $username;
		$this->project = NULL;
		$this->username = $username;
		$this->result = "You have successfully logged in.";
	}
	private function createUser($username) {
		$this->database->createUser($username);

		$this->logIn($username);
		$this->result = "You have successfully created a new user.";
	}

	public function getInstructions() {
		return "<p>You don't actually need credentials to log in. By entering your name here, you are simply keeping track of your projects.
			We expect everyone on this system to play nicely, and work only on their own projects. We recognize this assumption is naive.</p>";
	}
	public function getForm() {
		$loginForm = "
			<form method=\"POST\">
			<p>Log in (existing user)<br/>
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<label for=\"username\">User name: <input type=\"text\" name=\"username\" value=\"{$this->username}\"></label>
			<button type=\"submit\">Log In</button></p>
			</form>";
		$createForm = "
			<form method=\"POST\">
			<p>Create new user<br/>
			<input type=\"hidden\" name=\"step\" value=\"$this->step\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"username\">New user name: <input type=\"text\" name=\"username\"></label>
			<button type=\"submit\">Create</button></p>
			</form>";
		return $loginForm . "<strong>-OR-</strong><br/>" . $createForm;
	}
}
