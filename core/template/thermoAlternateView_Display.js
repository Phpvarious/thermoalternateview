function initialize(parametres, template) {

    //Gestion des fichiers SVG (pour IE)
    svg4everybody();

    if (template === undefined) {
        template = "";
    } else {
        template = template.capitalize();
    }

    if (template === "Dark") {
        initialize_Dark_Template(parametres, template);
    } else {
        initialize_Default_Template(parametres);
    }

    //Position des boutons mode
    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-switch-off, .eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-switch-on").click(function () {
        var cmdId = $(this).find(".thermo" + template + "-mode-switch").attr("data-cmd_id");
        if (cmdId != undefined) {
            jeedom.cmd.execute({
                id: cmdId
            });
        }
    });

    //Statistiques
    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-statistics").click(function () {
        $('#md_modal').dialog({title: "Statistiques"});
        $("#md_modal").load(parametres.statisticsUrl + '&ajax=1', function (){ 
            $("#md_modal .bs-sidebar").parent().hide();
            $("#md_modal #div_object").parent().removeClass("col-lg-10").addClass("col-lg-12");
        }).dialog('open');
    });


    //Plus / Moins
    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-button").click(function () {
        var cmdId = $(this).attr("data-cmd_id");
        var consigne = $(this).attr("data-cmd_val");

        if ($(this).hasClass("thermo" + template + "-button-plus")) {
            var $self = $(this);
            consigne = parseFloat(consigne) + 0.5;

            jeedom.cmd.execute({
                id: cmdId,
                value: {
                    slider: consigne
                }
            });
        } else {
            if ($(this).hasClass("thermo" + template + "-button-moins")) {
                var $self = $(this);
                consigne = parseFloat(consigne) - 0.5;

                jeedom.cmd.execute({
                    id: cmdId,
                    value: {
                        slider: consigne
                    }
                });
            } else {
                jeedom.cmd.execute({
                    id: cmdId
                });
            }
        }

    });

}

function toggleSwitchMode(switchElement, uid, template) {

    //Le switch sur ON passe à OFF
    $(".eqLogic[data-eqlogic_uid=" + uid + "] .thermo" + template + "-switch-on").each(function () {
        $(this).removeClass("thermo" + template + "-switch-on");
        $(this).addClass("thermo" + template + "-switch-off");
        $(this).find(".thermo" + template + "-mode-switch use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-switch-off");
        $(this).find(".thermo" + template + "-mode-label").removeClass("thermo" + template + "-mode-label-on");
    });

    //On bascule le switch cliqué sur ON
    var $parentSwitch = $(switchElement).closest(".thermo" + template + "-switch-off");
    $parentSwitch.removeClass("thermo" + template + "-switch-off");
    $parentSwitch.addClass("thermo" + template + "-switch-on");
    $(switchElement).find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-switch-on");
    $(switchElement).prevAll(".thermo" + template + "-mode-label").addClass("thermo" + template + "-mode-label-on");
}

function initialize_Default_Template(parametres) {

    if (!$("script[src='plugins/thermoAlternateView/3rparty/jquery.fittext.js']").length) {
        $('<script type="text/javascript" src="plugins/thermoAlternateView/3rparty/jquery.fittext.js"><//script>').appendTo("head");
    }

    if (!$("link[href='plugins/thermoAlternateView/desktop/css/thermoAlternateView.css']").length) {
        $('<link rel="stylesheet" type="text/css" href="plugins/thermoAlternateView/desktop/css/thermoAlternateView.css">').appendTo("head");
    }

    //Mise à jour des valeurs
    $('body').on('cmd::update', function (_event, _options) {

        $(_options).each(function () {

            switch (this.cmd_id) {
                case parametres.tempInterieureId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-temperature").children();
                    $domElement.text("");

                    this.value.toString().split(/\./).forEach(function (elem) {
                        $domElement.first().text(elem);
                        $domElement = $domElement.next("span").first();
                    });
                    break;

                case parametres.tempExterieureId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-temperature-ext");
                    $domElement.text(this.value);
                    break;

                case parametres.consigneId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-consigne").children();
                    $domElement.text("");

                    this.value.toString().split(/\./).forEach(function (elem) {
                        $domElement.first().text(elem);
                        $domElement = $domElement.next("span").first();
                    });

                    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-button-plus, .eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-button-moins").attr("data-cmd_val", this.value);

                    //Prochaine consigne
                    setNextEventCalendar(parametres.eqLogicId, $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-next-consigne"));
                    break;

                case parametres.statutId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-mode-icon");

                    switch (this.value) {
                        case "Chauffage":
                            $domElement.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-icon-flame");
                            $domElement.css("fill", parametres.flameColor);
                            break;

                        case "Suspendu":
                            $domElement.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-icon-wait");
                            $domElement.css("fill", parametres.waitColor);
                            break;

                        case "Climatisation":
                            $domElement.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-icon-fan");
                            $domElement.css("fill", parametres.fanColor);
                            break;

                        default:
                            $domElement.find("use").attr("xlink:href", "");
                    }
                    break;

                case parametres.lockId:
                    var $lockIcon = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-lock-icon");
                    var lockId1 = $lockIcon.attr("data-cmd_id");
                    var lockId2 = $lockIcon.attr("data-cmd_id2");

                    $lockIcon.attr("data-cmd_id", lockId2);
                    $lockIcon.attr("data-cmd_id2", lockId1);
                    if (this.value === 1) {
                        $lockIcon.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-lock-on");
                        $lockIcon.css("fill", parametres.lockColor);
                    } else {
                        $lockIcon.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-lock-off");
                        $lockIcon.css("fill", "");
                    }
                    break;

                case parametres.modeId:
                    var escapeMode = this.value.toLowerCase().replace(/ /g, "");
                    var $cmdMode = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] [data-mode=" + escapeMode + "]").nextAll(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-mode-switch");
                    toggleSwitchMode($cmdMode, parametres.uid, "");
                    break;
            }

        });


    });

    setTimeout(() => {
        fitTextTemp("");
    }, 300);

}

