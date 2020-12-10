<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (isset($_GET["pid"])){
    $pid = $_GET["pid"];
    $_SESSION["pid"] = $pid;
}
elseif (isset($_SESSION["pid"])){
    $pid = $_SESSION["pid"];
}
if (isset($_GET["pusername"])){
    $pusername = $_GET["pusername"];
    $_SESSION["pusername"] = $pusername;
}
elseif (isset($_SESSION["pusername"])){
    $pusername = $_SESSION["pusername"];
}
?>
<?php
$per_page = 10;
$db = getDB();

$query = "SELECT count(distinct title) as total from Survey s JOIN Responses r on s.id = r.survey_id where r.user_id = :user_id";
$params = [":user_id" => $pid];
paginate($query, $params, $per_page);

$stmt = $db->prepare("Select distinct title, s.id, s.user_id, (select count(distinct user_id) from Responses where Responses.survey_id = s.id) as taken from Survey s JOIN Responses r on s.id = r.survey_id where r.user_id = :user_id LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":user_id", $pid);
$r = $stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$responses = [];
if ($r){
    $outcome = $stmt->fetchAll(PDO::FETCH_ASSOC);
    /*
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
        <h3>Surveys <?php echo $pusername ?> Has Taken</h3>
        <a class="btn btn-secondary" type="button" href="view_profile.php?id=<?php safer_echo($pid); ?>">Back to profile</a>
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
                                        <?php if (has_role("Admin")): ?>
                                            <a class="btn btn-warning" type="button" href="edit_survey.php?id=<?php safer_echo($index[0]); ?>">Edit</a>
                                        <?php endif; ?>
                                        <?php if (intval($index["taken"]) == 0): ?>
                                            <a class="btn btn-success" type="button" href="take_survey.php?id=<?php safer_echo($index["id"]); ?>">Take Survey</a>
                                        <?php endif; ?>
                                        <?php if (intval($index["taken"]) > 0): ?>
                                            <a class="btn btn-primary" type="button" href="results.php?id=<?php safer_echo($index["id"]); ?>">View Results</a>
                                        <?php endif; ?>
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