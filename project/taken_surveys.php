<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$per_page = 10;
$db = getDB();

$query = "SELECT count(Responses.survey_id as GroupId, Responses.survey_id as rsurveyId, Survey.id as ssurveyId, Survey.title as SurveyTitle, Survey.user_id as suid, Responses.user_id) as total FROM Responses JOIN Survey on Responses.survey_id = Survey.id where Responses.user_id = :user_id";
$params = [":user_id" => get_user_id()];
paginate($query, $params, $per_page);
$stmt = $db->prepare("SELECT Responses.survey_id as GroupId, Responses.survey_id as rsurveyId, Survey.id as ssurveyId, Survey.title as SurveyTitle, Survey.user_id as suid, Responses.user_id FROM Responses JOIN Survey on Responses.survey_id = Survey.id where Responses.user_id = :user_id LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":user_id", get_user_id());
$r = $stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}

/*
$stmt = $db->prepare("SELECT Responses.survey_id as GroupId, Responses.survey_id as rsurveyId, Survey.id as ssurveyId, Survey.title as SurveyTitle, Survey.user_id as suid, Responses.user_id FROM Responses JOIN Survey on Responses.survey_id = Survey.id where Responses.user_id = :user_id");
$r = $stmt->execute([":user_id" => get_user_id()]);
*/
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
echo "<pre>" . var_export($responses, true) . "</pre>";
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
                                        <a class="btn btn-info" type="button" href="view_profile.php?id=<?php safer_echo($index[2]); ?>">View Creator's Profile</a>
                                    </div>
                                    <div class="col">
                                        <?php if (has_role("Admin")): ?>
                                            <a class="btn btn-warning" type="button" href="edit_survey.php?id=<?php safer_echo($index[0]); ?>">Edit</a>
                                        <?php endif; ?>
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
        <?php include(__DIR__."/partials/pagination.php");?>
    </div>
<?php require(__DIR__ . "/partials/flash.php"); ?>