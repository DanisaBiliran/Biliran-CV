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

    <!-- Experience Areas -->
    <h2>Experience Areas</h2>
    <div id="experience_areas">
        <?php 
        $experience_areas = $conn->query("SELECT * FROM experience_areas WHERE profile_id = 1")->fetch_all(MYSQLI_ASSOC);
        foreach ($experience_areas as $exp): ?>
            <div class="experience-entry" id="experience-<?= $exp['id'] ?>" data-id="<?= $exp['id'] ?>">
                <label>Name:</label>
                <input type="text" name="experience_areas[<?= $exp['id'] ?>][name]" 
                    value="<?= htmlspecialchars($exp['name']) ?>">
                <label>Percentage:</label>
                <input type="number" name="experience_areas[<?= $exp['id'] ?>][percentage]" 
                    value="<?= htmlspecialchars($exp['percentage']) ?>" min="0" max="100">
                <button type="button" onclick="removeExperience(<?= $exp['id'] ?>)">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addExperience()">Add Experience Area</button>
    <input type="hidden" name="removed_experience" id="removed-experience">
    
    <!-- Services -->
    <h2>Services</h2>
    <div id="services">
        <?php
        $services = $conn->query("SELECT * FROM services WHERE profile_id = 1")->fetch_all(MYSQLI_ASSOC);
        $available_icons = [
            ["icon" => "fa-solid fa-code", "label" => "Code"],
            ["icon" => "fa-solid fa-laptop-code", "label" => "Laptop Code"],
            ["icon" => "fa-solid fa-database", "label" => "Database"],
            ["icon" => "fa-solid fa-mobile-alt", "label" => "Mobile Dev"],
            ["icon" => "fa-solid fa-globe", "label" => "Web"],
            ["icon" => "fa-solid fa-brain", "label" => "AI / ML"],
            ["icon" => "fa-solid fa-shield-alt", "label" => "Cybersecurity"],
            ["icon" => "fa-solid fa-chart-line", "label" => "Marketing"],
            ["icon" => "fa-solid fa-palette", "label" => "Design"],
            ["icon" => "fa-solid fa-tools", "label" => "Tech Support"],
            ["icon" => "fa-solid fa-handshake", "label" => "Consulting"],
            ["icon" => "fa-solid fa-user-gear", "label" => "DevOps"],
            ["icon" => "fa-solid fa-cogs", "label" => "Engineering"],
            ["icon" => "fa-solid fa-microchip", "label" => "Hardware"],
            ["icon" => "fa-solid fa-video", "label" => "Video"],
            ["icon" => "fa-solid fa-music", "label" => "Music"],
            ["icon" => "fa-solid fa-pen-nib", "label" => "Content"]
        ];
        foreach ($services as $service): ?>
            <div class="service-entry" id="service-<?= $service['id'] ?>" data-id="<?= $service['id'] ?>">
                <label>Icon:</label>
                <select name="services[<?= $service['id'] ?>][icon]" onchange="updateIcon(this, '<?= $service['id'] ?>')">
                    <?php foreach ($available_icons as $icon): ?>
                        <option value="<?= $icon['icon'] ?>" <?= ($service['icon'] == $icon['icon']) ? 'selected' : '' ?>>
                            <?= $icon['label'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="<?= htmlspecialchars($service['icon']) ?>"></i> <!-- Display the icon -->
                <br>
                <label>Service Name:</label>
                <input type="text" name="services[<?= $service['id'] ?>][service_name]" value="<?= htmlspecialchars($service['service_name']) ?>"><br>
                <label>Short Description:</label>
                <textarea name="services[<?= $service['id'] ?>][short_description]"><?= htmlspecialchars($service['short_description']) ?></textarea><br>
                <button type="button" onclick="removeService(<?= $service['id'] ?>)">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addService()">Add Service</button>
    <input type="hidden" name="removed_services" id="removed-services">

    <script>
        function updateIcon(selectElement, serviceId) {
            let iconClass = selectElement.value;
            let iconElement = selectElement.nextElementSibling; // Get the <i> tag

            // If serviceId is provided, use it to target the specific icon
            if (serviceId) {
                iconElement = document.querySelector(`#service-${serviceId} i`);
            }

            iconElement.className = iconClass; // Update the class
        }
    </script>

    <!-- Projects -->
    <h2>Projects</h2>
    <div id="projects">
        <?php
        $projects = $conn->query("SELECT * FROM projects WHERE profile_id = 1")->fetch_all(MYSQLI_ASSOC);
        foreach ($projects as $project): ?>
            <div class="project-entry" id="project-<?= $project['id'] ?>" data-id="<?= $project['id'] ?>">
                <label>Name:</label>
                <input type="text" name="projects[<?= $project['id'] ?>][name]" value="<?= htmlspecialchars($project['name']) ?>"><br>
                <label>Link:</label>
                <input type="text" name="projects[<?= $project['id'] ?>][link]" value="<?= htmlspecialchars($project['link']) ?>"><br>
                <label>Image:</label>
                <input type="file" name="project_image[<?= $project['id'] ?>]">
                <?php if (!empty($project['image'])): ?>
                    <br><img src="uploads/project_pics/<?= $project['image'] ?>" width="150"><br>
                <?php endif; ?>
                <button type="button" onclick="removeProject(<?= $project['id'] ?>)">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addProject()">Add Project</button>
    <input type="hidden" name="removed_projects" id="removed-projects">

    <!-- References -->
    <h2>References</h2>
    <div id="references">
        <?php
        $references = $conn->query("SELECT * FROM `reference` WHERE profile_id = 1")->fetch_all(MYSQLI_ASSOC);
        foreach ($references as $ref): ?>
            <div class="reference-entry" id="reference-<?= $ref['id'] ?>" data-id="<?= $ref['id'] ?>">
                <label>Title:</label>
                <select name="references[<?= $ref['id'] ?>][title]" onchange="toggleCustomTitle(this, this.nextElementSibling)">
                    <option value="Mr." <?= ($ref['title'] == 'Mr.') ? 'selected' : '' ?>>Mr.</option>
                    <option value="Ms." <?= ($ref['title'] == 'Ms.') ? 'selected' : '' ?>>Ms.</option>
                    <option value="Mrs." <?= ($ref['title'] == 'Mrs.') ? 'selected' : '' ?>>Mrs.</option>
                    <option value="Other" <?= ($ref['title'] != 'Mr.' && $ref['title'] != 'Ms.' && $ref['title'] != 'Mrs.') && ($ref['title'] != '') ? 'selected' : '' ?>>Other</option>
                </select>
                <input type="text" name="references[<?= $ref['id'] ?>][custom_title]"
                    value="<?= ($ref['title'] != 'Mr.' && $ref['title'] != 'Ms.' && $ref['title'] != 'Mrs.') ? htmlspecialchars($ref['title']) : '' ?>"
                    placeholder="Custom Title"
                    style="display:<?= ($ref['title'] != 'Mr.' && $ref['title'] != 'Ms.' && $ref['title'] != 'Mrs.' && $ref['title'] != 'Other') ? 'inline' : 'none' ?>;">
                <br>

                <label>First Name:</label>
                <input type="text" name="references[<?= $ref['id'] ?>][first_name]" value="<?= htmlspecialchars($ref['first_name']) ?>"><br>
                <label>Middle Name:</label>
                <input type="text" name="references[<?= $ref['id'] ?>][middle_name]" value="<?= htmlspecialchars($ref['middle_name']) ?>"><br>
                <label>Last Name:</label>
                <input type="text" name="references[<?= $ref['id'] ?>][last_name]" value="<?= htmlspecialchars($ref['last_name']) ?>"><br>
                <label>Position:</label>
                <input type="text" name="references[<?= $ref['id'] ?>][position]" value="<?= htmlspecialchars($ref['position']) ?>"><br>
                <label>Department:</label>
                <input type="text" name="references[<?= $ref['id'] ?>][department]" value="<?= htmlspecialchars($ref['department']) ?>"><br>
                <label>Institution:</label>
                <input type="text" name="references[<?= $ref['id'] ?>][institution]" value="<?= htmlspecialchars($ref['institution']) ?>"><br>
                <label>Mobile:</label>
                <input type="text" name="references[<?= $ref['id'] ?>][mobile]" value="<?= htmlspecialchars($ref['mobile']) ?>"><br>
                <label>Email:</label>
                <input type="email" name="references[<?= $ref['id'] ?>][email]" value="<?= htmlspecialchars($ref['email']) ?>"><br>
                <button type="button" onclick="removeReference(<?= $ref['id'] ?>)">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addReference()">Add Reference</button>
    <input type="hidden" name="removed_references" id="removed-references">

    <br><input type="submit" value="Save">
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

    // AFFILIATIONS
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

    // EXPERIENCE AREAS
    let experienceIndex = <?= count($experience_areas) ?>;
    let newExperienceCounter = 0;

    function addExperience() {
        const container = document.getElementById("experience_areas");
        const entry = document.createElement("div");
        const newId = 'new_' + newExperienceCounter++;
        
        entry.classList.add("experience-entry");
        entry.setAttribute('data-id', newId);
        entry.innerHTML = `
            <label>Name:</label>
            <input type="text" name="experience_areas[${newId}][name]">
            <label>Percentage:</label>
            <input type="number" name="experience_areas[${newId}][percentage]" min="0" max="100">
            <button type="button" onclick="removeExperience('${newId}')">Remove</button>
        `;
        container.appendChild(entry);
    }

    function removeExperience(id) {
        const entry = document.querySelector(`[data-id="${id}"]`);
        if (entry) entry.remove();

        if (typeof id === 'number' || !isNaN(id)) {
            const removed = document.getElementById('removed-experience');
            let removedIds = JSON.parse(removed.value || '[]');
            removedIds.push(parseInt(id));
            removed.value = JSON.stringify(removedIds);
        }
    }

    // SERVICES
    let serviceIndex = <?= count($services) ?>;
    let newServiceCounter = 0;

    const availableIcons = [
        {icon: "fa-solid fa-code", label: "Code"},
        {icon: "fa-solid fa-laptop-code", label: "Laptop Code"},
        {icon: "fa-solid fa-database", label: "Database"},
        {icon: "fa-solid fa-mobile-alt", label: "Mobile Dev"},
        {icon: "fa-solid fa-globe", label: "Web"},
        {icon: "fa-solid fa-brain", label: "AI / ML"},
        {icon: "fa-solid fa-shield-alt", label: "Cybersecurity"},
        {icon: "fa-solid fa-chart-line", label: "Marketing"},
        {icon: "fa-solid fa-palette", label: "Design"},
        {icon: "fa-solid fa-tools", label: "Tech Support"},
        {icon: "fa-solid fa-handshake", label: "Consulting"},
        {icon: "fa-solid fa-user-gear", label: "DevOps"},
        {icon: "fa-solid fa-cogs", label: "Engineering"},
        {icon: "fa-solid fa-microchip", label: "Hardware"},
        {icon: "fa-solid fa-video", label: "Video"},
        {icon: "fa-solid fa-music", label: "Music"},
        {icon: "fa-solid fa-pen-nib", label: "Content"}
    ];

    function addService() {
        const container = document.getElementById("services");
        const newId = 'new_' + newServiceCounter++;
        const entry = document.createElement("div");
        entry.classList.add("service-entry");
        entry.setAttribute('data-id', newId);

        let iconOptions = '';
        availableIcons.forEach(icon => {
            iconOptions += `<option value="${icon.icon}">${icon.label}</option>`;
        });

        entry.innerHTML = `
            <label>Icon:</label>
            <select name="services[${newId}][icon]" onchange="updateIcon(this)">
                ${iconOptions}
            </select>
            <i class="fa-solid fa-code"></i> <!-- Initial icon -->
            <br>
            <label>Service Name:</label>
            <input type="text" name="services[${newId}][service_name]"><br>
            <label>Short Description:</label>
            <textarea name="services[${newId}][short_description]"></textarea><br>
            <button type="button" onclick="removeService('${newId}')">Remove</button>
        `;
        container.appendChild(entry);
    }

    function removeService(id) {
        const entry = document.querySelector(`[data-id="${id}"]`);
        if (entry) entry.remove();

        if (typeof id === 'number' || !isNaN(id)) {
            const removed = document.getElementById('removed-services');
            let removedIds = JSON.parse(removed.value || '[]');
            removedIds.push(parseInt(id));
            removed.value = JSON.stringify(removedIds);
        }
    }

    function updateIcon(selectElement) {
        const iconClass = selectElement.value;
        const iconElement = selectElement.nextElementSibling; // Get the <i> tag
        iconElement.className = iconClass; // Update the class
    }

    // PROJECTS
    let projectIndex = <?= count($projects) ?>;
    let newProjectCounter = 0;

    function addProject() {
        const container = document.getElementById("projects");
        const newId = 'new_' + newProjectCounter++;
        const entry = document.createElement("div");
        entry.classList.add("project-entry");
        entry.setAttribute('data-id', newId);
        entry.innerHTML = `
            <label>Name:</label>
            <input type="text" name="projects[${newId}][name]"><br>
            <label>Link:</label>
            <input type="text" name="projects[${newId}][link]"><br>
            <label>Image:</label>
            <input type="file" name="project_image[${newId}]"><br>
            <button type="button" onclick="removeProject('${newId}')">Remove</button>
        `;
        container.appendChild(entry);
    }

    function removeProject(id) {
        const entry = document.querySelector(`[data-id="${id}"]`);
        if (entry) entry.remove();

        if (typeof id === 'number' || !isNaN(id)) {
            const removed = document.getElementById('removed-projects');
            let removedIds = JSON.parse(removed.value || '[]');
            removedIds.push(parseInt(id));
            removed.value = JSON.stringify(removedIds);
        }
    }

    // REFERENCES
    let referenceIndex = <?= count($references) ?>;
    let newReferenceCounter = 0;

    function addReference() {
        const container = document.getElementById("references");
        const newId = 'new_' + newReferenceCounter++;
        const entry = document.createElement("div");
        entry.classList.add("reference-entry");
        entry.setAttribute('data-id', newId);
        entry.innerHTML = `
            <label>Title:</label>
            <select name="references[${newId}][title]" onchange="toggleCustomTitle(this, this.nextElementSibling)">
                <option value="Mr.">Mr.</option>
                <option value="Ms.">Ms.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" name="references[${newId}][custom_title]" placeholder="Custom Title" style="display:none;"><br>

            <label>First Name:</label>
            <input type="text" name="references[${newId}][first_name]"><br>
            <label>Middle Name:</label>
            <input type="text" name="references[${newId}][middle_name]"><br>
            <label>Last Name:</label>
            <input type="text" name="references[${newId}][last_name]"><br>
            <label>Position:</label>
            <input type="text" name="references[${newId}][position]"><br>
            <label>Department:</label>
            <input type="text" name="references[${newId}][department]"><br>
            <label>Institution:</label>
            <input type="text" name="references[${newId}][institution]"><br>
            <label>Mobile:</label>
            <input type="text" name="references[${newId}][mobile]"><br>
            <label>Email:</label>
            <input type="email" name="references[${newId}][email]"><br>
            <button type="button" onclick="removeReference('${newId}')">Remove</button>
        `;
        container.appendChild(entry);
    }

    function removeReference(id) {
        const entry = document.querySelector(`[data-id="${id}"]`);
        if (entry) entry.remove();

        if (typeof id === 'number' || !isNaN(id)) {
            const removed = document.getElementById('removed-references');
            let removedIds = JSON.parse(removed.value || '[]');
            removedIds.push(parseInt(id));
            removed.value = JSON.stringify(removedIds);
        }
    }

    function toggleCustomTitle(selectElement, customTitleInput) {
        customTitleInput.style.display = (selectElement.value === 'Other') ? 'inline' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('select[name$="[title]"]').forEach(function(selectElement) {
            toggleCustomTitle(selectElement, selectElement.nextElementSibling);
        });
    });

</script>


</body>
</html>
