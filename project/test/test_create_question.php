<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
	flash("You don't have permission to access this page");
	die(header("Location: ../login.php"));
}

if(isset($_GET["id"])){
    $id = $_GET["id"];
}
?>
<div class="container-fluid">
	<h3>Create Question</h3>
    <h5>Must have at least 2 answers per question</h5>
	<form method="POST">
        <div class="form-group">
		    <label>Question</label>
		    <input class="form-control" name="question" placeholder="Question"/>
        </div>
        <input class="btn btn-primary" type="submit" name="save" value="Add Answers"/>
        <a class="btn btn-success" type="button" href="test_edit_survey.php?id=<?php echo($id); ?>">View Survey</a>
	</form>
</div>
<?php
if (isset($_POST["save"])) {
	$question = $_POST["question"];
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Questions (question, survey_id, user_id) VALUES(:question, :survey_id, :user)");
	$r = $stmt->execute([
		":question" => $question,
		":survey_id" => $id,
		":user" => $user
	]);
	if ($r) {
		//flash("Created successfully with id: " . $db->lastInsertID());
        $qid = $db->lastInsertId();
        die(header("Location: test_create_answer.php?id=$qid&survey_id=$id"));
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/../partials/flash.php");
