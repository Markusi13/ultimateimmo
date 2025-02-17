<?php
/*
 * Copyright (C) 2019-2020 Fabien Fernandes Alves   <fabien@code42.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *    \file       htdocs/gestionparc/template/index.php
 *    \ingroup    gestionparc
 *    \brief      Home page of gestionparc top menu
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) { $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--;
}
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) { $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
}
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) { $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) { $res=@include "../main.inc.php";
}
if (! $res && file_exists("../../main.inc.php")) { $res=@include "../../main.inc.php";
}
if (! $res && file_exists("../../../main.inc.php")) { $res=@include "../../../main.inc.php";
}
if (! $res) { die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
dol_include_once('ultimateimmo/class/gp_infobox.class.php');
dol_include_once('ultimateimmo/class/gp_modele_boxes.class.php');
dol_include_once('ultimateimmo/lib/dashboard.lib.php');

// If not defined, we select menu "home"
$_GET['mainmenu']=GETPOST('mainmenu', 'aZ09')?GETPOST('mainmenu', 'aZ09'):'home';
$action=GETPOST('action', 'aZ09');

$hookmanager->initHooks(array('index'));

// Fixed global boxes
$globalboxes = array();

// Color theme : (#96BBBB, #F2E3BC, #618985, #C19875)
if ($user->rights->ultimateimmo->device->read) {
    $globalboxes[] = array('name' => $langs->trans('PROPERTY'), 'color' =>'#96BBBB',
        'url' => dol_buildpath('/ultimateimmo/device_list.php', 1),
        'url_add' => dol_buildpath('/ultimateimmo/device_card.php?action=create', 1),
        'right' => $user->rights->ultimateimmo->device->write,
        'lines' => array(
            array('title' => $langs->trans('NbDevicesUnderManagement'), 'value' => getDevicesNumberUnderContract(), 'url' => dol_buildpath('/ultimateimmo/device_list.php?search_under_management=1', 1)),
            array('title' => $langs->trans('NbDevicesWithoutManagement'), 'value' => getDevicesNumberWithoutContract(), 'url' => dol_buildpath('/ultimateimmo/device_list.php?search_under_management=-1', 1)),
            array('title' => $langs->trans('Total'), 'value' => getDevicesNumber(), 'url' => dol_buildpath('/ultimateimmo/device_list.php', 1))
        )
    );
}

if ($user->rights->ultimateimmo->user->read) {
    $globalboxes[] = array('name' => $langs->trans('RENTER'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_USER, 'icon' => 'fa-user',
        'url' => dol_buildpath('/gestionparc/contact_list.php', 1),
        'url_add' => dol_buildpath('/gestionparc/contact_card.php?action=create', 1),
        'right' => $user->rights->gestionparc->user->write,
        'lines' => array(
            array('title' => $langs->trans('Total'), 'value' => getUsersNumber(), 'url' => dol_buildpath('/gestionparc/contact_list.php', 1)),
        )
    );
}

if ($user->rights->gestionparc->application->read) {
    $globalboxes[] = array('name' => $langs->trans('RENT'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_APP, 'icon' => 'fa-mobile',
        'url' => dol_buildpath('/gestionparc/application_list.php', 1),
        'url_add' => dol_buildpath('/gestionparc/application_card.php?action=create', 1),
        'right' => $user->rights->gestionparc->application->write,
        'lines' => array(
            array('title' => $langs->trans('Total'), 'value' => getApplicationsNumber(), 'url' => dol_buildpath('/gestionparc/application_list.php', 1)),
        )
    );
}

if ($user->rights->gestionparc->adresse->read) {
    $globalboxes[] = array('name' => $langs->trans('ADDRESSES'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_ADDR, 'icon' => 'fa-code-fork',
        'url' => dol_buildpath('/gestionparc/address_list.php', 1),
        'url_add' => dol_buildpath('/gestionparc/address_card.php?action=create', 1),
        'right' => $user->rights->gestionparc->adresse->write,
        'lines' => array(
            array('title' => $langs->trans('Total'), 'value' => getAddressesNumber(), 'url' => dol_buildpath('/gestionparc/address_list.php', 1)),
        )
    );
}

if ($user->rights->gestionparc->auth->read) {
    $globalboxes[] = array('name' => $langs->trans('AUTHENTICATIONS'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_AUTH, 'icon' => 'fa-key',
        'url' => dol_buildpath('/gestionparc/auth_list.php', 1),
        'lines' => array(
            array('title' => $langs->trans('Total'), 'value' => getAuthenticationsNumber(), 'url' => dol_buildpath('/gestionparc/auth_list.php', 1)),
        )
    );
}

if ($user->rights->infoextranet->user->read) {
    $globalboxes[] = array('name' => $langs->trans('USERS'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_USER, 'icon' => 'fa-user',
        'lines' => array(
            array('title' => $langs->trans('Total'), 'value' => getUsersNumber(), 'url' => dol_buildpath('/infoextranet/contact_list.php', 1)),
        )
    );
}

if (!empty($conf->contrat->enabled) && $user->rights->contrat->lire) {
    $globalboxes[] = array('name' => $langs->trans('CONTRACTS'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_CONTRACT, 'icon' => 'fa-plug',
        'url' => dol_buildpath('/contrat/list.php', 1),
        'url_add' => dol_buildpath('/contrat/card.php?action=create', 1),
        'right' => $user->rights->contrat->creer,
        'lines' => array(
            array('title' => $langs->trans('Total'), 'value' => getContractsNumber(), 'url' => dol_buildpath('/contrat/list.php', 1)),
        )
    );
}

if (!empty($conf->ficheinter->enabled) && $conf->global->GESTIONPARC_INTER_LINK && $user->rights->ficheinter->lire) {
    $globalboxes[] = array('name' => $langs->trans('INTERVENTIONS'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_INTER, 'icon' => 'fa-ambulance',
        'url' => dol_buildpath('/fichinter/list.php', 1),
        'url_add' => dol_buildpath('/fichinter/card.php?action=create', 1),
        'right' => $user->rights->ficheinter->creer,
        'lines' => array(
            array('title' => $langs->trans('Total'), 'value' => getInterventionsNumber(), 'url' => dol_buildpath('/fichinter/list.php', 1)),
        )
    );
}

if (!empty($conf->ticket->enabled) && $conf->global->GESTIONPARC_TICKET_LINK && $user->rights->ticket->read) {
    require_once DOL_DOCUMENT_ROOT.'/ticket/class/ticket.class.php';
    $globalboxes[] = array('name' => $langs->trans('TICKETS'), 'color' => '#'.$conf->global->GESTIONPARC_COLOR_TICKET, 'icon' => 'fa-ticket',
        'url' => dol_buildpath('/ticket/list.php', 1),
        'url_add' => dol_buildpath('/ticket/card.php?action=create', 1),
        'right' => $user->rights->ticket->write,
        'lines' => array(
            array('title' => $langs->trans('NotRead'), 'value' => getTicketsNumber(Ticket::STATUS_NOT_READ), 'url' => dol_buildpath('/ticket/list.php', 1)),
            array('title' => $langs->trans('InProgress'), 'value' => getTicketsNumber(Ticket::STATUS_IN_PROGRESS), 'url' => dol_buildpath('/ticket/list.php', 1)),
            array('title' => $langs->trans('Assigned'), 'value' => getTicketsNumber(Ticket::STATUS_ASSIGNED), 'url' => dol_buildpath('/ticket/list.php', 1)),
            array('title' => $langs->trans('Waiting'), 'value' => getTicketsNumber(Ticket::STATUS_WAITING), 'url' => dol_buildpath('/ticket/list.php', 1)),
        )
    );
}

/*
 * Actions
 */

