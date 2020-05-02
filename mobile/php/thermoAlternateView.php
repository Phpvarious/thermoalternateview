<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'thermoAlternateView');
$eqLogics = eqLogic::byType('thermoAlternateView');
?>

<div class="row row-overflow">
    <div class="col-md-2 col-md-3 col-md-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un thermoAlternateView}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%" /></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"  style="' . $opacity . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-md-10 col-md-9 col-md-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes thermoAlternateView}}</legend>
        <span style="float:right;">v2.7</span>
        <legend><i class="fa fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction" data-action="add">
                <i class="fa fa-plus-circle" style="color:#642940;"></i>
                <br>
                <span>{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fa fa-wrench"></i>
                <br>
                <span>{{Configuration}}</span>
            </div>
        </div>
        <legend><i class="fa fa-table"></i> {{Configuration des widgets}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
                echo "<center>";
                echo '<img src="plugins/thermoAlternateView/plugin_info/thermoAlternateView_icon.png" height="105" width="95" />';
                echo "</center>";
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="col-md-10 col-md-9 col-md-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
        <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#optiontab" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Options}}</a></li>
            <!-- <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li> -->
        </ul>

        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <form class="form-horizontal">
                    <fieldset>
                        <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}} <i style="display:none;" class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Nom de l'équipement thermoAlternateView}}</label>
                            <div class="col-md-3">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement thermoAlternateView}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Objet parent}}</label>
                            <div class="col-md-3">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    foreach (jeeObject::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">{{Catégorie}}</label>
                            <div class="col-md-9">
                                <?php
                                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                    echo '<label class="checkbox-inline">';
                                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                    echo '</label>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label"></label>
                                <div class="col-md-8">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked />{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked />{{Visible}}</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-6 control-label">{{Thermostat}}</label>
                                <div class="col-md-6">

                                    <select required class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="thermostat">
                                        <option value="" disabled selected>{{Sélectionnez un thermostat}}</option>
                                        <?php
                                        foreach (eqLogic::byType("Thermostat") as $eqLogic) {
                                            if ($eqLogic->getIsEnable()) {
                                                echo '<option value="' . $eqLogic->getId() . '">' . $eqLogic->getName() . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-6 control-label">{{Template}}</label>
                                <div class="col-md-6">
                                    <select required class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="template">
                                        <option value="" disabled selected>{{Sélectionnez un Visuel}}</option>
                                        <option value="default">Light (Défaut)</option>
                                        <option value="dark">Dark</option>
                                        <option value="darkatmo">Darkatmo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-xs-offset-3 col-md-3 col-md-offset-0">
                            <img id="templatePreview" src="" style="width:100%;display:none;">
                        </div>
                    </fieldset>
                </form>
            </div>

            <div role="tabpanel" class="tab-pane" id="optiontab">
                <form class="form-horizontal">
                    <fieldset>
                        <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}</legend>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Unité}}</label>
                            <div class="col-md-3">
                                <input type="text" id="thermoUnit" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="thermoUnit" placeholder="{{Unité du thermostat}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Utiliser la couleur de fond du widget}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isEnableBackColorStd">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Température extérieure visible}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isvisibletempext" checked>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Hygrométrie visible}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isvisibleHygrometrie" checked>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Puissance visible}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isvisiblePuissance" checked>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Verrou visible}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isvisibleVerrou" checked>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Statistiques visible}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isvisibleStatistiques" checked>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Masquer les modes}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isDisabledMode">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Afficher la prochaine consigne (agenda)}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isVisibleConsigne">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Afficher le nom de l'évènement (agenda)}}</label>
                            <div class="col-md-3 form-check-input">
                                <input type="checkbox" required class="eqLogicAttr" data-l1key="configuration" data-l2key="isVisibleEvent">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Couleur icône mode chauffage}}</label>
                            <div class="col-md-3 form-check-input">
                                <label>Defaut <input type="checkbox" class="eqLogicAttr color-default" data-l1key="configuration" data-l2key="isDefaultFlameColor" checked></label>
                                <input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="flameColor">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Couleur icône mode climatisation}}</label>
                            <div class="col-md-3 form-check-input">
                                <label>Defaut <input type="checkbox" class="eqLogicAttr color-default" data-l1key="configuration" data-l2key="isDefaultFanColor" checked></label>
                                <input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="fanColor">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Couleur icône mode suspendu}}</label>
                            <div class="col-md-3 form-check-input">
                                <label>Defaut <input type="checkbox" class="eqLogicAttr color-default" data-l1key="configuration" data-l2key="isDefaultWaitColor" checked></label>
                                <input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="waitColor">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Couleur icône verrouillé}}</label>
                            <div class="col-md-3 form-check-input">
                                <label>Defaut <input type="checkbox" class="eqLogicAttr color-default" data-l1key="configuration" data-l2key="isDefaultLockColor" checked></label>
                                <input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="lockColor">
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'thermoAlternateView', 'js', 'thermoAlternateView'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>