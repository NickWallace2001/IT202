<?php require_once(__DIR__ . "/../partials/nav.php");
if (!has_role("Admin")){
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
$results = [];

$db = getDB();
$user = get_user_id();
$stmt = $db->prepare("SELECT * FROM Survey Where Survey.user_id = :user_id LIMIT 10");
$r = $stmt->execute([":user_id" => $user]);
if ($r) {
    $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
}
else{
    flash("There was a problem fetching the results");
}
?>

<div class="container-fluid">
    <h3>Your Surveys</h3>
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
                            <a class="btn btn-warning" type="button" href="test_edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                            <a class="btn btn-success" type="button" href="test_view_survey.php?id=<?php safer_echo($r['id']); ?>">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
                <p>No results</p>
            <?php endif; ?>
        </div>
    </div>
</div>