// Check if company name is defined (first install)
if (!isset($conf->global->MAIN_INFO_SOCIETE_NOM) || empty($conf->global->MAIN_INFO_SOCIETE_NOM))
{
    header("Location: ".DOL_URL_ROOT."/admin/index.php?mainmenu=home&leftmenu=setup&mesg=setupnotcomplete");
    exit;
}
if (count($conf->modules) <= (empty($conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING)?1:$conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING))	// If only user module enabled
{
    header("Location: ".DOL_URL_ROOT."/admin/index.php?mainmenu=home&leftmenu=setup&mesg=setupnotcomplete");
    exit;
}
if (GETPOST('addbox'))	// Add box (when submit is done from a form when ajax disabled)
{
    $zone=GETPOST('areacode', 'aZ09');
    $userid=GETPOST('userid', 'int');
    $boxorder=GETPOST('boxorder', 'aZ09');
    $boxorder.=GETPOST('boxcombo', 'aZ09');

    $result=GPInfoBox::saveboxorder($db, $zone, $boxorder, $userid);
    if ($result > 0) setEventMessages($langs->trans("BoxAdded"), null);
}

/*
 * View
 */

if (! is_object($form)) $form=new Form($db);

// Translations
$langs->loadLangs(array("admin", "ultimateimmo@gultimateimmo"));

