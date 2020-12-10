<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (isset($_GET["id"])){
    $id = $_GET["id"];
}
if (isset($_GET["username"])){
    $username = $_GET["username"];
}
?>
<?php
$db = getDB();
$stmt = $db->prepare("SELECT Responses.survey_id as GroupId, Responses.survey_id as rsurveyId, Survey.id as ssurveyId, Survey.title as SurveyTitle, Survey.user_id as suid, Responses.user_id FROM Responses JOIN Survey on Responses.survey_id = Survey.id where Responses.user_id = :user_id LIMIT 10");
$r = $stmt->execute([":user_id" => $id]);
$responses = [];
if ($r){
    $outcome = $stmt->fetchAll(PDO::FETCH_GROUP);

    if ($outcome){
        foreach ($outcome as $index => $group){
            foreach ($group as $details){
                $sid = intval($details["ssurveyId"]);
                $stitle = $details["SurveyTitle"];
                $suid = $details["suid"];

                if (!isset($responses[$sid])){
                    $responses[$sid] = [];
                }
                //array_push($responses[$sid], $sid);
            }
            array_push($responses[$sid], $sid);
            array_push($responses[$sid], $stitle);
            array_push($responses[$sid], $suid);
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
        <h3>Surveys <?php echo $username ?> Has Taken</h3>
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
                                        <?php if (has_role("Admin")): ?>
                                            <a class="btn btn-warning" type="button" href="edit_survey.php?id=<?php safer_echo($index[0]); ?>">Edit</a>
                                        <?php endif; ?>
                                        <?php if (intval($ind["total"]) == 0): ?>
                                            <a class="btn btn-success" type="button" href="take_survey.php?id=<?php safer_echo($index[2]); ?>">Take Survey</a>
                                        <?php endif; ?>
                                        <?php if (intval($ind["total"]) > 0): ?>
                                            <a class="btn btn-primary" type="button" href="results.php?id=<?php safer_echo($index[2]); ?>">View Results</a>
                                        <?php endif; ?>
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