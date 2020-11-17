<?php require_once(__DIR__ . "/../partials/nav.php");
if (!has_role("Admin")){
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
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
                <div class="row">
                    <div class="col">
                        <div>Title:</div>
                        <div><?php safer_echo($r["title"]); ?></div>
                    </div>
                    <div class="col">
                        <div>Description:</div>
                        <div><?php safer_echo($r["description"]); ?></div>
                    </div>
                    <div class="col">
                        <div>Visibility:</div>
                        <div><?php getVisibility($r["visibility"]); ?></div>
                    </div>
                    <div class="col">
                        <?php if ($r["user_id"] == get_user_id()):?>
                        <a class="btn btn-warning" type="button" href="test_edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                        <?php endif; ?>
                        <a class="btn btn-success" type="button" href="test_view_survey.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
</div>
