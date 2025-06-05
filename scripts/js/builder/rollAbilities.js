function roll4d6DropLowest() {
    const rolls = Array.from({ length: 4 }, () => Math.floor(Math.random() * 6) + 1);
    rolls.sort((a, b) => a - b);
    return rolls[1] + rolls[2] + rolls[3]; // Drop the lowest
}

function roll4d6DropLowest() {
    const rolls = Array.from({ length: 4 }, () => Math.floor(Math.random() * 6) + 1);
    rolls.sort((a, b) => a - b);
    return rolls[1] + rolls[2] + rolls[3]; // Drop the lowest
}

function rollAbilities() {
    const scores = Array.from({ length: 6 }, () => roll4d6DropLowest());
    const fields = document.querySelectorAll('.ability-score');

    fields.forEach((field, index) => {
        field.value = scores[index];
        field.dispatchEvent(new Event('change'));
    });
}