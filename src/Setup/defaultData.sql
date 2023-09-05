INSERT INTO `fossil_form_field` (`fieldOrder`, `fieldName`, `fieldLabel`, `fieldType`, `showInOverview`, `allowBlank`, `isFilter`, `isRequiredDefault`)
VALUES (0, 'findingDate', 'Funddatum', 'date', true, true, true, true),
       (10, 'fossilNumber', 'Nummer', 'number', true, false, true, true),
       (20, 'fossilGenus', 'Gattung', 'text', true, false, true, true),
       (30, 'fossilSpecies', 'Art', 'text', true, false, true, true),
       (40, 'findingPlace', 'Fundort', 'text', true, true, true, false),
       (50, 'findingLayer', 'Fundschicht', 'text', true, true, true, false),
       (60, 'earthAge', 'Erdzeitalter', 'text', true, true, true, false),
       (70, 'descriptionAndNotes', 'Beschreibung und Anmerkungen', 'textarea', false, true, false, true);