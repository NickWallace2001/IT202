<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
	flash("You don't have permission to access this page");
	die(header("Location: ../login.php"));
}
?>
	<h3>Create Question</h3>
	<form method="POST">
		<label>Question</label>
		<input name="question" placeholder="Question"/>
		<label>Survey ID</label>
		<input type="number" name="survey_id" placeholder="Survey ID"/>
		<input type="submit" name="save" value="Create"/>
	</form>

<?php
if (isset($_POST["save"])) {
	$question = $_POST["question"];
	$survey_id = $_POST["survey_id"];
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Questions (question, survey_id, user_id) VALUES(:question, :survey_id, :user)");
	$r = $stmt->execute([
		":question" => $question,
		":survey_id" => $survey_id,
		":user" => $user
	]);
	if ($r) {
		flash("Created successfully with id: " . $db->lastInsertID());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/../partials/flash.php");
