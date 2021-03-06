<!-- Visa ett historgram för alla bitar i satserna  -->
<?php
    $connection	=	mysqli_connect("mysql.itn.liu.se","lego", "", "lego"); //Connect to Lego database
    if (!$connection){
        die("<p>MySQL error:</p> <p>" . mysqli_error($connection) . "</p>"); //Error message if connection failed
    }

    $data = "SELECT DISTINCT Partname, SUM(Quantity) FROM inventory, parts WHERE inventory.SetID='$_POST' AND ItemID=PartID GROUP BY Partname ORDER BY Partname ASC";
    $contents = mysqli_query($connection, $data);

    $things = [];

    while ($row = mysqli_fetch_row($contents)) {
        $things[] = $row;
    }

    for($i = 0; $i < count($things); $i++){
        $things[$i]['text'] = $things[$i][0];
        $things[$i]['number'] = $things[$i][1];
        unset($things[$i][0]);
        unset($things[$i][1]);
    }

    //query for set information
    $titleData = "SELECT Setname, SetID FROM sets WHERE sets.SetID='$_POST'";
    $titleQuery = mysqli_query($connection, $titleData);
    $title = mysqli_fetch_assoc($titleQuery);

    //get set URL
    $prefix = "http://www.itn.liu.se/~stegu76/img.bricklink.com/";
    $SetID = $title['SetID'];
    $filename = "SL/$SetID.jpg";
    $setURL = "$prefix$filename";
    //assign proper data structure for js rendering
    $legoData = [];
    $legoData['title'] = $title['Setname'];
    $legoData['data'] = $things;
    $legoData['dataType'] = "pieces";
    $legoData['url'] = $setURL;

    $totalsResultString = addslashes(json_encode($legoData));

    // Send the results to javascript for rendering
    echo "<script>",
    "createGraph('histogram','$totalsResultString', '.statistics');",
    "</script>"
    ;
?>
