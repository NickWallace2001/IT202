<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$per_page = 10;
$db = getDB();

$query = "SELECT count(distinct title) as total from Survey s JOIN Responses r on s.id = r.survey_id where r.user_id = :user_id";
$params = [":user_id" => get_user_id()];
paginate($query, $params, $per_page);
$stmt = $db->prepare("Select distinct title, s.id, s.user_id, (select count(distinct user_id) from Responses where Responses.survey_id = s.id) as taken from Survey s JOIN Responses r on s.id = r.survey_id where r.user_id = :user_id LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":user_id", get_user_id());
$r = $stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}

$responses = [];
if ($r){
    $outcome = $stmt->fetchAll(PDO::FETCH_ASSOC);
    /*
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
    */
}

//echo "<pre>" . var_export($outcome, true) . "</pre>";
//echo "<pre>" . var_export($responses, true) . "</pre>";
//echo "<pre>" . var_export($taken, true) . "</pre>";
?>
    <div class="container-fluid">
        <h3>Surveys You've Taken</h3>
        <div class="results">
            <?php if (count($outcome) > 0): ?>
            <div class="list-group">
                <?php foreach ($outcome as $index): ?>
                    <div class="list-group-item">
                                <div class="row">
                                    <div class="col">
                                        <div>Title:</div>
                                        <div><?php safer_echo($index["title"]); ?></div>
                                    </div>
                                    <div class="col">
                                        <a class="btn btn-info" type="button" href="view_profile.php?id=<?php safer_echo($index["user_id"]); ?>">View Creator's Profile</a>
                                    </div>
                                    <div class="col">
                                        <?php if (has_role("Admin")): ?>
                                            <a class="btn btn-warning" type="button" href="edit_survey.php?id=<?php safer_echo($index["id"]); ?>">Edit</a>
                                        <?php endif; ?>
                                        <a class="btn btn-primary" type="button" href="results.php?id=<?php safer_echo($index["id"]); ?>">View Results</a>
                                    </div>
                                    <div class="col">
                                        <div>Times Taken:</div> <div><?php safer_echo($index["taken"]); ?></div>
                                    </div>
                                </div>
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