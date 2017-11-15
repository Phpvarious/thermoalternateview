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
        $isEnableBackColorStd = $this->getConfiguration('isEnableBackColorStd');
        $isDisabledMode = $this->getConfiguration('isDisabledMode');
        $isDefalutFlameColor = $this->getConfiguration('isDefalutFlameColor');
        $flameColor = $this->getConfiguration('flameColor');
        $thermostat = eqLogic::byId($idThermostat);

        $islocked = 0;
        $mode = "";
        $statut = ""; // Arrêté
        $cmdStatutId = 0;
        $cmdModeId = 0;
        $cmdTempInterId = 0;
        $cmdTempExterId = 0;
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
                    break;
                case "temperature_outdoor":
                    $cmdTempExterId = $commande->getId();
                    $tempExterieure= $commande->execCmd();
                    break;
                case "order":
                    $tempConsigne = $commande->execCmd();
                    break;
            }
        }
        $modesCompatibles = array('confort', 'eco', 'absent', 'off', 'vacances');

        if ($isDisabledMode != "1") {
            foreach ($modes as $modeActionName => $modeActionId) {
                if (in_array($modeActionName, $modesCompatibles)) {
                    $modeIcon = $modeActionName;
                } else {
                    $modeIcon = 'other';
                }

                if ($mode == $modeActionName) {
                    $htmlModes = $htmlModes . '<span class="thermo-switch-on thermo-tooltip"><span class="thermo-tooltiptext">' . ucfirst($modeActionName) . '</span><svg data-mode="' . $modeActionName . '" class="thermo-mode-label thermo-mode-label-on"><use xlink:href="#shape-icon-' . $modeIcon . '" /></svg><svg class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '><use xlink:href="#shape-switch-on" /></svg></span>';
                    $htmlModesMobile = $htmlModesMobile . '<span class="thermo-switch-on"><svg data-mode="' . $modeActionName . '" class="thermo-mode-label thermo-mode-label-on"><use xlink:href="#shape-icon-' . $modeIcon . '" /></svg><svg style="display:none;" class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '></svg></span>';
                } else {
                    $htmlModes = $htmlModes . '<span class="thermo-switch-off thermo-tooltip"><span class="thermo-tooltiptext">' . ucfirst($modeActionName) . '</span><svg data-mode="' . $modeActionName . '" class="thermo-mode-label"><use xlink:href="#shape-icon-' . $modeIcon . '" /></svg><svg class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '><use xlink:href="#shape-switch-off" /></svg></span>';
                    $htmlModesMobile = $htmlModesMobile . '<span class="thermo-switch-off"><svg data-mode="' . $modeActionName . '" class="thermo-mode-label"><use xlink:href="#shape-icon-' . $modeIcon . '" /></svg><svg style="display:none;" class="thermo-mode-switch" data-cmd_id=' . $modeActionId . '></svg></span>';
                }
            }
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

        //Couleur du widget
        empty($template) || $template === "default" ? $cssTempate = '': $cssTempate = ucfirst($template);
        $replace['#thermo-cover#'] = $isEnableBackColorStd == "1" ? "" : "thermo". $cssTempate ."-cover-color";
        $replace['#thermo-widget-name#'] = $isEnableBackColorStd == "1" ? "" : "thermo". $cssTempate ."-widget-name";
        $replace['#thermo-back#'] = $isEnableBackColorStd == "1" ? "" : "thermo". $cssTempate ."-back-color";

        //Couleur de la flamme
        $replace['#thermo-flameColor#'] = $isDefalutFlameColor == "1" ? "" : "fill:" . $flameColor;
        

        //Modes de chauffe
        if (empty($template) || $template === "default") {
            $template = '';
        } else {
            $htmlModes = str_replace("thermo-", "thermo" . $template . "-", $htmlModes);
            $htmlModesMobile = str_replace("thermo-", "thermo" . ucfirst($template) . "-", $htmlModesMobile);
            $template = '_' . $template;
        }

        //Commandes Info
        $replace["#statut-id#"] = $cmdStatutId;
        $replace["#mode-id#"] = $cmdModeId;
        $replace['#thermo-mode#'] = $htmlModes;
        $replace['#thermo-mode-mobile#'] = $htmlModesMobile;
        $replace['#consigne-id#'] = $cmdThermostatId;
        $replace['#chauffe-icon#'] = $statut == "Arrêté" ? "" : "#shape-icon-flame";
        $replace['#statistics-url#'] = "window.location = '/index.php?v=d&m=thermostat&p=panel&object_id=" . $thermostat->getObject_id() . "'";
        
        list($entier, $decimale) = explode(".", $tempInterieure);
        $replace['#tempInterieure1#'] = $entier;
        $replace['#tempInterieure2#'] = $decimale;
        $replace['#tempInterieure-id#'] = $cmdTempInterId;
        $replace['#temperature-value#'] = $tempInterieure;

        $replace['#tempExterieure-id#'] = $cmdTempExterId;
        $replace['#tempExterieure-value#'] = $tempExterieure;
        $replace['#tempExterieure-visible#'] = $isVisibleTempExt == "1" ? "bloc" : "none";

        list($entier, $decimale) = explode(".", $tempConsigne);
        $replace['#tempConsigne1#'] = $entier;
        $replace['#tempConsigne2#'] = $decimale;
        $replace['#consigne-value#'] = $tempConsigne;

        $parameters = $this->getDisplay('parameters');
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $replace['#' . $key . '#'] = $value;
            }
        }

        return template_replace($replace, getTemplate('core', $version, 'thermoAlternateView_display' . $template, 'thermoAlternateView'));
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
