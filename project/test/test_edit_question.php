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
			flash("Updated successfully with id: " . $id);
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
}
$db = getDB();
$stmt = $db->prepare("SELECT id,title from Survey LIMIT 10");
$r = $stmt->execute();
$surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
		    <select class="form-control" name="survey_id" value="<?php echo $result["survey_id"];?>">
			    <option value="-1">None</option>
			    <?php foreach ($surveys as $survey): ?>
				    <option value="<?php safer_echo($survey["id"]); ?>" <?php echo ($result["survey_id"] == $survey["id"] ? 'selected="selected"' : '');?>
				    ><?php safer_echo($survey["title"]); ?></option>
			    <?php endforeach; ?>
		    </select>
        </div>
		<input class="btn btn-primary" type="submit" name="save" value="Update"/>
	</form>
</div>

<?php require(__DIR__ . "/../partials/flash.php");