// Title
$title = $langs->trans("GPDashboard");
if (! empty($conf->global->MAIN_APPLICATION_TITLE)) $title=$langs->trans("HomeArea").' - '.$conf->global->MAIN_APPLICATION_TITLE;

llxHeader('', $title);
$resultboxes = GPGetBoxesArea($user, "0");    // Load $resultboxes (selectboxlist + boxactivated + boxlista + boxlistb)
$morehtmlright = $resultboxes['selectboxlist'];

print load_fiche_titre($langs->trans("GPDashboard"), $morehtmlright, 'gestionparc_minimized@gestionparc');
print '<div class="dashboardBtnContainer">'.$button.'</div>';

/*
 * Demo text
 */
if ($conf->global->GESTIONPARC_DEMO_ACTIVE == 1 && !empty($conf->global->GESTIONPARC_DEMO_HOME)) {
    print '<div class="gp-demo-div">';
    print $conf->global->GESTIONPARC_DEMO_HOME;
    print '</div>';
    print '<div class="clearboth"></div>';
}

/*
 * Global synthesis
 */

print '<div class="fichecenter gp-grid">';

foreach ($globalboxes as $globalbox) {
    print '<div class="gp-card">';
    print '<div class="gp-left-side" style="background-color: '.$globalbox['color'].';"><i class="fa '.$globalbox['icon'].' icon"></i></div>';
    print '<div class="gp-right-side"><div class="inner"><b style="color: '.$globalbox['color'].';">'.$globalbox['name'].'</b>';
    if (!empty($globalbox['url_add']) && $globalbox['right'])
        print '<a href="'.$globalbox['url_add'].'" class="gp-rounded-btn"><i class="fa fa-plus-circle fa-2x" style="color: '.$globalbox['color'].';"></i></a>';
    foreach ($globalbox['lines'] as $line) {
        print '<div class="line-info">'.$line['title'].' : <a href="'.$line['url'].'"><span style="background-color: '.$globalbox['color'].'">' . $line['value'] . '</span></a></div>';
    }
    print '</div></div>';
    print '</div>';
}

print '</div>';

// Separator
print '<div class="clearboth"></div>';

print '<div class="fichecenter fichecenterbis">';

/*
 * Show boxes
 */

$boxlist.='<div class="twocolumns">';

$boxlist.='<div class="firstcolumn fichehalfleft boxhalfleft" id="boxhalfleft">';

$boxlist.=$boxwork;
$boxlist.=$resultboxes['boxlista'];

$boxlist.= '</div>';

$boxlist.= '<div class="secondcolumn fichehalfright boxhalfright" id="boxhalfright">';

$boxlist.=$boxstat;
$boxlist.=$resultboxes['boxlistb'];

$boxlist.= '</div>';
$boxlist.= "\n";

$boxlist.='</div>';


print $boxlist;

print '</div>';

// Separator
print '<div class="clearboth"></div>';

// End of page
llxFooter();
$db->close();

/**
 * Get array with HTML tabs with boxes of a particular area including personalized choices of user.
 *
 * @param   User    $user           Object User
 * @param   String  $areacode       Code of area for pages ('0'=value for Home page)
 * @return  array
 */
