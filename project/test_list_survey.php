<?php require_once(__DIR__ . "/partials/nav.php");?>
<?php
if (!has_role("Admin")){
	flash("You don't have permission to access this page");
	die(header("Location: login.php"));
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
	$stmt = $db->prepare("SELECT id, title, description, visibility from Survey WHERE title like :q LIMIT 10");
	$r = $stmt->execute([":q" => "%$query%"]);
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
						<a type="button" href="test_edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
						<a type="button" href="test_view_survey.php?id=<?php safer_echo($r['id']); ?>">View</a>
                                </div>
                        <?php endforeach; ?>
                </div>
        <?php else: ?>
                <p>No results</p>
        <?php endif; ?>
</div>
