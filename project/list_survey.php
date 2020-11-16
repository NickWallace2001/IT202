<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])){
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)){
    $db = getDB();
    $user = get_user_id();
    $stmt = $db->prepare("SELECT * FROM Survey Where (visibility = 2 OR (visibility < 2 and Survey.user_id = :user_id)) AND Survey.title like :q");
    $r = $stmt->execute([
        ":user_id" => $user,
        ":q" => "%$query%"
    ]);
    if ($r) {
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
    }
    else{
        flash("There was a problem fetching the results");
    }
}
?>

<form method="POST">
    <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>

<div class="results">
    <?php if (count($results) > 0): ?>
    <div class="list-group">
        <?php foreach ($results as $r): ?>
        <div class="list-group-item">
            <div>
                <div>Title:</div>
                <div><?php safer_echo($r["title"]); ?></div>
            </div>
            <div>
                <div>Description:</div>
                <div><?php safer_echo($r["description"]); ?></div>
            </div>
            <div>
                <div>Visibility:</div>
                <div><?php getVisibility($r["visibility"]); ?></div>
            </div>
            <div>
                <?php if ($r["user_id"] == get_user_id()):?>
                    <a type="button" href="edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                <?php endif; ?>
                <a type="button" href="view_survey.php?id=<?php safer_echo($r['id']); ?>">View</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
