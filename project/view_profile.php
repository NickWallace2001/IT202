<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (isset($_GET["id"])){
    $id = $_GET["id"];
}
?>
<?php
$result = [];
if (isset($id)){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Users where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt ->fetch(PDO::FETCH_ASSOC);
    if (!$result){
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
<?php if ($result["visibility"] == 2): ?>
    <h2><?php echo $result["username"] . "'s Profile" ?></h2>
        <?php if (isset($result) && !empty($result)): ?>
            <div class="card">
                <div class="card-title">
                    <h3>About <?php $result["username"]; ?></h3>
                </div>
                <div class="card-body">
                    <div>
                        <!--<p>About</p>-->
                        <?php if ($result["visibility"] == 1): ?>
                            <div>
                                Email: <?php safer_echo($result["email"]); ?>
                            </div>
                        <?php endif; ?>
                        <div>Profile Visibility: <?php getVisibility($result["visibility"]); ?></div>
                        <div>Created on: <?php safer_echo($result["created"]); ?></div>
                        <div>
                            <a class="btn btn-primary" type="button" href="profile_surveys_created.php?pid=<?php echo($id); ?>&pusername=<?php echo $result["username"]; ?>">Surveys Created</a>
                            <a class="btn btn-success" type="button" href="profile_surveys_taken.php?pid=<?php echo($id); ?>&pusername=<?php echo $result["username"]; ?>">Surveys Taken</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>Error looking up id...</p>
        <?php endif; ?>
<?php elseif ($result["visibility"] == 1 && get_user_id() == $id): ?>
    <h2><?php echo $result["username"] . "'s Profile" ?></h2>
    <?php if (isset($result) && !empty($result)): ?>
        <div class="card">
            <div class="card-title">
                <h3>About <?php $result["username"]; ?></h3>
            </div>
            <div class="card-body">
                <div>
                    <!--<p>About</p>-->
                    <?php if ($result["visibility"] == 1): ?>
                        <div>
                            Email: <?php safer_echo($result["email"]); ?>
                        </div>
                    <?php endif; ?>
                    <div>Profile Visibility: <?php getVisibility($result["visibility"]); ?></div>
                    <div>Created on: <?php safer_echo($result["created"]); ?></div>
                    <div>
                        <a class="btn btn-primary" type="button" href="profile_surveys_created.php?pid=<?php echo($id); ?>&pusername=<?php echo $result["username"]; ?>">Surveys Created</a>
                        <a class="btn btn-success" type="button" href="profile_surveys_taken.php?pid=<?php echo($id); ?>&pusername=<?php echo $result["username"]; ?>">Surveys Taken</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p>Error looking up id...</p>
    <?php endif; ?>
<?php else: ?>
    <h2><?php echo $result["username"] . "'s Profile is Private" ?></h2>
<?php endif; ?>
<?php require_once(__DIR__ . "/partials/flash.php");
