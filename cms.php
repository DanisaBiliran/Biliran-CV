<?php
include 'db.php';

// Fetch profile info
$result = $conn->query("SELECT * FROM profile WHERE id = 1");
$data = $result->fetch_assoc();

// Fetch achievements
$achievements = $conn->query("SELECT * FROM achievements WHERE profile_id = 1")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<h1>Edit Profile</h1>
<form action="save.php" method="post" enctype="multipart/form-data">

    <!-- Greeting and Intro -->
    <label>Greeting:</label><input type="text" name="greeting" value="<?= htmlspecialchars($data['greeting']) ?>"><br>
    <label>Short Introduction:</label><textarea name="short_intro"><?= htmlspecialchars($data['short_intro']) ?></textarea><br>

    <!-- Pictures -->
    <label>Formal Picture:</label>
    <input type="file" name="formal_picture">
    <?php if (!empty($data['formal_picture'])): ?>
        <br><img src="<?= $data['formal_picture'] ?>" width="150"><br>
    <?php endif; ?>

    <label>Working Picture:</label>
    <input type="file" name="working_picture">
    <?php if (!empty($data['working_picture'])): ?>
        <br><img src="<?= $data['working_picture'] ?>" width="150"><br>
    <?php endif; ?>

    <!-- General Info -->
    <h2>General Information</h2>
    <label>First Name:</label><input type="text" name="first_name" value="<?= htmlspecialchars($data['first_name']) ?>"><br>
    <label>Middle Name:</label><input type="text" name="middle_name" value="<?= htmlspecialchars($data['middle_name']) ?>"><br>
    <label>Last Name:</label><input type="text" name="last_name" value="<?= htmlspecialchars($data['last_name']) ?>"><br>
    <label>Birthday:</label><input type="date" name="birthday" value="<?= $data['birthday'] ?>"><br>
    <label>Phone:</label><input type="text" name="phone" value="<?= htmlspecialchars($data['phone']) ?>"><br>
    <label>Gmail:</label><input type="email" name="gmail" value="<?= htmlspecialchars($data['gmail']) ?>"><br>
    <label>Facebook:</label><input type="text" name="fb" value="<?= htmlspecialchars($data['fb']) ?>"><br>

    <!-- Achievements -->
    <h2>Achievements and Special Activities</h2>
    <div id="achievements">
        <?php foreach ($achievements as $ach): ?>
            <div class="achievement-entry" id="achievement-<?= $ach['id'] ?>" data-id="<?= $ach['id'] ?>">
                <label>Achievement:</label>
                <input type="text" name="achievements[<?= $ach['id'] ?>][achievement]" value="<?= htmlspecialchars($ach['achievement']) ?>"><br>
                <label>Event:</label>
                <input type="text" name="achievements[<?= $ach['id'] ?>][event]" value="<?= htmlspecialchars($ach['event']) ?>"><br>
                <label>Bestower:</label>
                <input type="text" name="achievements[<?= $ach['id'] ?>][bestower]" value="<?= htmlspecialchars($ach['bestower']) ?>"><br>
                <label>Venue:</label>
                <input type="text" name="achievements[<?= $ach['id'] ?>][venue]" value="<?= htmlspecialchars($ach['venue']) ?>"><br>
                <label>Year:</label>
                <input type="text" name="achievements[<?= $ach['id'] ?>][year]" value="<?= htmlspecialchars($ach['year']) ?>"><br>
                <button type="button" onclick="removeAchievement(<?= $ach['id'] ?>)">Remove Achievement</button><br><br>
            </div>
        <?php endforeach; ?>
    </div>
    
    <button type="button" onclick="addAchievement()">Add Achievement</button><br><br>
    <input type="hidden" name="removed_achievements" id="removed-achievements">

    <!-- Affiliations -->
    <h2>Professional Affiliations</h2>
    <div id="affiliations">
        <?php 
        $affiliations = $conn->query("SELECT * FROM affiliations WHERE profile_id = 1")->fetch_all(MYSQLI_ASSOC);
        foreach ($affiliations as $aff): ?>
            <div class="affiliation-entry" id="affiliation-<?= $aff['id'] ?>" data-id="<?= $aff['id'] ?>">
                <input type="text" name="affiliations[<?= $aff['id'] ?>][name]" 
                    value="<?= htmlspecialchars($aff['name']) ?>">
                <button type="button" onclick="removeAffiliation(<?= $aff['id'] ?>)">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addAffiliation()">Add Affiliation</button>
    <input type="hidden" name="removed_affiliations" id="removed-affiliations">

    <input type="submit" value="Save">
</form>

<script>
    let newAchievementCounter = 0;

    // ACHIEVEMENT
    function addAchievement() {
        const container = document.getElementById("achievements");
        const entry = document.createElement("div");
        const newId = 'new_' + newAchievementCounter++;
        entry.classList.add("achievement-entry");
        entry.setAttribute('data-id', newId);
        entry.innerHTML = `
            <label>Achievement:</label><input type="text" name="achievements[${newId}][achievement]"><br>
            <label>Event:</label><input type="text" name="achievements[${newId}][event]"><br>
            <label>Bestower:</label><input type="text" name="achievements[${newId}][bestower]"><br>
            <label>Venue:</label><input type="text" name="achievements[${newId}][venue]"><br>
            <label>Year:</label><input type="text" name="achievements[${newId}][year]"><br>
            <button type="button" onclick="removeAchievement('${newId}')">Remove Achievement</button><br><br>
        `;
        container.appendChild(entry);
    }

    function removeAchievement(id) {
        const entry = document.querySelector(`[data-id="${id}"]`);
        if (entry) entry.remove();
        
        // Only track database IDs for deletion
        if (!isNaN(id)) {
            const removedField = document.getElementById('removed-achievements');
            const removed = JSON.parse(removedField.value || '[]');
            removed.push(parseInt(id));
            removedField.value = JSON.stringify(removed);
        }
    }

    // AFFILIATION
    let affiliationIndex = <?= count($affiliations) ?>;
    let newAffiliationCounter = 0;

    function addAffiliation() {
        const container = document.getElementById("affiliations");
        const entry = document.createElement("div");
        const newId = 'new_' + newAffiliationCounter++;
        
        entry.classList.add("affiliation-entry");
        entry.setAttribute('data-id', newId);
        entry.innerHTML = `
            <input type="text" name="affiliations[${newId}][name]">
            <button type="button" onclick="removeAffiliation('${newId}')">Remove</button>
        `;
        container.appendChild(entry);
    }

    function removeAffiliation(id) {
        const entry = document.querySelector(`[data-id="${id}"]`);
        if (entry) entry.remove();

        if (typeof id === 'number' || !isNaN(id)) {
            const removed = document.getElementById('removed-affiliations');
            let removedIds = JSON.parse(removed.value || '[]');
            removedIds.push(parseInt(id));
            removed.value = JSON.stringify(removedIds);
        }
    }

</script>


</body>
</html>
