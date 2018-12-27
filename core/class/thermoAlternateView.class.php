<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class thermoAlternateView extends eqLogic
{
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert()
    {
    }

    public function postInsert()
    {
    }

    public function preSave()
    {
    }

    public function postSave()
    {
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
    }

    public function preRemove()
    {
    }

    public function postRemove()
    {
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin */
    public function toHtml($_version = 'dashboard')
    {
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $version = jeedom::versionAlias($_version);

        $idThermostat = $this->getConfiguration('thermostat');
        $template = $this->getConfiguration('template');
        $isVisibleTempExt = $this->getConfiguration('isvisibletempext');
        $isVisibleConsigne = $this->getConfiguration('isVisibleConsigne');
        $isEnableBackColorStd = $this->getConfiguration('isEnableBackColorStd');
        $isDisabledMode = $this->getConfiguration('isDisabledMode');
        $isDefaultFlameColor = $this->getConfiguration('isDefaultFlameColor');
        $flameColor = $this->getConfiguration('flameColor');
        $isDefaultFanColor = $this->getConfiguration('isDefaultFanColor');
        $fanColor = $this->getConfiguration('fanColor');
        $isDefaultWaitColor = $this->getConfiguration('isDefaultWaitColor');
        $waitColor = $this->getConfiguration('waitColor');
        $isDefaultLockColor = $this->getConfiguration('isDefaultLockColor');
        $lockColor = $this->getConfiguration('lockColor');
        $thermoUnit = $this->getConfiguration('thermoUnit');
        $isVisibleEvent = $this->getConfiguration('isVisibleEvent');
        $thermostat = eqLogic::byId($idThermostat);


        $islocked = 0;
        $mode = "";
        $statut = ""; // Arrêté
        $cmdStatutId = 0;
        $cmdModeId = 0;
        $cmdTempInterId = 0;
        $cmdTempExterId = 0;
        $tempConsigneId = 0;
        $cmdThermostatHisto = "";        
        $cmdTempInterHisto = "";
        $cmdTempExterHisto = "";
        $cmdLockId = 0;
        $cmdUnlockId = 0;
        $cmdThermostatId = 0;
        $tempInterieure = 0;
        $tempExterieure = 0;
        $modes = array();
        $htmlModes = "";
        $htmlModesMobile = "";
        $modeIcon = "";

        //Parcours chaque commande du thermostat
        foreach (cmd::byEqLogicId($idThermostat) as $commande) {
            switch ($commande->getLogicalId()) {
                case "modeAction":
                case "off":
                    if ($commande->getIsVisible()) {
                        $modes[strtolower($commande->getName())] = $commande->getId();
                    }
                    break;
                case "lock":
                    $cmdLockId = $commande->getId();
                    break;
                case "unlock":
                    $cmdUnlockId = $commande->getId();
                    break;
                case "lock_state":
                    $islocked = $commande->execCmd();
                    break;
                case "thermostat":
                    $cmdThermostatId = $commande->getId();
                    break;
                case "status":
                    $cmdStatutId = $commande->getId();
                    $statut = $commande->execCmd();
                    break;
                case "mode":
                    $cmdModeId = $commande->getId();
                    $mode = strtolower($commande->execCmd());
                    break;
                case "temperature":
                    $cmdTempInterId = $commande->getId();
                    $tempInterieure = $commande->execCmd();
                    $cmdTempInterHisto = $commande->getIsHistorized();
                    break;
                case "temperature_outdoor":
                    $cmdTempExterId = $commande->getId();
                    $tempExterieure= $commande->execCmd();
                    $cmdTempExterHisto = $commande->getIsHistorized();                    
                    break;
                case "order":
                    $tempConsigneId = $commande->getId();
                    $tempConsigne = $commande->execCmd();
                    $cmdConsigneHisto = $commande->getIsHistorized();
                    break;
            }
        }
        $modesCompatibles = array('confort', 'eco', 'absent', 'off', 'vacances', 'nuit', 'jour', 'confort-2', 'horsgel');

        if ($isDisabledMode != "1") {
            foreach ($modes as $modeActionName => $modeActionId) {
                $modeEscapeActionName = str_replace(' ', '', $modeActionName);

                if (in_array($modeEscapeActionName, $modesCompatibles)) {
                    $modeIcon = $modeEscapeActionName;
                } else {
                    switch ( $modeEscapeActionName ) {
                        case 'vacance':
                            $modeIcon = 'vacances'; 
                            break;                       
                        default:
                            $modeIcon = 'other';
                    }
                }

                if ($mode == $modeActionName) {
                    $htmlModes = $htmlModes . '<span class="thermo-switch-on thermo-tooltip"><span class="thermo-tooltiptext">' . ucfirst($modeActionName) . '</span><svg data-mode="' . $modeEscapeActionName . '" data-mode-humanname="' . $modeActionName . '" class="thermo-mode-label thermo-mode-label-on"><use xlink:href="plugins/thermoAlternateView/resources/images.svg#shape-icon-' . $modeIcon . '" /></svg><svg class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '><use xlink:href="plugins/thermoAlternateView/resources/images.svg#shape-switch-on" /></svg></span>';
                    $htmlModesMobile = $htmlModesMobile . '<span class="thermo-switch-on"><svg data-mode="' . $modeEscapeActionName . '" data-mode-humanname="' . $modeActionName . '" class="thermo-mode-label thermo-mode-label-on"><use xlink:href="plugins/thermoAlternateView/resources/images.svg#shape-icon-' . $modeIcon . '" /></svg><svg style="display:none;" class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '></svg></span>';
                } else {
                    $htmlModes = $htmlModes . '<span class="thermo-switch-off thermo-tooltip"><span class="thermo-tooltiptext">' . ucfirst($modeActionName) . '</span><svg data-mode="' . $modeEscapeActionName . '" data-mode-humanname="' . $modeActionName . '" class="thermo-mode-label"><use xlink:href="plugins/thermoAlternateView/resources/images.svg#shape-icon-' . $modeIcon . '" /></svg><svg class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '><use xlink:href="plugins/thermoAlternateView/resources/images.svg#shape-switch-off" /></svg></span>';
                    $htmlModesMobile = $htmlModesMobile . '<span class="thermo-switch-off"><svg data-mode="' . $modeEscapeActionName . '" data-mode-humanname="' . $modeActionName . '" class="thermo-mode-label"><use xlink:href="plugins/thermoAlternateView/resources/images.svg#shape-icon-' . $modeIcon . '" /></svg><svg style="display:none;" class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '></svg></span>';
                }
            }
        }

        //Prochaine consigne
        $replace['#nextConsigne-visible#'] = $isVisibleConsigne == "1" ? "bloc" : "none";

        if ( $isVisibleConsigne == '1') {
            $nextEvent = thermoAlternateView::getNextEventCalendar($thermostat, $isVisibleEvent);           
        }

        //Icone de verrouillage
        if ($islocked == 1) {
            $replace['#lock-icon#'] = '#shape-lock-on';
            $replace['#lock-id#'] = $cmdUnlockId;
            $replace['#lock-id2#'] = $cmdLockId;
        } else {
            $replace['#lock-icon#'] = '#shape-lock-off';
            $replace['#lock-id#'] = $cmdLockId;
            $replace['#lock-id2#'] = $cmdUnlockId;
        }

        //Couleur du cadenna
        $replace['#thermo-islockColor#'] = $isDefaultLockColor == "1" || $islocked == 0 ? "" : "fill:" . $lockColor;
        $replace['#thermo-lockColor#'] = $isDefaultLockColor == "1" ? "" : $lockColor;

        //Couleur du widget
        empty($template) || $template === "default" ? $cssTempate = '': $cssTempate = ucfirst($template);
        $replace['#thermo-cover#'] = $isEnableBackColorStd == "1" ? "" : "thermo". $cssTempate ."-cover-color";
        $replace['#thermo-widget-name#'] = $isEnableBackColorStd == "1" ? "" : "thermo". $cssTempate ."-widget-name";
        $replace['#thermo-back#'] = $isEnableBackColorStd == "1" ? "" : "thermo". $cssTempate ."-back-color";

        //Couleur de la flamme
        $replace['#thermo-flameColor#'] = $isDefaultFlameColor == "1" ? "" : $flameColor;
        
        //Couleur du mode suspendu
        $replace['#thermo-waitColor#'] = $isDefaultWaitColor == "1" ? "" : $waitColor;

        //Couleur du mode climatisation
        $replace['#thermo-fanColor#'] = $isDefaultFanColor == "1" ? "" : $fanColor;

        //Modes de chauffe
        if (empty($template) || $template === "default") {
            $template = '';

            if ($replace['#height#'] == "auto" && $replace['#width#'] == "auto") {
                $replace['#height#'] = "300px";
                $replace['#width#'] = "300px";
            }
            
        } else {
            $htmlModes = str_replace("thermo-", "thermo" . $template . "-", $htmlModes);
            $htmlModesMobile = str_replace("thermo-", "thermo" . ucfirst($template) . "-", $htmlModesMobile);
            $template = '_' . $template;

            if ($replace['#height#'] == "auto" && $replace['#width#'] == "auto") {
                $replace['#height#'] = "250px";
                $replace['#width#'] = "380px";
            }
            
        }

        //Commandes Info
        $replace["#statut-id#"] = $cmdStatutId;
        $replace["#mode-id#"] = $cmdModeId;
        $replace["#thermo-unit#"] = $thermoUnit;
        $replace['#thermo-mode#'] = $htmlModes;
        $replace['#thermo-mode-mobile#'] = $htmlModesMobile;
        $replace['#thermostat-id#'] = $cmdThermostatId;
        $replace['#statistics-url#'] = "index.php?v=d&m=thermostat&p=panel&object_id=" . $thermostat->getObject_id();
        
        //Statut
        switch ($statut) {
            case "Chauffage":
                $replace['#statut-icon#'] = "#shape-icon-flame";
                $replace['#statut-color#'] = $isDefaultFlameColor == "1" ? "" : "fill:" . $flameColor;
                break;

            case "Suspendu":
                $replace['#statut-icon#'] = "#shape-icon-wait";
                $replace['#statut-color#'] = $isDefaultWaitColor == "1" ? "" : "fill:" . $waitColor;
                break;

            case "Climatisation":
                $replace['#statut-icon#'] = "#shape-icon-fan";
                $replace['#statut-color#'] = $isDefaultFanColor == "1" ? "" : "fill:" . $fanColor;
                break;

            default:
                $replace['#statut-icon#'] = "";
                $replace['#statut-color#'] = "";
        }

        list($entier, $decimale) = explode(".", $tempInterieure);
        $replace['#tempInterieure1#'] = $entier;
        $replace['#tempInterieure2#'] = $decimale;
        $replace['#tempInterieure-id#'] = $cmdTempInterId;
        $replace['#temperature-value#'] = $tempInterieure;
        $replace['#temperature-histo#'] = $cmdTempInterHisto == "1" ? "history cursor" : "";        

        $replace['#tempExterieure-id#'] = $cmdTempExterId;
        $replace['#tempExterieure-value#'] = $tempExterieure;
        $replace['#tempExterieure-visible#'] = $isVisibleTempExt == "1" ? "bloc" : "none";
        $replace['#tempExterieure-histo#'] = $cmdTempExterHisto == "1" ? "history cursor" : "";      
        

        list($entier, $decimale) = explode(".", $tempConsigne);
        $replace['#tempConsigne1#'] = $entier;
        $replace['#tempConsigne2#'] = $decimale;
        $replace['#consigne-id#'] = $tempConsigneId;        
        $replace['#consigne-value#'] = $tempConsigne;
        $replace['#consigne-histo#'] = $cmdConsigneHisto == "1" ? "history cursor" : "";      
    
        $replace['#thermo-eqLogicId#'] = $idThermostat;      
        $replace['#thermo-nextOccurence#'] = $nextEvent;      

        $parameters = $this->getDisplay('parameters');
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $replace['#' . $key . '#'] = $value;
            }
        }

        return template_replace($replace, getTemplate('core', $version, 'thermoAlternateView_display' . $template, 'thermoAlternateView'));
    }

    public static function getNextEventCalendar($thermostat, $displayName) {

        $return = "";

        try {
            $calendar_events = thermoAlternateView::getLinkCalendar($thermostat);

            if ( count($calendar_events) > 0 ) {
                //Récupération des évènements du calendrier
                $startDate = date('Y-m-d H:i:s', strtotime('-1 days ' . date('Y-m-d H:i:s')));
                $endDate = date('Y-m-d H:i:s', strtotime('+2 days ' . date('Y-m-d H:i:s')));
                $events = calendar_event::calculeEvents($calendar_events, $startDate, $endDate);

                //Récupération de l'évènement en court
                $now = new DateTime();
                $prevEventEnd = new DateTime(date('Y-m-d H:i:s', strtotime('+10 days ' . date('Y-m-d H:i:s'))));

                //tri par date
                usort($events, function($a, $b) {
                    return strtotime($a['start']) - strtotime($b['start']);
                });

                foreach ($events as $event) {
                    $eventStart = new DateTime($event['start']);
                    $eventEnd = new DateTime($event['end']);
                    if ( $eventStart <= $now && $eventEnd > $now ) {
                        $return = $event['end'];
                        break;
                    }
                    else {
                        if ( $eventStart >= $now && $prevEventEnd < $now ) {
                            $return = $event['start'];
                            break;
                        }
                    }
                    $prevEventEnd = $eventEnd;
                }
                $return = "Prochaine consigne " . thermoAlternateView::getHumanDelai($return);

                if ($displayName == true)
                    $return = $return . " (" . trim($event["title"]) . ")";
            }
            else {
                $return = "";
            }
        }
        catch (Exception $e){
            $return = "";
        }
    
        return $return;

    }

    private static function getHumanDelai($date){
        $return = "";
        $now = new DateTime();
        $dateRef = new DateTime($date);

        $diffDays = (int)$dateRef->format('d') - (int)$now->format('d');

        switch( $diffDays ) {
            case 0:
                $return = "à " . $dateRef->format('H:i');
                break;
            case +1:
            $return = "demain à " . $dateRef->format('H:i');
                break;
        }

        return $return;
    }

    private static function getLinkCalendar($thermostat) {
		try {
			$plugin = plugin::byId('calendar');
			if (!is_object($plugin) || $plugin->isActive() != 1) {
			    throw new Exception("Le plugin agenda n'est pas installé !");
			}
		} catch (Exception $e) {
			throw new Exception("Le plugin agenda n'est pas installé !");            
		}
		if (!class_exists('calendar_event')) {
			throw new Exception("Le plugin agenda n'est pas installé !");
        }
        /////// reste à filtrer par agenda actifs.
		$eventsInProgress = array();
		$eventsNotRunning = array();
		foreach ($thermostat->getCmd(null, 'modeAction', null, true) as $mode) {
			foreach (calendar_event::searchByCmd($mode->getId()) as $event) {
                if ($event->getEqLogic()->getIsEnable() == 1) {
                    if ($event->getCmd_param()["in_progress"] == 1)
                        $eventsInProgress[$event->getId()] = $event;
                    else
                        $eventsNotRunning[$event->getId()] = $event;
                }
			}
        }
        /*
		$thermostat_cmd = $thermostat->getCmd(null, 'thermostat');
		if (is_object($thermostat_cmd)) {
			foreach (calendar_event::searchByCmd($thermostat_cmd->getId()) as $event) {
                if ($event->getCmd_param()["in_progress"] == 1)
                    $eventsInProgress[$event->getId()] = $event;
                else
                    $eventsNotRunning[$event->getId()] = $event;
			}
        }
        */

        // Retourne les évènements en cours, sinon les autres (consigne entre deux évènements)
		return (sizeof($eventsInProgress) > 0 ? $eventsInProgress : $eventsNotRunning);
	}

    public static $_widgetPossibility = array('custom' => array(
        'visibility' => true,
        'displayName' => true,
        'optionalParameters' => false,
        'background-color' => true,
        'background-opacity' => true,
        'text-color' => true,
        'border-radius' => true,
        'border' => true,
    ));
     

    /*     * **********************Getteur Setteur*************************** */
}

class thermoAlternateViewCmd extends cmd
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array())
    {
    }

    /*     * **********************Getteur Setteur*************************** */
}
