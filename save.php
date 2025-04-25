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

    // ==================================
    // EXPERIENCE AREAS
    // add and update
    if (isset($_POST['experience_areas'])) {
        foreach ($_POST['experience_areas'] as $key => $exp) {
            $name = $conn->real_escape_string($exp['name']);
            $percentage = $conn->real_escape_string($exp['percentage']);

            if (is_numeric($key)) {
                // Update existing entry
                $conn->query("UPDATE experience_areas SET 
                    name='$name', 
                    percentage='$percentage' 
                    WHERE id = $key");
            } else {
                // Insert new entry
                $conn->query("INSERT INTO experience_areas (profile_id, name, percentage) 
                    VALUES (1, '$name', '$percentage')");
            }
        }
    }

    // Handle removed experience areas
    if (!empty($_POST['removed_experience'])) {
        $removedIds = json_decode($_POST['removed_experience']);
        foreach ($removedIds as $id) {
            $id = (int)$id;
            $conn->query("DELETE FROM experience_areas WHERE id = $id");
        }
    }

    // ==================================
    // SERVICES
    // add and update
    // Handle services
    if (isset($_POST['services'])) {
        foreach ($_POST['services'] as $key => $service) {
            $icon = $conn->real_escape_string($service['icon']);
            $service_name = $conn->real_escape_string($service['service_name']);
            $short_description = $conn->real_escape_string($service['short_description']);

            if (is_numeric($key)) {
                // Update existing entry
                $sql = "UPDATE services SET
                            icon='$icon',
                            service_name='$service_name',
                            short_description='$short_description'
                        WHERE id = $key";
                $conn->query($sql);
            } else {
                // Insert new entry
                $sql = "INSERT INTO services (profile_id, icon, service_name, short_description)
                        VALUES (1, '$icon', '$service_name', '$short_description')";
                $conn->query($sql);
            }
        }
    }

    // Handle removed services
    if (!empty($_POST['removed_services'])) {
        $removedIds = json_decode($_POST['removed_services']);
        foreach ($removedIds as $id) {
            $id = (int)$id;
            $conn->query("DELETE FROM services WHERE id = $id");
        }
    }

    // ==================================
    // PROJECTS
    // add and update
    // Directory for project images
    $project_image_dir = "uploads/project_pics/";

    // Function to handle image uploads
    function uploadProjectImage($file, $project_image_dir) {
        $image_name = basename($file["name"]);
        $target_file = $project_image_dir . $image_name;
        move_uploaded_file($file["tmp_name"], $target_file);
        return $image_name;
    }

    // Handle projects
    if (isset($_POST['projects'])) {
        foreach ($_POST['projects'] as $key => $project) {
            $name = $conn->real_escape_string($project['name']);
            $link = $conn->real_escape_string($project['link']);
            $status = $conn->real_escape_string($project['status']);
            $year_completed = ($status == 'completed' && isset($project['year_completed'])) ? intval($project['year_completed']) : null;

            $image_name = '';

            // reorganise $_FILES array
            $project_image = array();
            if(isset($_FILES['project_image'])){
            foreach($_FILES['project_image'] as $key_outer => $value_outer) {
                foreach($value_outer as $key_inner => $value_inner) {
                    $project_image[$key_inner][$key_outer] = $value_inner;
                }
            }
            }
        
            // Handle image upload
            if (isset($project_image[$key]) && !empty($project_image[$key]['name'])) {
                $image_name = uploadProjectImage($project_image[$key], $project_image_dir);
            }

            if (is_numeric($key)) {
                // Update existing entry
                $sql = "UPDATE projects SET
                            name='$name',
                            link='$link',
                            status='$status',
                            year_completed=" . ($year_completed === null ? 'NULL' : $year_completed);

                if (!empty($image_name)) {
                    $sql .= ", image='$image_name'";
                }

                $sql .= " WHERE id = $key";
                $conn->query($sql);

            } else {
                // Insert new entry
                $sql = "INSERT INTO projects (profile_id, name, link, image, status, year_completed)
                        VALUES (1, '$name', '$link', '$image_name', '$status', " . ($year_completed === null ? 'NULL' : $year_completed) . ")";
                $conn->query($sql);
            }
        }
    }

    // Handle removed projects
    if (!empty($_POST['removed_projects'])) {
        $removedIds = json_decode($_POST['removed_projects']);
        foreach ($removedIds as $id) {
            $id = (int)$id;

            // Fetch image name to delete from directory
            $result = $conn->query("SELECT image FROM projects WHERE id = $id");
            $project = $result->fetch_assoc();
            if ($project && !empty($project['image'])) {
                $image_path = $project_image_dir . $project['image'];
                if (file_exists($image_path)) {
                    unlink($image_path); // Delete the file
                }
            }

            $conn->query("DELETE FROM projects WHERE id = $id");
        }
    }

    // ==================================
    // REFERENCES
    // add and update
    if (isset($_POST['references'])) {
        foreach ($_POST['references'] as $key => $ref) {
            $first_name = $conn->real_escape_string($ref['first_name']);
            $middle_name = $conn->real_escape_string($ref['middle_name']);
            $last_name = $conn->real_escape_string($ref['last_name']);

            // Determine title value
            if ($ref['title'] == 'Other') {
                $title = $conn->real_escape_string($ref['custom_title']);
            } else {
                $title = $conn->real_escape_string($ref['title']);
            }

            $position = $conn->real_escape_string($ref['position']);
            $department = $conn->real_escape_string($ref['department']);
            $institution = $conn->real_escape_string($ref['institution']);
            $mobile = $conn->real_escape_string($ref['mobile']);
            $email = $conn->real_escape_string($ref['email']);

            if (is_numeric($key)) {
                // Update existing entry
                $sql = "UPDATE `reference` SET
                            first_name='$first_name',
                            middle_name='$middle_name',
                            last_name='$last_name',
                            title='$title',
                            position='$position',
                            department='$department',
                            institution='$institution',
                            mobile='$mobile',
                            email='$email'
                        WHERE id = $key";
                $conn->query($sql);
            } else {
                // Insert new entry
                $sql = "INSERT INTO `reference` (profile_id, first_name, middle_name, last_name, title, position, department, institution, mobile, email)
                        VALUES (1, '$first_name', '$middle_name', '$last_name', '$title', '$position', '$department', '$institution', '$mobile', '$email')";
                $conn->query($sql);
            }
        }
    }

    // Handle removed references
    if (!empty($_POST['removed_references'])) {
        $removedIds = json_decode($_POST['removed_references']);
        foreach ($removedIds as $id) {
            $id = (int)$id;
            $conn->query("DELETE FROM `reference` WHERE id = $id");
        }
    }

    echo "Profile updated successfully. <a href='cms.php'>Go back</a>";
}
?>
