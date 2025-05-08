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

    function getraces(){
        $db = dbConnect();

        $sql = "SELECT * FROM race";

        $resourse = $db->query($sql) or die($db->error);

        $races = $resourse->fetch_all(MYSQLI_ASSOC);

        return $races;
    }

    function displayraces() {
        $races = getraces();

        foreach($races as $race) {
            ?>
                <a href="?raceId=<?php echo $race['raceId']; ?>">
                    <div>
                        <h1><?php echo $race['raceName'] ?></h1>
                        <p><?php echo$race['raceShortInformation'];  ?></p>
                    </div>
                </a>
            <?php
        }
    }

    function getrace($raceId) {
        $db = dbConnect();

        $sql = "SELECT * FROM race WHERE raceId =" . $raceId;

        $resourse = $db->query($sql) or die($db->error);

        $race = $resourse->fetch_assoc();

        return $race;
    }
    
    function displayrace($raceId) {
        $race = getrace($raceId);

        ?>
            <h1><?php echo $race['raceName'] ?></h1>
            <p><?php echo nl2br($race['raceInformation']);  ?></p>
            <img src="<?php echo $race['raceImage'];?>">
        <?php
    }
?>
<!DOCTYPE html>
<html>
    <head>
        
    </head>
    <body>
        <?php
        if (isset($_GET['raceId']) && is_numeric($_GET['raceId'])) {
            displayrace($_GET['raceId']);

            displayraces();
        } else {
            displayraces();
        }
        ?>
    </body>
</html>