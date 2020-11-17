<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
	$query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
	$db = getDB();
	$stmt = $db->prepare("SELECT Questions.id,Questions.question,Survey.title, Users.username from Questions JOIN Users on Questions.user_id = Users.id LEFT JOIN Survey on Questions.survey_id = Survey.id WHERE Questions.question like :q LIMIT 10");
	$r = $stmt->execute([":q" => "%$query%"]);
	if ($r) {
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	else {
		flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
	}
}
?>
<div class="container-fluid">
    <h3>List Questions</h3>
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
						        <div>Question:</div>
						        <div><?php safer_echo($r["question"]); ?></div>
					        </div>
					        <div class="col">
						        <div>Survey:</div>
						        <div><?php safer_echo($r["title"]); ?></div>
					        </div>
					        <div class="col">
						        <div>Creator:</div>
						        <div><?php safer_echo($r["username"]); ?></div>
					        </div>
					        <div class="col">
						        <a class="btn btn-warning" type="button" href="test_edit_question.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
						        <a class="btn btn-success" type="button" href="test_view_question.php?id=<?php safer_echo($r['id']); ?>">View</a>
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
