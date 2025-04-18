<?php 

define('LANG_PAGE_TITLE', 'Rezepte');
define('LANG_FORMFIELD_ERRORS', array(
    'pattern' => 'Eingabe ungültig!',
    'external' => 'Eingabe ungültig!',
    'too_long' => 'Eingabe zu lang! (max {LENGTH}, aktuell {ACTUAL_LENGTH})',
    'too_short' => 'Eingabe zu kurz! (min {LENGTH})',
    'mandatory' => 'Eingabe erforderlich!'
));
define('LANG_FORM_INVALID', 'Bitte überprüfen Sie ihre Eingabe!');

define('LANG_FIELD_USER_USERNAME', 'Benutzername');
define('LANG_FIELD_USER_EMAIL', 'E-Mail');
define('LANG_FIELD_USER_PASSWORD', 'Passwort');
define('LANG_FIELD_USER_PASSWORD_ERROR', 'Passwort muß mindestens je einen Großbuchstaben, Kleinbuchstaben, Zahl und Sonderzeichen haben!');
define('LANG_FIELD_USER_REPEAT_PASSWORD', 'Passwort wiederholen');
define('LANG_FIELD_USER_REPEAT_PASSWORD_ERROR', 'Passwort nicht identisch!');
define('LANG_FIELD_USER_ACTUAL_PASSWORD', 'Aktuelles Passwort');

define('LANG_NAVIGATION_LOGIN', 'login');
define('LANG_NAVIGATION_LOGOUT', 'logout');
define('LANG_NAVIGATION_REGISTER', 'registrieren');

define('LANG_REGISTER_HDL', 'Registrieren');
define('LANG_REGISTER_SAVE', 'Registrieren');
define('LANG_REGISTER_FAIL_EMAIL', 'E-Mail schon vorhanden!');
define('LANG_REGISTER_SUCCESS', 'Registrierung erfolgreich!');

define('LANG_PASSWORD_REQUEST_HDL', 'Passwort vergessen');
define('LANG_PASSWORD_REQUEST_BUTTON', 'Link anfordern');
define('LANG_PASSWORD_REQUEST_SUBJECT', 'Neues Passwort angefordert');
define('LANG_PASSWORD_REQUEST_SUCCESS', 'Der Link zum ändern des Passworts wurde an die E-Mail-Adresse verschick!');

define('LANG_PASSWORD_CHANGE_HDL', 'Passwort ändern');
define('LANG_PASSWORD_CHANGE_SAVE', 'Speichern');
define('LANG_PASSWORD_CHANGE_ERROR_TIME', 'Der Link ist abgelaufen! <br>Bitte fordern Sie einen neuen Link an!');
define('LANG_PASSWORD_CHANGE_SUCCESS', 'Passwort wurde erfolgreich geändert!');

define('LANG_PROFILE_SUCCESS', 'Erfolgreich gespeichert!');
define('LANG_PROFILE_SAVE', 'Speichern');
define('LANG_PROFILE_DATA_HDL', 'Profildaten ändern');
define('LANG_PROFILE_DATA_FAIL', 'Speichern fehlgeschlagen!');
define('LANG_PROFILE_EMAIL_HDL', 'E-Mail ändern');
define('LANG_PROFILE_EMAIL_FAIL', 'Speichern fehlgeschlagen!<br>Aktuelles Passwort ist falsch oder E-Mail existiert bereits!');
define('LANG_PROFILE_PASSWORD_HDL', 'Passwort ändern');
define('LANG_PROFILE_PASSWORD_FAIL', 'Speichern fehlgeschlagen!<br>Aktuelles Passwort ist falsch!');

define('CHECK_LOGIN_ERROR_NOT_LOGIN', 'Sie sind nicht eingeloggt');

define('LANG_LOGIN_HDL', 'Login');
define('LANG_LOGIN_SAVE', 'Login');
define('LANG_LOGIN_FORGOTTEN', 'Passwort vergessen');
define('LANG_LOGIN_FAIL', 'Login fehlgeschlagen!');
define('LANG_LOGIN_SUCCESS', 'Login erfolgreich!');

define('LANG_RECIPE_LIST_FILTER_USER', 'Benutzer');
define('LANG_RECIPE_LIST_FILTER_CATEGORY', 'Kategorie');
define('LANG_RECIPE_LIST_FILTER_TAG', 'Tag');
define('LANG_RECIPE_LIST_FILTER_INGREDIENTS', 'Zutaten');
define('LANG_RECIPE_LIST_FILTER_NAME', 'Name');
define('LANG_RECIPE_LIST_FILTER_ERROR', 'Etwas ist schiefgelaufen!');
define('LANG_RECIPE_LIST_ADD', 'Rezept hinzfügen');
define('LANG_RECIPE_LIST_LOAD_DETAIL_ERROR', 'Etwas ist schiefgelaufen!');
define('LANG_RECIPE_LIST_DETAIL_INGREDINTS_ALTERNATIVE', 'oder');

define('LANG_RECIPE_LIST_DELETE_CONFIRM_TITLE', 'Löschen?');
define('LANG_RECIPE_LIST_DELETE_CONFIRM_TEXT', 'Wollen Sie das Rezept {recipe_name} wirklich löschen?');
define('LANG_RECIPE_LIST_DELETE_CONFIRM_CONFIRM', 'Ja');
define('LANG_RECIPE_LIST_DELETE_CONFIRM_CANCEL', 'Nein');
define('LANG_RECIPE_LIST_DELETE_ERROR', 'Etwas ist schiefgelaufen!');


define('LANG_RECIPE_EDITOR_BACK', 'Zurück');
define('LANG_RECIPE_EDITOR_ADD_NEW', 'Neues Rezept hinzufügen');
define('LANG_SELECT2_SELECT', 'Auswählen');
define('LANG_RECIPE_EDITOR_IMAGE', 'Bild');
define('LANG_RECIPE_EDITOR_DEL_IMAGE', 'Bild löschen');
define('LANG_RECIPE_EDITOR_ORIGINAL', 'Original-Text');
define('LANG_RECIPE_EDITOR_PUBLIC', 'Öffentlich');
define('LANG_RECIPE_EDITOR_CATEGORY', 'Kategorie');
define('LANG_RECIPE_EDITOR_TAGLIST', 'Tags');
define('LANG_RECIPE_EDITOR_NAME', 'Name');
define('LANG_RECIPE_EDITOR_PERSONS', 'Anz. Personen');
define('LANG_RECIPE_EDITOR_INGREDIENTS', 'Zutaten');
define('LANG_RECIPE_EDITOR_INGREDIENTS_IS_ALTERNATIVE', 'Oder');
define('LANG_RECIPE_EDITOR_INGREDIENTS_QUANTITY', 'Menge');
define('LANG_RECIPE_EDITOR_INGREDIENTS_UNIT', 'Einheit');
define('LANG_RECIPE_EDITOR_INGREDIENTS_NAME', 'Name');
define('LANG_RECIPE_EDITOR_STEP', 'Schritt ');
define('LANG_RECIPE_EDITOR_SAVE', 'Speichern');
define('LANG_RECIPE_EDITOR_FAIL', 'Speichern fehlgeschlagen!');
define('LANG_RECIPE_EDITOR_FAIL_IMAGE', 'Bildupload fehlgeschlagen!');
define('LANG_RECIPE_EDITOR_SUCCESS', 'Speichern erfolgreich!');