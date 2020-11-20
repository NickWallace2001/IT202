<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
if (isset($_GET["survey_id"])) {
    $sid = $_GET["survey_id"];
}
?>
<?php
if (isset($_POST["save"])) {
	$question = $_POST["question"];
	$survey = $_POST["survey_id"];
	if ($survey <= 0) {
		$survey = null;
	}
	$user = get_user_id();
	$db = getDB();
	if (isset($id)) {
		$stmt = $db->prepare("UPDATE Questions set question=:question, survey_id=:survey where id=:id");
		$r = $stmt->execute([
			":question" => $question,
			":survey" => $survey,
			":id" => $id
		]);
		if ($r) {
			//flash("Updated successfully with id: " . $id);
			die(header("Location: test_edit_survey.php?id=$survey"));
		}
		else{
			$e = $stmt->errorInfo();
			flash("Error updating: " . var_export($e, true));
		}
	}
	else {
		flash("ID isn't set, we need an ID in order to update");
	}
}
elseif (isset($_POST["deletea"])) {
    $itemID = $_POST["deletea"];
    deleteAnswer($itemID);
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Questions where id = :id");
	$r = $stmt->execute([":id" => $id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$qid = $result["id"];


    $stmt = $db->prepare("SELECT id,title,user_id from Survey LIMIT 10");
    $r = $stmt->execute();
    $surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $db->prepare("SELECT id, answer, question_id FROM Answers where question_id=:qid");
    $r = $stmt->execute([":qid" => $qid]);
    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$i=1;

?>
<div class="container-fluid">
	<h3>Edit Question</h3>
	<form method="POST">
        <div class="form-group">
		    <label>Question</label>
		    <input class="form-control" name="question" placeholder="Question" value="<?php echo $result["question"]; ?>"/>
        </div>
        <div class="form-group">
		    <label>Survey</label>
            <h6>Notice: Changing this will remove this question from the listed survey; instead, create a new question with the same name. Click Update to go back</h6>
		    <select class="form-control" name="survey_id" value="<?php echo $result["survey_id"];?>">
			    <option value="-1">None</option>
			    <?php foreach ($surveys as $survey): ?>
                    <?php if ($survey["user_id"] == $result["user_id"]): ?>
				        <option value="<?php safer_echo($survey["id"]); ?>" <?php echo ($result["survey_id"] == $survey["id"] ? 'selected="selected"' : '');?>
				        ><?php safer_echo($survey["title"]); ?></option>
                    <?php endif; ?>
			    <?php endforeach; ?>
		    </select>
        </div>
		<input class="btn btn-primary" type="submit" name="save" value="Update"/>
	</form>
    <div class="results">
        <?php if (count($answers) > 0): ?>
            <div class="list-group">
                <?php foreach ($answers as $answer): ?>
                    <div class="list-group-item">
                        <div class="h2"><?php echo "Answer" . $i; ?></div>
                        <div class="h5 justify-content-center text-center"><?php safer_echo($answer["answer"]); ?></div>
                        <form method="POST">
                            <div>
                                <p align="right">
                                    <input type="hidden" name="deletea" value="<?php echo($answer["id"]); ?>"/>
                                    <input class="btn btn-danger " type="submit" value="X"/>
                                    <a class="btn btn-success" type="button" href="test_edit_answer.php?id=<?php safer_echo($answer["id"]); ?>">Edit Answer <?php echo $i++; ?></a>
                                </p>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Please add answers</p>
        <?php endif; ?>
    </div>
    <br>
    <a class="btn btn-primary" type="button" href="test_create_answer.php?id=<?php echo($qid); ?>&survey_id=<?php safer_echo($sid); ?>">Add Answers</a>
</div>

<?php require(__DIR__ . "/../partials/flash.php");
