<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formal_picture_path = $working_picture_path = "";

    // Upload formal picture
    if (!empty($_FILES["formal_picture"]["name"])) {
        $formal_dir = "uploads/formal_pics/";
        $formal_picture_path = $formal_dir . basename($_FILES["formal_picture"]["name"]);
        move_uploaded_file($_FILES["formal_picture"]["tmp_name"], $formal_picture_path);
    }

    // Upload working picture
    if (!empty($_FILES["working_picture"]["name"])) {
        $work_dir = "uploads/work_pics/";
        $working_picture_path = $work_dir . basename($_FILES["working_picture"]["name"]);
        move_uploaded_file($_FILES["working_picture"]["tmp_name"], $working_picture_path);
    }

    // Sanitize inputs
    $greeting = $conn->real_escape_string($_POST['greeting']);
    $short_intro = $conn->real_escape_string($_POST['short_intro']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $middle_name = $conn->real_escape_string($_POST['middle_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $gmail = $conn->real_escape_string($_POST['gmail']);
    $fb = $conn->real_escape_string($_POST['fb']);

    $sql = "UPDATE profile SET
        greeting='$greeting',
        short_intro='$short_intro',
        first_name='$first_name',
        middle_name='$middle_name',
        last_name='$last_name',
        birthday='$birthday',
        phone='$phone',
        gmail='$gmail',
        fb='$fb'";

    if (!empty($formal_picture_path)) {
        $sql .= ", formal_picture='$formal_picture_path'";
    }
    if (!empty($working_picture_path)) {
        $sql .= ", working_picture='$working_picture_path'";
    }

    $sql .= " WHERE id = 1";
    $conn->query($sql);

    // ==================================
    // ACHIEVEMENTS
    // add and update
    if (isset($_POST['achievements'])) {
        foreach ($_POST['achievements'] as $key => $ach) {
            $achievement = $conn->real_escape_string($ach['achievement']);
            $event = $conn->real_escape_string($ach['event']);
            $bestower = $conn->real_escape_string($ach['bestower']);
            $venue = $conn->real_escape_string($ach['venue']);
            $year = $conn->real_escape_string($ach['year']);

            if (is_numeric($key)) {
                // Update existing achievement
                $conn->query("UPDATE achievements SET
                    achievement='$achievement',
                    event='$event',
                    bestower='$bestower',
                    venue='$venue',
                    year='$year'
                    WHERE id = $key");
            } else {
                // Insert new achievement
                $conn->query("INSERT INTO achievements (profile_id, achievement, event, bestower, venue, year)
                    VALUES (1, '$achievement', '$event', '$bestower', '$venue', '$year')");
            }
        }
    }

    // Handle removals
    if (!empty($_POST['removed_achievements'])) {
        $removed = json_decode($_POST['removed_achievements']);
        foreach ($removed as $id) {
            $id = (int)$id;
            $conn->query("DELETE FROM achievements WHERE id = $id");
        }
    }

    // ==================================
    // AFFILIATIONS
    // add and update
    if (isset($_POST['affiliations'])) {
        foreach ($_POST['affiliations'] as $key => $aff) {
            $name = $conn->real_escape_string($aff['name']);
            
            if (is_numeric($key)) {
                // Update existing
                $conn->query("UPDATE affiliations SET name='$name' WHERE id = $key");
            } else {
                // Insert new
                $conn->query("INSERT INTO affiliations (profile_id, name) VALUES (1, '$name')");
            }
        }
    }

    // Handle removed affiliations
    if (!empty($_POST['removed_affiliations'])) {
        $removedIds = json_decode($_POST['removed_affiliations']);
        foreach ($removedIds as $id) {
            $id = (int)$id;
            $conn->query("DELETE FROM affiliations WHERE id = $id");
        }
    }

    echo "Profile updated successfully. <a href='cms.php'>Go back</a>";
}
?>
