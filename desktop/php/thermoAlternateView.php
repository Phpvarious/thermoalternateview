<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('thermoAlternateView');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
        <span style="float:right;">v4.1</span>
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
        <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
        <div class="eqLogicThumbnailContainer">
			<?php
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br/>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			?>
		</div>
    </div>

    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default eqLogicAction btn-sm roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab" data-action="returnToThumbnailDisplay" style="padding:10px 5px !important"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#optiontab" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Options}}</a></li>
        </ul>

        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br/>
                <form class="form-horizontal">
                    <fieldset>
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
                                        <option value="default">Lightatmo (Défaut)</option>
                                        <option value="darkatmo">Darkatmo</option>
                                        <option value="dark">Dark</option>
                                        <option value="simplelight">Simple Light</option>
                                        <option value="simpledark">Simple Dark</option>
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
                <br/>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{Unité}}</label>
                            <div class="col-md-1">
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
                            <label class="col-md-3 control-label">{{Commande d'hygrométrie}}</label>
                            <div class="col-md-6 input-group">
                                <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="cmdHygrometrie"></input>
                                <a class="btn btn-default listEquipementInfo cursor btn-sm input-group-addon" data-input="cmdHygrometrie"><i class="fas fa-list-alt"></i></a>
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