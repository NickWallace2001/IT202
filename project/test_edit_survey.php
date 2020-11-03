<?php require_once(__DIR__ . "/partials/nav.php");?>
<?php
if (!has_role("Admin")) {
	flash("You don't have permission to access this page");
	die(header("Location: login.php"));
}
?>
<?php
if(isset($_GET["id"])){
	$id = $_GET["id"];
}
?>
<?php
if(isset($_POST["save"])){
	$title = $_POST["title"];
	$description = $_POST["description"];
	$visibility = $_POST["visibility"];
	$user = get_user_id();
	$db = getDB();
	if(isset($id)){
		$stmt = $db->prepare("UPDATE Survey set title=:title, description=:description, visibility=:visibility where id=:id");
		$r = $stmt->execute([
			":title"=>$title,
			":description"=>$description,
			":visibility"=>$visibility,
			":id"=>$id
		]);
		if($r){
			flash("Updated successfully with id: " . $id);
		}
		else{
			$e = $stmt->errorInfo();
			flash("Error updating: " . var_export($e, true));
		}
	}
	else{
		flash("ID isn't set, we need an ID in order to update");
	}
}
?>
<?php
//fetching
$result = [];
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Survey where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form method="POST">
	<label>Title</label>
	<input name="title" placeholder="Title" value="<?php echo $result["title"];?>"/>
	<label>Description</label>
        <input name="description" placeholder="Description" value="<?php echo $result["description"];?>"/>
	<select name="visibility" value="<?php echo $result["visibility"];?>">
		<option value="0" <?php echo ($result["visibility"] == "0"?'selected=selected"selected"':'');?>>Draft</option>
		<option value="1" <?php echo ($result["visibility"] == "1"?'selected=selected"selected"':'');?>>Private</option>
		<option value="2" <?php echo ($result["visibility"] == "2"?'selected=selected"selected"':'');?>>Public</option>
	</select>
        <input type="submit" name="save" value="Update"/>
</form>

<?php require(__DIR__ . "/partials/flash.php");?>
