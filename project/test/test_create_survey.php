<?php require_once(__DIR__. "/../partials/nav.php");?>
<?php

if (!has_role("Admin")){
	flash("You don't have permission to access this page");
	die(header("Location: ../login.php"));
}
?>

<div class="container-fluid">
    <h3>Create Survey</h3>
    <form method= "POST">
        <div class="form-group">
            <label>Title</label>
            <input class="form-control" name="title" placeholder="Title"/>
        </div>
        <div class="form-group">
            <label>Description</label>
            <input class="form-control" name="description" placeholder="Description"/>
        </div>
        <div class="form-group">
            <label>Visibility</label>
            <select class="form-control" name="visibility">
                <option value="0">Draft</option>
            </select>
        </div>
        <input class="btn btn-primary" type="submit" name="save" value="Add Questions"/>
    </form>
</div>

<?php
if(isset($_POST["save"])){
	$title = $_POST["title"];
	$description = $_POST["description"];
	$visibility = $_POST["visibility"];
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Survey (title, description, visibility, user_id) VALUES(:title, :description, :visibility, :user)");
	$r = $stmt->execute([
		":title"=>$title,
		":description"=>$description,
		":visibility"=>$visibility,
		":user"=>$user
]);

if($r){
	//flash("Created successfully wth id: " . $db->lastInsertID());
    $sid = $db->lastInsertId();
    die(header("Location:test_create_question.php?id=$sid"));
}
else{
	$e = $stmt->errorInfo();
	flash("Error creating: " . var_export($e, true));
}
}
?>
<?php require(__DIR__. "/../partials/flash.php");?>
