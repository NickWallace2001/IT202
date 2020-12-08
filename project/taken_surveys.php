<?php require_once(__DIR__ . "/partials/nav.php"); ?>
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

//$stmt = $db->prepare("SELECT survey_id as GroupId, question_id as QuestionId, survey_id as SurveyId, answer_id as AnswerId, user_id FROM Responses where user_id = :user_id");
$stmt = $db->prepare("SELECT Responses.survey_id as GroupId, Responses.survey_id as rsurveyId, Survey.id as ssurveyId, Survey.title as SurveyTitle, Responses.user_id FROM Responses JOIN Survey on Responses.survey_id = Survey.id where Responses.user_id = :user_id LIMIT 10");
$r = $stmt->execute([":user_id" => get_user_id()]);
$responses = [];
if ($r){
    $outcome = $stmt->fetchAll(PDO::FETCH_GROUP);

    if ($outcome){
        foreach ($outcome as $index => $group){
            foreach ($group as $details){
                $sid = intval($details["ssurveyId"]);
                $stitle = $details["SurveyTitle"];

                if (!isset($responses[$sid])){
                    $responses[$sid] = [];
                }
                //array_push($responses[$sid], $sid);
            }
            array_push($responses[$sid], $sid);
            array_push($responses[$sid], $stitle);
        }
    }
}

$stmt = $db->prepare("SELECT Survey.title, Survey.id, count(Responses.survey_id) as total FROM Survey LEFT JOIN (SELECT distinct user_id, survey_id FROM Responses) as Responses on Survey.id = Responses.survey_id GROUP BY title");
$r = $stmt->execute();
if ($r){
    $taken = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else{
    flash("There was a problem fetching the results");
}

//echo "<pre>" . var_export($outcome, true) . "</pre>";
//echo "<pre>" . var_export($responses, true) . "</pre>";
//echo "<pre>" . var_export($taken, true) . "</pre>";
?>
    <div class="container-fluid">
        <h3>Surveys You've Taken</h3>
        <div class="results">
            <?php if (count($responses) > 0): ?>
            <div class="list-group">
                <?php foreach ($responses as $index): ?>
                    <div class="list-group-item">
                        <?php foreach($taken as $ind): ?>
                            <?php if ($ind["title"] == $index[1]): ?>
                                <div class="row">
                                    <div class="col">
                                        <div>Title:</div>
                                        <div><?php safer_echo($index[1]); ?></div>
                                    </div>
                                    <div class="col">
                                        <a class="btn btn-primary" type="button" href="results.php?id=<?php safer_echo($index[0]); ?>">View Results</a>
                                    </div>
                                    <div class="col">
                                        <div>Times Taken:</div> <div><?php safer_echo($ind["total"]); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <p>No results</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php require(__DIR__ . "/partials/flash.php"); ?>