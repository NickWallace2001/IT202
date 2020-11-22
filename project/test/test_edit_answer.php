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
if (isset($_GET["question_id"])) {
    $qid = $_GET["question_id"];
}
if (isset($_GET["survey_id"])) {
    $sid = $_GET["survey_id"];
}
?>
<?php
if (isset($_POST["save"])) {
    $answer = $_POST["answer"];
    $question = $_POST["question_id"];
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE Answers set answer=:answer, question_id=:question where id=:id");
        $r = $stmt->execute([
            ":question" => $question,
            ":answer" => $answer,
            ":id" => $id
        ]);
        if ($r) {
            //flash("Updated successfully with id: " . $id);
            die(header("Location: test_edit_question.php?id=$question&survey_id=$sid"));
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
    $stmt = $db->prepare("SELECT * FROM Answers where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $aid = $result["id"];


    $stmt = $db->prepare("SELECT id,question, user_id from Questions LIMIT 10");
    $r = $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

$i=1;

?>
    <div class="container-fluid">
        <h3>Edit Answer</h3>
        <form method="POST">
            <div class="form-group">
                <label>Answer</label>
                <input class="form-control" name="answer" placeholder="Answer" value="<?php echo $result["answer"]; ?>"/>
            </div>
            <div class="form-group">
                <label>Survey</label>
                <h6>Notice: Changing this will remove this answer from the listed question; instead, create a new answer with the same name. Click Update to go back</h6>
                <select class="form-control" name="question_id" value="<?php echo $result["question_id"];?>">
                    <option value="-1">None</option>
                    <?php foreach ($questions as $question): ?>
                        <?php if ($question["user_id"] == $result["user_id"]): ?>
                            <option value="<?php safer_echo($question["id"]); ?>" <?php echo ($result["question_id"] == $question["id"] ? 'selected="selected"' : '');?>
                            ><?php safer_echo($question["question"]); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <input class="btn btn-primary" type="submit" name="save" value="Update"/>
        </form>
    </div>

<?php require(__DIR__ . "/../partials/flash.php");
