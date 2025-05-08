<?php

require_once "functions.php";

////classes////
// get all classes
function getClasses(){
    $db = dbConnect();

    $sql = "SELECT * FROM class";

    $resourse = $db->query($sql) or die($db->error);

    $classes = $resourse->fetch_all(MYSQLI_ASSOC);

    return $classes;
}

// display all classes
function displayClasses() {
    $classes = getClasses();

    foreach($classes as $class) {
        ?>
            <a href="?classId=<?php echo $class['classId']; ?>">
                <div>
                    <h1><?php echo $class['className'] ?></h1>
                    <p><?php echo$class['classShortInformation'];  ?></p>
                </div>
            </a>
        <?php
    }
}

// get a singular class
function getClass($classId) {
    $db = dbConnect();

    $sql = "SELECT * FROM class WHERE classId =" . $classId;

    $resourse = $db->query($sql) or die($db->error);

    $class = $resourse->fetch_assoc();

    return $class;
}

// display a singular class
function displayClass($classId) {
    $class = getclass($classId);

    ?>
        <h1><?php echo $class['className'] ?></h1>
        <p><?php echo nl2br($class['classInformation']);  ?></p>
        <img src="<?php echo $class['classImage'];?>">
    <?php
}
?>