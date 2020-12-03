<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
$results = [];

$db = getDB();
$user = get_user_id();
$stmt = $db->prepare("SELECT * From Survey");
$r = $stmt->execute([":user_id" => $user]);
if ($r){
    $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
}
else{
    flash("There was a problem fetching the results");
}

$stmt = $db->prepare("SELECT survey_id as GroupId, question_id as QuestionId, survey_id as SurveyId, answer_id as AnswerId, user_id FROM Responses where user_id = :user_id");
$r = $stmt->execute([":user_id" => get_user_id()]);
$responses = [];
if ($r){
    $outcome = $stmt->fetchAll(PDO::FETCH_GROUP);

    if ($outcome){
        foreach ($outcome as $index => $group){
            foreach ($group as $details){
                $qid = $details["QuestionId"];
                $answer = intval($details["AnswerId"]);
                $sid = intval($details["SurveyId"]);

                if (!isset($responses[$sid])){
                    $responses[$sid] = [];
                }
                array_push($responses[$sid], $answer);
            }
        }
    }
}

echo "<pre>" . var_export($outcome, true) . "</pre>";
?>

<div class="container-fluid">
    <h3>Surveys You've Taken</h3>
    <div class="results">
        <?php if (count($outcome) > 0): ?>
        <div class="list-group">
            <?php foreach ($outcome as $ind): ?>
                <div class="list-group-item">
                    <div class="row">
                        <?php foreach ($results as $r): ?>
                            <?php foreach ($ind as $o): ?>
                                <?php if ($o["SurveyId"] == $r["id"]): ?>
                        <div class="col">
                            <div>Title:</div>
                            <div><?php safer_echo($r["title"]); ?></div>
                        </div>
                        <div class="col">
                            <a class="btn btn-primary" type="button" href="results.php?id=<?php safer_echo($r['id']); ?>">View Results</a>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endforeach; ?>
            <?php else: ?>
                <p>No results</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>