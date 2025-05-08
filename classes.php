<?php
    function dbConnect(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dnm";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: ". $conn->connect_error);
        }
        
        return $conn;
    }

    function getClasses(){
        $db = dbConnect();

        $sql = "SELECT * FROM class";

        $resourse = $db->query($sql) or die($db->error);

        $classes = $resourse->fetch_all(MYSQLI_ASSOC);

        return $classes;
    }

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

    function getClass($classId) {
        $db = dbConnect();

        $sql = "SELECT * FROM class WHERE classId =" . $classId;

        $resourse = $db->query($sql) or die($db->error);

        $class = $resourse->fetch_assoc();

        return $class;
    }
    
    function displayClass($classId) {
        $class = getclass($classId);

        ?>
            <h1><?php echo $class['className'] ?></h1>
            <p><?php echo nl2br($class['classInformation']);  ?></p>
            <img src="<?php echo $class['classImage'];?>">
        <?php
    }
?>
<!DOCTYPE html>
<html>
    <head>
        
    </head>
    <body>
        <?php
        if (isset($_GET['classId']) && is_numeric($_GET['classId'])) {
            displayClass($_GET['classId']);

            displayClasses();
        } else {
            displayClasses();
        }
        ?>
    </body>
</html>