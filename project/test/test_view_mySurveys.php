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
$stmt = $db->prepare("SELECT * FROM Survey Where Survey.user_id = :user_id");
$r = $stmt->execute([":user_id" => $user]);
if ($r) {
    $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
}
else{
    flash("There was a problem fetching the results");
}
?>

<h3>Your Surveys</h3>
<div class="results">
    <?php if (count($results) > 0): ?>
    <div class="list-group">
        <?php foreach ($results as $r): ?>
        <div class="list-group-item">
            <div>
                <div>Title: <?php safer_echo($r["title"]); ?></div>
            </div>
            <div>
                <div>Description: <?php safer_echo($r["description"]); ?></div>
            </div>
            <div>
                <div>Visibility: <?php getVisibility($r["visibility"]); ?></div>
            </div>
            <div>
                <a type="button" href="test_edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                <a type="button" href="test_view_survey.php?id=<?php safer_echo($r['id']); ?>">View</a>
            </div>
            <br>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