function GPGetBoxesArea($user, $areacode)
{
    global $conf,$langs,$db;

    $confuserzone='MAIN_BOXES_'.$areacode;

    // $boxactivated will be array of boxes enabled into global setup
    // $boxidactivatedforuser will be array of boxes choosed by user

    $selectboxlist='';
    $boxactivated=GPInfoBox::listBoxes($db, 'activated', $areacode, (empty($user->conf->$confuserzone)?null:$user), array(), 0);  // Search boxes of common+user (or common only if user has no specific setup)
    $boxidactivatedforuser=array();
    foreach ($boxactivated as $box)
    {
        if (empty($user->conf->$confuserzone) || $box->fk_user == $user->id) $boxidactivatedforuser[$box->id]=$box->id; // We keep only boxes to show for user
    }

    // Define selectboxlist
    $arrayboxtoactivatelabel=array();
    if (! empty($user->conf->$confuserzone))
    {
        $boxorder='';
        $langs->load("boxes");  // Load label of boxes
        foreach ($boxactivated as $box)
        {
            if (! empty($boxidactivatedforuser[$box->id])) continue;    // Already visible for user
            $label=$langs->transnoentitiesnoconv($box->boxlabel);
            if (preg_match('/graph/', $box->class) && empty($conf->browser->phone))
            {
                $label=$label.' <span class="fa fa-bar-chart"></span>';
            }
            $arrayboxtoactivatelabel[$box->id]=$label; // We keep only boxes not shown for user, to show into combo list
        }
        foreach ($boxidactivatedforuser as $boxid)
        {
            if (empty($boxorder)) $boxorder.='A:';
            $boxorder.=$boxid.',';
        }

        // Class Form must have been already loaded
        $selectboxlist.='<!-- Form with select box list -->'."\n";
        $selectboxlist.='<form id="addbox" name="addbox" method="POST" action="'.$_SERVER["PHP_SELF"].'" style="display:inline">';
        if ((float) DOL_VERSION >= 11) {
            $selectboxlist.='<input type="hidden" name="token" value="' . newToken() . '">';
        } else {
            $selectboxlist='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        }
        $selectboxlist.='<input type="hidden" name="addbox" value="addbox">';
        $selectboxlist.='<input type="hidden" name="userid" value="'.$user->id.'">';
        $selectboxlist.='<input type="hidden" name="areacode" value="'.$areacode.'">';
        $selectboxlist.='<input type="hidden" name="boxorder" value="'.$boxorder.'">';
        $selectboxlist.=Form::selectarray('boxcombo', $arrayboxtoactivatelabel, -1, $langs->trans("ChooseBoxToAdd").'...', 0, 0, '', 0, 0, 0, 'ASC', 'maxwidth150onsmartphone', 0, 'hidden selected', 0, 1);

        if (empty($conf->use_javascript_ajax)) $selectboxlist.=' <input type="submit" class="button" value="'.$langs->trans("AddBox").'">';
        $selectboxlist.='</form>';
        if (! empty($conf->use_javascript_ajax))
        {
            include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
            $selectboxlist.=ajax_combobox("boxcombo");
        }
    }
    // Javascript code for dynamic actions
    if (! empty($conf->use_javascript_ajax))
    {
        $box_file = dol_buildpath('/ultimateimmo/ajax/box.php', 1);

        $selectboxlist.='<script type="text/javascript" language="javascript">
  
             // To update list of activated boxes
             function updateBoxOrder(closing) {
                 var left_list = cleanSerialize(jQuery("#boxhalfleft").sortable("serialize"));
                 var right_list = cleanSerialize(jQuery("#boxhalfright").sortable("serialize"));
                 var boxorder = \'A:\' + left_list + \'-B:\' + right_list;
                 if (boxorder==\'A:A-B:B\' && closing == 1)  // There is no more boxes on screen, and we are after a delete of a box so we must hide title
                 {
                     jQuery.ajax({
                         url: \''.$box_file.'?closing=0&boxorder=\'+boxorder+\'&zone='.$areacode.'&userid=\'+'.$user->id.',
                         async: false
                     });
                     // We force reload to be sure to get all boxes into list
                     window.location.search=\'mainmenu='.GETPOST("mainmenu", "aZ09").'&leftmenu='.GETPOST('leftmenu', "aZ09").'&action=delbox\';
                 }
                 else
                 {
                     jQuery.ajax({
                         url: \''.$box_file.'?closing=\'+closing+\'&boxorder=\'+boxorder+\'&zone='.$areacode.'&userid=\'+'.$user->id.',
                         async: true
                     });
                 }
             }
  
             jQuery(document).ready(function() {
                 jQuery("#boxcombo").change(function() {
                 var boxid=jQuery("#boxcombo").val();
                     if (boxid > 0) {
                         var left_list = cleanSerialize(jQuery("#boxhalfleft").sortable("serialize"));
                         var right_list = cleanSerialize(jQuery("#boxhalfright").sortable("serialize"));
                         var boxorder = \'A:\' + left_list + \'-B:\' + right_list;
                         jQuery.ajax({
                             url: \''.$box_file.'?boxorder=\'+boxorder+\'&boxid=\'+boxid+\'&zone='.$areacode.'&userid='.$user->id.'\',
                             async: false
                         });
                         window.location.search=\'mainmenu='.GETPOST("mainmenu", "aZ09").'&leftmenu='.GETPOST('leftmenu', "aZ09").'&action=addbox&boxid=\'+boxid;
                     }
                 });';
        if (! count($arrayboxtoactivatelabel)) $selectboxlist.='jQuery("#boxcombo").hide();';
        $selectboxlist.='
  
                 jQuery("#boxhalfleft, #boxhalfright").sortable({
                     handle: \'.boxhandle\',
                     revert: \'invalid\',
                     items: \'.boxdraggable\',
                     containment: \'document\',
                     connectWith: \'#boxhalfleft, #boxhalfright\',
                     stop: function(event, ui) {
                         updateBoxOrder(1);  /* 1 to avoid message after a move */
                     }
                 });
  
                 jQuery(".boxclose").click(function() {
                     var self = this;    // because JQuery can modify this
                     var boxid=self.id.substring(8);
                     var label=jQuery(\'#boxlabelentry\'+boxid).val();
                     console.log("We close box "+boxid);
                     jQuery(\'#boxto_\'+boxid).remove();
                     if (boxid > 0) jQuery(\'#boxcombo\').append(new Option(label, boxid));
                     updateBoxOrder(1);  /* 1 to avoid message after a remove */
                 });
  
             });'."\n";

        $selectboxlist.='</script>'."\n";
    }

    // Define boxlista and boxlistb
    $nbboxactivated=count($boxidactivatedforuser);
    $boxlista = '';
    $boxlistb = '';

    if ($nbboxactivated)
    {
        $langs->load("boxes");
        $langs->load("projects");

        $emptybox=new GPModeleBoxes($db);

        $boxlista.="\n<!-- Box left container -->\n";

        // Define $box_max_lines
        $box_max_lines=5;
        if (! empty($conf->global->GESTIONPARC_BOXES_MAXLINES)) $box_max_lines=$conf->global->GESTIONPARC_BOXES_MAXLINES;

        $ii=0;
        foreach ($boxactivated as $key => $box)
        {
            if ((! empty($user->conf->$confuserzone) && $box->fk_user == 0) || (empty($user->conf->$confuserzone) && $box->fk_user != 0)) continue;
            if (empty($box->box_order) && $ii < ($nbboxactivated / 2)) $box->box_order='A'.sprintf("%02d", ($ii+1)); // When box_order was not yet set to Axx or Bxx and is still 0
            if (preg_match('/^A/i', $box->box_order)) // column A
            {
                $ii++;
                //print 'box_id '.$boxactivated[$ii]->box_id.' ';
                //print 'box_order '.$boxactivated[$ii]->box_order.'<br>';
                // Show box
                $box->loadBox($box_max_lines);
                $boxlista.= $box->outputBox();
            }
        }

        if (empty($conf->browser->phone))
        {
            $emptybox->box_id='A';
            $emptybox->info_box_head=array();
            $emptybox->info_box_contents=array();
            $boxlista.= $emptybox->outputBox(array(), array());
        }
        $boxlista.= "<!-- End box left container -->\n";

        $boxlistb.= "\n<!-- Box right container -->\n";

        $ii=0;
        foreach ($boxactivated as $key => $box)
        {
            if ((! empty($user->conf->$confuserzone) && $box->fk_user == 0) || (empty($user->conf->$confuserzone) && $box->fk_user != 0)) continue;
            if (empty($box->box_order) && $ii < ($nbboxactivated / 2)) $box->box_order='B'.sprintf("%02d", ($ii+1)); // When box_order was not yet set to Axx or Bxx and is still 0
            if (preg_match('/^B/i', $box->box_order)) // colonne B
            {
                $ii++;
                //print 'box_id '.$boxactivated[$ii]->box_id.' ';
                //print 'box_order '.$boxactivated[$ii]->box_order.'<br>';
                // Show box
                $box->loadBox($box_max_lines);
                $boxlistb.= $box->outputBox();
            }
        }

        if (empty($conf->browser->phone))
        {
            $emptybox->box_id='B';
            $emptybox->info_box_head=array();
            $emptybox->info_box_contents=array();
            $boxlistb.= $emptybox->outputBox(array(), array());
        }

        $boxlistb.= "<!-- End box right container -->\n";
    }

    return array('selectboxlist'=>count($boxactivated)?$selectboxlist:'', 'boxactivated'=>$boxactivated, 'boxlista'=>$boxlista, 'boxlistb'=>$boxlistb);
}
