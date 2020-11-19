<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php require_once(__DIR__ . "/../lib/helpers.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
if(isset($_GET["id"])){
    $id = $_GET["id"];
}

if(isset($_GET["survey_id"])){
    $sid = $_GET["survey_id"];
}
?>
<div class="container-fluid">
    <h3>Create Answer</h3>
    <form method="POST">
        <div class="form-group">
            <label>Answer</label>
            <input class="form-control" name="answer" placeholder="Answer"/>
        </div>
        <input class="btn btn-light" type="submit" name="save" value="Add Answer"/>
        <a class="btn btn-primary" type="button" href="test_create_question.php?id=<?php echo($sid); ?>">Add new question</a>
    </form>
</div>

<?php
if (isset($_POST["save"])) {
    $answer = $_POST["answer"];
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Answers (answer, question_id, user_id) VALUES(:answer, :question_id, :user)");
    $r = $stmt->execute([
        ":answer" => $answer,
        ":question_id" => $id,
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