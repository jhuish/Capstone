<!DOCTYPE html>
<!--
    JoCo Arts MAP project
 -->
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
            // Connect to the database.
            $con = new mysqli("localhost", "root", "", "test");
            // Verify the connection.
            if ($con->connect_errno) {
                echo "Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error; 
            }
            // Grab all the stuff from the forms.
            $piece = htmlspecialchars($_POST["artpiece"]);
            $fname = htmlspecialchars($_POST["fname"]);
            $lname = htmlspecialchars($_POST["lname"]);
            $installed = htmlspecialchars($_POST["installed"]);
            $material = htmlspecialchars($_POST["material"]);
            $notes = htmlspecialchars($_POST["notes"]);
            $owner = htmlspecialchars($_POST["owner"]);
            // Echo out all the things.
            echo $piece . "<br>";
            echo $fname . "<br>";
            echo $lname . "<br>";
            echo $installed . "<br>";
            echo $material . "<br>";
            echo $notes . "<br>";
            echo $owner . "<br>";

            // Use prepared SQL statements to prevent injections.
            // ArtPiece will probably fail because of the missing location.
            if (!($sql_artpiece = $con->prepare("INSERT INTO ArtPiece(title, yearInstalled, info, locationID) VALUES (?, ?, ?, ?)"))) {
                printf("WARNING: ArtPiece prepare failed: ( %s ) %s.", $con->errno, $con->error);
            } else {
                $sql_artpiece->bind_param("ssss", $piece, $installed, $notes, $owner);
                $sql_artpiece->execute(); 
                printf("Debug: %d rows inserted.<br>", $sql_artpiece->affected_rows);
                if ($sql_artpiece->affected_rows < 0) {
                    printf("WARNING: ArtPiece failed to be created. That will break some things.<br>");
                }
                $sql_artpiece->close();
                $piece_id = $con->insert_id;
            }
            
            // Check to see if there is an artist that matches the first and last name.
            // If there is then we will use that ID otherwise we will create a new one.
            // Though this is probably a bad idea in the long run if people have the same name.
            // It should probably be based on something else.
            if (!($check_artist = $con->prepare("SELECT artistID FROM Artist WHERE firstName = ? AND lastName = ?"))) {
                printf("WARNING: Checking Artist prepare failed: ( %s ) %s.", $con->errno, $con->error);
            } else {
                $check_artist->bind_param("ss", $fname, $lname);
                $check_artist->execute();
                $check_artist->bind_result($artist_id);
                $check_artist->store_result();
                printf("Debug: %d matching rows.<br>", $check_artist->affected_rows);
                if ($check_artist->affected_rows > 0) {
                    $check_artist->fetch();
                    if (!$artist_id) {
                        printf("Debug: ArtistID not found.<br>");
                    } else {
                        printf("Debug: ArtistID found is %d.<br>", $artist_id);
                    }
                }
                $check_artist->free_result();
                $check_artist->close();
            }
            // Create an artist if there isn't one already.
            if (!$artist_id) {
                printf("Debug: ArtistID not found.<br>");
                printf("Debug: Creating new Artist.<br>");
                if (!($sql_artist = $con->prepare("INSERT INTO Artist (firstName, lastName) VALUES (?, ?)"))) {
                    printf("WARNING: Artist prepare failed: ( %s ) %s.", $con->errno, $con->error);
                } else {
                    $sql_artist->bind_param("ss", $fname, $lname);
                    $sql_artist->execute();
                    printf("Debug: %d rows inserted.<br>", $sql_artist->affected_rows);
                    $sql_artist->close();
                    $artist_id = $con->insert_id;
                }
            }

            // Add a link between the artist and the art piece. 
            // Will fail if the artpiece failed to be created.
            if (!($sql_collab = $con->prepare("INSERT INTO Collaborator (pieceID, artistID) VALUES (?, ?)"))) {
                printf("WARNING: Collaborator prepare failed: ( %s ) %s.", $con->errno, $con->error);
            } else {
                $sql_collab->bind_param("ii", $piece_id, $artist_id);
                $sql_collab->execute();
                printf("Debug: %d rows Inserted.<br>", $sql_collab->affected_rows);
                $sql_collab->close();
            }

            // Check to see if there already a material that has a matching type.
            // If there isn't one then create a new material.
            // Basically a copy of the check_artist. 
            if (!($check_material = $con->prepare("SELECT materialID FROM MaterialType WHERE type = ?"))) {
                printf("WARNING: Check Material prepare failed: ( %s ) %s.", $con->errno, $con->error);
            } else {
                $check_material->bind_param("s", $material);
                $check_material->execute();
                $check_material->bind_result($material_id);
                $check_material->store_result();
                printf("Debug: %d matching rows.<br>", $check_material->affected_rows);
                if ($check_material->affected_rows > 0) {
                    $check_material->fetch();
                    if (!$material_id) {
                        printf("Debug: MaterialID not found.<br>");
                    } else {
                        printf("Debug: MaterialID found is %d.<br>", $material_id);
                    }
                }
                $check_material->free_result();
                $check_material->close();
            }
            // Add the material type if there isn't one already.
            if (!$material_id) {
                if (!($sql_material = $con->prepare("INSERT INTO MaterialType (type) VALUES (?)"))) {
                    printf("WARNING: Material prepare failed: ( %s ) %s.", $con->errno, $con->error);
                } else {
                    $sql_material->bind_param("s", $material);
                    $sql_material->execute();
                    printf("Debug: %d rows inserted.<br>", $sql_material->affected_rows);
                    $sql_material->close();
                    $material_id = $con->insert_id;
                }
            }

            // Add a link between the material and the art piece. 
            // Will fail if the artpiece failed to be created.
            if (!($sql_art_material = $con->prepare("INSERT INTO Material (pieceID, materialID) VALUES (?, ?)"))) {
                printf("WARNING: Material Link prepare failed: ( %s ) %s.", $con->errno, $con->error);
            } else {
                $sql_art_material->bind_param("ii", $piece_id, $material_id);
                $sql_art_material->execute();
                printf("Debug: %d rows Inserted.<br>", $sql_art_material->affected_rows);
                $sql_art_material->close();
            }
            // Echo out the results of the script.
            echo "<br>----------<br>";
            echo "PieceID is: " . $piece_id . "<br>";
            echo "ArtistID is: " . $artist_id . "<br>";
            echo "MaterialID is: " . $material_id . "<br>";
            echo "----------<br><br>";
            
            // Close the connection to the database.
            $con->close();

            // End
            echo "<br>";
            echo "<p>Piece added.</p>";

            ?>
    </body>
</html>