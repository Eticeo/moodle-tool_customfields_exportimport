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
 * @package   tool_customfields_exportimport
 * @copyright 2025 Serge Touvoli <serge.touvoli@eticeo.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Title
$string['plugintitle'] = 'Import/Export des champs personnalisés';
$string['pluginname'] = 'Champs personnalisés - Import/Export';
$string['field'] = 'Nom du champ';
$string['description'] = 'Description';
$string['type'] = 'Type de donnée';
$string['required'] = 'Obligatoire';
$string['actions'] = 'Actions';
$string['export'] = 'Exporter';
$string['category'] = 'Catégorie';
$string['cohortfields'] = 'Champs de cohorte';
$string['exportcategory'] = 'Exporter la catégorie';
$string['importsuccess'] = 'Importation réussie';
$string['profilefields'] = 'Champs du profil';
$string['coursefields'] = 'Champs de cours';
$string['exportpage'] = 'Export de champs personnalisés';
$string['importpage'] = 'Import de champs personnalisés';
$string['settingspage'] = 'Paramètres';
$string['settingsinfo'] = 'Aucun paramètre pour le moment.';
$string['export_title'] = 'Exporter les champs personnalisés';


// Cli
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
$string['clihelp_help'] = 'Afficher cette aide.';
$string['clihelp_type'] = 'Type de champ : profile, course, cohort.';
$string['clihelp_categoryid'] = 'ID de la catégorie à exporter.';
$string['clihelp_fieldid'] = '(Optionnel) ID d\'un champ unique à exporter.';
$string['clihelp_destination'] = '(Optionnel) Répertoire de destination où enregistrer le fichier JSON (par défaut : répertoire courant).';

$string['cli_export_success'] = 'Export effectué avec succès : {$a}';
$string['cli_export_failed'] = 'Échec de l\'écriture du fichier : {$a}';

// Cli import
$string['clihelp_import'] = 'Importe des champs personnalisés ou des champs du profil utilisateur à partir d\'un fichier JSON.

Options :
-h, --help      {$a->help}
-f, --file      {$a->file}

Exemple :
$php import.php --file=/chemin/vers/fichier.json';
$string['clihelp_import_help'] = 'Afficher cette aide.';
$string['clihelp_import_file'] = 'Chemin vers le fichier JSON à importer.';

$string['cli_import_success'] = 'Import réussi pour le type : {$a}';
$string['cli_import_failed'] = '❌ Échec de l\'import : {$a}';
$string['cli_import_invalidfile'] = 'Erreur : impossible de lire le fichier à l\'emplacement {$a}';
$string['cli_import_invalidjson'] = 'Le fichier JSON est invalide ou ne contient pas le champ "type".';
