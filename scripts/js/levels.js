// Assume characterId is provided
const character = await db.query("SELECT classId, level FROM characters WHERE characterId = ?", [characterId]);
const className = getClassNameFromId(character.classId); // e.g., "Barbarian"
const characterLevel = character.level;

// Load and filter JSON
const classFeatures = jsonData.filter(feature =>
  feature.className === className && feature.level <= characterLevel
);

// Format output
const output = classFeatures.map(feature => ({
  name: feature.name,
  level: feature.level,
  description: feature.entries
}));

return output;