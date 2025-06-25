// Get URL parameters
const params = new URLSearchParams(window.location.search);

// Get specific value
const characterId = params.get('characterId');

let refresh = false;

function updateField(field, value) {
$.ajax({
    url: 'updateBuilder',
    type: 'POST',
    data: {
    field: field,
    value: value,
    characterId: characterId,
    userId: userId

    },
    success: function(response) {
    console.log(`Updated ${field} to ${value}: ${response}`);
    if (refresh) {
        refresh = false;
        location.reload();
    }
    },

    error: function() {
    console.log(`Failed to update ${field} to ${value}`);
    }
});
}

// Listen for input changes
$('#characterName').on('change', function() {
updateField('characterName', $(this).val());
});

$('#characterAge').on('change', function() {
updateField('characterAge', $(this).val());
});

$('#level').on('change', function() {
refresh = true;
updateField('level', $(this).val());
});

$('#levels').on('change', function() {
refresh = true;
updateField('levels', $(this).val());
});

$('#alignment').on('change', function () {
updateField('alignment', $(this).val());
});

$('#characterBackstory').on('change', function () {
updateField('characterBackstory', $(this).val());
});

$('#characterPersonality').on('change', function () {
updateField('characterPersonality', $(this).val());
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'confirm-class-selection') {
        refresh = true;
        const index = e.target.value;
        updateField('classId', index);
    }
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'confirm-race-selection') {
        refresh = true;
        const index = e.target.value;
        updateField('raceId', index);
    }
});

$('.ability-score').on('change', function () {
refresh = true;
const field = $(this).data('field'); // gets "strength", "dexterity", etc.
const value = $(this).val();
updateField(field, value);
});