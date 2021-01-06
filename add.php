<!DOCTYPE html>

<html>
            
<head>
  <title>Add</title>
    <style>
        body {
            background-color: #e7e7e7;
        }
    </style>
</head>

<body>
    
        <?php       
			$con = mysqli_connect("localhost","root","","test");
				
				
            if (mysqli_connect_errno())
				{
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}
             
        
			$piece = $_POST["artpiece"];
			echo $piece . '<br>';
            $fname = $_POST["fname"];
            echo $fname . '<br>';
            $lname = $_POST["lname"];
            echo $lname . '<br>';
            $installed = $_POST["installed"];
            echo $installed . '<br>';
            // $percent = $_POST["percent"];
            // echo $percent . '<br>';
            $material  = $_POST["material"];
            echo $material . '<br>';
            $notes = $_POST["notes"];
            echo $notes . '<br>';
            // $collection = $_POST["collection"];
            // echo $collection . '<br>';
            $owner = $_POST["owner"];
            echo $owner . '<br>';
				
				
				
				
             
            $sql1 = sprintf("INSERT INTO ArtPiece (title, yearInstalled, info, locationID) VALUES (
					'%s', '%s', '%s', '%d' )",
					mysqli_real_escape_string($con, $piece),
					mysqli_real_escape_string($con, $installed), 
					mysqli_real_escape_string($con, $notes),
					mysqli_real_escape_string($con, $owner));
                   
                  
            $sql2 = sprintf("INSERT INTO MaterialType (type) VALUES (
					'%s')",
					mysqli_real_escape_string($con, $material));
              
            $sql3 = sprintf("INSERT INTO Artist (firstName, lastName) VALUES (
					'%s', '%s')",
					mysqli_real_escape_string($con, $fname),
					mysqli_real_escape_string($con, $lname));

            mysqli_query($con, $sql1);
            mysqli_query($con, $sql2);
            mysqli_query($con, $sql3);
              
            $query = " select * from artpiece where title like '%" . $piece . "%'";
          
            $pieceid = 0; // Initialize this
            printf ("Debug: running the query %s <br>", $query);
            $result = mysqli_query($con, $query);
             
            foreach ($result as $row) {
                printf('<li><span>%d || %s </span></li>',
						htmlspecialchars($row['pieceID']),
						htmlspecialchars($row['title']),
						$pieceid = htmlspecialchars($row['pieceID']));
            }
          
            $query = " select * from artist where lastName like '%" . $lname . "%'";
          
                
            printf ("Debug: running the query %s <br>", $query);
            $result = mysqli_query($con, $query);
           
            foreach ($result as $row) {
                printf('<li><span>%d || %s </span></li>',
						htmlspecialchars($row['artistID']),
						htmlspecialchars($row['lastName']),
						$artistid = htmlspecialchars($row['artistID']));
            }

			echo "Pieceid is: " . $pieceid . "<br>artistid is " . $artistid.'<br>';

            $sql4 = sprintf("INSERT INTO collaborator (artistID, pieceID) VALUES (
					'%d', '%d' )",
					$artistid, $pieceid);   
            mysqli_query($con, $sql4);               
            
            // $sql = sprintf("INSERT INTO Note (note) VALUES (
            //     '%s'
            //     )", mysqli_real_escape_string($con, $notes));
            // $sql = sprintf("INSERT INTO Genretype  (type) VALUES (
            //     '%s'
            //     )", mysqli_real_escape_string($con, $genre));      
                  
              
            // mysqli_query($con, $sql4);
            //   mysqli_query($con, $sql5);
             

            mysqli_close($con);
            echo '<br>';
            echo '<p>Piece added.</p>';

        ?>

</body>



</html>