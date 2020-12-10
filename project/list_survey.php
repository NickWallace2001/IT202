<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])){
    $query = $_POST["query"];
}
?>
<?php

if (isset($_POST["search"]) && !empty($query) && has_role("Admin")){
$db = getDB();
$stmt = $db->prepare("SELECT * FROM Survey Where (title like :q) LIMIT 10");
$r = $stmt->execute([":q" => "%$query%"]);
if ($r) {
    $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
}
else{
    flash("There was a problem fetching the results");
}
}
elseif (isset($_POST["search"]) && !empty($query)){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Survey Where visibility = 2 AND (title like :q) LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
    }
    else{
        flash("There was a problem fetching the results");
    }
}
elseif (has_role("Admin")){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Survey LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
    }
    else{
        flash("There was a problem fetching the results admin");
    }
}
else{
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Survey Where visibility = 2 LIMIT 10");
    $r = $stmt->execute();
    if ($r) {
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
    }
    else{
        flash("There was a problem fetching the results");
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
?>
<div class="container-fluid">
    <h3>List Surveys</h3>
    <form method="POST" class="form-inline">
        <input class="form-control" name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
        <input class="btn btn-primary" type="submit" value="Search" name="search"/>
    </form>

    <div class="results">
        <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
            <div class="list-group-item">
                <?php foreach($taken as $ind): ?>
                    <?php if ($ind["title"] == $r["title"]): ?>
                        <div class="row">
                            <div class="col">
                                <div>Title:</div>
                                <div><?php safer_echo($r["title"]); ?></div>
                            </div>
                            <?php if (has_role("Admin")): ?>
                                <div class="col">
                                    <div>Visibility:</div>
                                    <div><?php safer_echo(getVisibility($r["visibility"])); ?></div>
                                </div>
                            <?php endif; ?>
                            <div class="col">
                                <a class="btn btn-info" type="button" href="view_profile.php?id=<?php safer_echo($r['user_id']); ?>&query=<?php echo $query ?>">View Creator's Profile</a>
                            </div>
                            <div class="col">
                                <?php if (has_role("Admin")): ?>
                                    <a class="btn btn-warning" type="button" href="edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                                <?php endif; ?>
                                <?php if ($r["visibility"] == 2): ?>
                                    <a class="btn btn-success" type="button" href="take_survey.php?id=<?php safer_echo($r['id']); ?>">Take Survey</a>
                                <?php endif; ?>
                                <?php if (intval($ind["total"]) > 0): ?>
                                    <a class="btn btn-primary" type="button" href="results.php?id=<?php safer_echo($r['id']); ?>">View Results</a>
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
        </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>