function initialize_Dark_Template(parametres, template) {

    if (!$("link[href='plugins/thermoAlternateView/desktop/css/thermoAlternateView_dark.css']").length) {
        $('<link rel="stylesheet" type="text/css" href="plugins/thermoAlternateView/desktop/css/thermoAlternateView_dark.css">').appendTo("head");
    }

    //Ajout des labels sur les modes
    if (parametres.mobile !== true) {
        $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-mode-label").each(function () {
            $(this).after("<span>" + $(this).attr("data-mode-humanname").capitalize() + "</span>");
        });
    }

    //Mise à jour des valeurs
    $('body').on('cmd::update', function (_event, _options) {

        $(_options).each(function () {

            switch (this.cmd_id) {

                case parametres.tempInterieureId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-temperature");
                    $domElement.text(this.value);
                    break;

                case parametres.tempExterieureId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-temperature-ext");
                    $domElement.text(this.value);
                    break;

                case parametres.consigneId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-consigne > span").first();
                    //Consigne
                    $domElement.text(this.value);

                    //Boutons +/-
                    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-button-plus, .eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-button-moins").attr("data-cmd_val", this.value);

                    //Prochaine consigne
                    setNextEventCalendar(parametres.eqLogicId, $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-next-consigne"));
                    break;

                case parametres.statutId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-mode-icon");

                    switch (this.value) {
                        case "Chauffage":
                            $domElement.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-icon-flame");
                            $domElement.css("fill", parametres.flameColor);
                            break;

                        case "Suspendu":
                            $domElement.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-icon-wait");
                            $domElement.css("fill", parametres.waitColor);
                            break;

                        case "Climatisation":
                            $domElement.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-icon-fan");
                            $domElement.css("fill", parametres.fanColor);
                            break;


                        default:
                            $domElement.find("use").attr("xlink:href", "");
                    }
                    break;

                case parametres.lockId:
                    var $lockIcon = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo" + template + "-lock-icon");
                    var lockId1 = $lockIcon.attr("data-cmd_id");
                    var lockId2 = $lockIcon.attr("data-cmd_id2");

                    $lockIcon.attr("data-cmd_id", lockId2);
                    $lockIcon.attr("data-cmd_id2", lockId1);
                    if (this.value === 1) {
                        $lockIcon.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-lock-on");
                        $lockIcon.css("fill", parametres.lockColor);
                    } else {
                        $lockIcon.find("use").attr("xlink:href", "plugins/thermoAlternateView/resources/images.svg#shape-lock-off");
                        $lockIcon.css("fill", "");
                    }

                    break;

                case parametres.modeId:
                    var escapeMode = this.value.toLowerCase().replace(/ /g, "");
                    var $cmdMode = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] [data-mode=" + escapeMode + "]").nextAll(".thermo" + template + "-mode-switch");
                    toggleSwitchMode($cmdMode, parametres.uid, template);
                    break;
            }

        });


    });

}

function fitTextTemp(template) {

    //Taille du texte
    $(".thermo" + template + "-temperature").fitText(0.165);
    $(".thermo" + template + "-consigne").fitText(0.165);
    //$(".thermo" + template + "-temperature-ext").fitText(0.165);
}

String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

// Récupère l'heure de la prochaine consigne
function setNextEventCalendar(eqLogicId, $domElement) {
    if ($domElement.css('display') != "none") {
        $.ajax({
            type: "POST",
            url: "plugins/thermoAlternateView/core/ajax/thermoAlternateView.ajax.php",
            data: {
                action: "getNextEventCalendar",
                eqLogic_id: eqLogicId
            },
            dataType: 'json',
            success: function (data) {
                if (data.state === 'ok') {
                    $domElement.text(data.result);
                }
            }
        });
    }
}