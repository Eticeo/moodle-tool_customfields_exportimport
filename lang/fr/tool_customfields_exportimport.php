<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * French strings for customfields_exportimport
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actions'] = 'Actions';
$string['category'] = 'Catégorie';
$string['categoryalreadyexists'] = 'Une catégorie portant le nom "{$a}" existe déjà.';
$string['categorynameexists'] = 'Une catégorie portant le nom "{$a}" existe déjà.';
$string['cli_export_failed'] = 'Échec de l\'écriture du fichier : {$a}';
$string['cli_export_success'] = 'Export effectué avec succès : {$a}';
$string['cli_import_failed'] = '❌ Échec de l\'import : {$a}';
$string['cli_import_invalidfile'] = 'Erreur : impossible de lire le fichier à l\'emplacement {$a}';
$string['cli_import_invalidjson'] = 'Le fichier JSON est invalide ou ne contient pas le champ "type".';
$string['cli_import_success'] = 'Import réussi pour le type : {$a}';
$string['clihelp'] = 'Exporte les champs personnalisés ou les champs du profil utilisateur (au format JSON).

Options :
-h, --help          {$a->help}
-t, --type          {$a->type}
-c, --categoryid    {$a->categoryid}
-f, --fieldid       {$a->fieldid}
-d, --destination   {$a->destination}

Exemples :
\$php export.php --type=profile --categoryid=1
\$php export.php --type=course --categoryid=2 --fieldid=5';
$string['clihelp_categoryid'] = 'ID de la catégorie à exporter.';
$string['clihelp_destination'] = '(Optionnel) Répertoire de destination où enregistrer le fichier JSON (par défaut : répertoire courant).';
$string['clihelp_fieldid'] = '(Optionnel) ID d\'un champ unique à exporter.';
$string['clihelp_help'] = 'Afficher cette aide.';
$string['clihelp_import'] = 'Importe des champs personnalisés ou des champs du profil utilisateur à partir d\'un fichier JSON.

Options :
-h, --help      {$a->help}
-f, --file      {$a->file}

Exemple :
$php import.php --file=/chemin/vers/fichier.json';
$string['clihelp_import_file'] = 'Chemin vers le fichier JSON à importer.';
$string['clihelp_import_help'] = 'Afficher cette aide.';
$string['clihelp_type'] = 'Type de champ : profile, course, cohort.';
$string['cohortfields'] = 'Champs de cohorte';
$string['coursefields'] = 'Champs de cours';
$string['description'] = 'Description';
$string['export'] = 'Exporter';
$string['export_title'] = 'Exporter les champs personnalisés';
$string['exportcategory'] = 'Exporter la catégorie';
$string['exportpage'] = 'Export de champs personnalisés';
$string['field'] = 'Nom du champ';
$string['fieldname'] = 'Nom';
$string['fieldshortnameexists'] = 'Un champ avec le shortname "{$a}" existe déjà dans cette catégorie.';
$string['import'] = 'Importer';
$string['importpage'] = 'Import de champs personnalisés';
$string['importsuccess'] = 'Importation réussie';
$string['insertcategoryfailed'] = 'Échec lors de la création de la nouvelle catégorie de champ personnalisé.';
$string['invalidfiletype'] = 'Le fichier envoyé doit être un fichier JSON valide.';
$string['invalidjson'] = 'Le fichier JSON envoyé est invalide ou incomplet.';
$string['invalidjsonstructure'] = 'Le fichier JSON importé n\'a pas la structure attendue.';
$string['invalidtab'] = 'Onglet sélectionné invalide.';
$string['invalidtype'] = 'Type de champ personnalisé invalide trouvé dans l\'import.';
$string['pluginname'] = 'Champs personnalisés - Import/Export';
$string['plugintitle'] = 'Import/Export des champs personnalisés';
$string['privacy:metadata'] = 'Le plugin d’export/import des champs personnalisés ne stocke aucune donnée personnelle.';
$string['profilefields'] = 'Champs du profil';
$string['required'] = 'Obligatoire';
$string['settingsinfo'] = 'Aucun paramètre pour le moment.';
$string['settingspage'] = 'Paramètres';
$string['shortnamealreadyexists'] = 'Un champ personnalisé avec le shortname "{$a}" existe déjà.';
$string['type'] = 'Type de donnée';
$string['invaliddatatype'] = 'Type de donnée invalide trouvé dans l\'import.';
$string['typefortable'] = 'Type';




