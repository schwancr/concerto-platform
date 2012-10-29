<?php
/*
  Concerto Platform - Online Adaptive Testing Platform
  Copyright (C) 2011-2012, The Psychometrics Centre, Cambridge University

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; version 2
  of the License, and not any of the later versions.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if (!isset($ini)) {
    require_once'../../Ini.php';
    $ini = new Ini();
}
$logged_user = User::get_logged_user();
if ($logged_user == null) {
    echo "<script>location.reload();</script>";
    die(Language::string(278));
}

if (isset($oid)) {
    if (!$logged_user->is_module_writeable($class_name))
        die(Language::string(81));
    if (!$logged_user->is_object_editable($obj))
        die(Language::string(81));
}
else {
    $oid = $_POST['oid'];
    $obj = Test::from_mysql_id($oid);

    $class_name = $_POST['class_name'];

    if (!$logged_user->is_module_writeable($class_name))
        die(Language::string(81));
    if (!$logged_user->is_object_editable($obj))
        die(Language::string(81));
}

$description = Language::string(213);
$loader = null;
if ($obj != null) {
    if (array_key_exists("loader", $_POST) && $_POST['loader'] != 0) {
        $loader = Template::from_mysql_id($_POST['loader']);
    } else {
        $loader = $obj->get_loader_Template();
    }
    if ($loader != null) {
        $description.=" " . Language::string(214) . ":<hr/>" . $loader->get_description();
    }
}
$loader_id = 0;
if ($loader != null)
    $loader_id = $loader->id;
?>

<script>
    $(function(){
        Methods.iniTooltips();
    });
</script>

<fieldset class="padding ui-widget-content ui-corner-all margin">
    <legend>
        <table>
            <tr>
                <td><span class="tooltip spanIcon ui-icon ui-icon-help" title="<?= Language::string(537) ?>"></span></td>
                <td class=""><b><?= Language::string(536) ?></b></td>
            </tr>
        </table>
    </legend>

    <table class="fullWidth">
        <tr>
            <td>
                <span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= htmlspecialchars(Template::strip_html($description), ENT_QUOTES) ?>"></span>
            </td>
            <?php if ($loader != null) { ?>
                <td>
                    <span class="spanIcon ui-icon ui-icon-extlink tooltip" title="<?= Language::string(522) ?>" onclick="Test.uiGoToRelatedObject(Test.sectionTypes.loadTemplate,<?= $loader->id ?>)"></span>
                </td>
            <?php } ?>
            <td class="fullWidth">
                <select id="selectLoaderTemplate" class="fullWidth ui-widget-content ui-corner-all fullWidth" onchange="Test.uiRefreshLoader($(this).val())">
                    <option value="0">&lt;<?= Language::string(538) ?>&gt;</option>
                    <?php
                    $sql = $logged_user->mysql_list_rights_filter("Template", "`name` ASC");
                    $z = mysql_query($sql);
                    while ($r = mysql_fetch_array($z)) {
                        $t = Template::from_mysql_id($r[0]);
                        ?>
                        <option value="<?= $t->id ?>" <?= ($loader_id == $t->id ? "selected" : "") ?>><?= $t->name ?> ( <?= $t->get_system_data() ?> )</option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>

</fieldset>