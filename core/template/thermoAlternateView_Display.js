if (!$("link[href='plugins/thermoAlternateView/desktop/css/thermoAlternateView.css']").length) {
    $('<link rel="stylesheet" type="text/css" href="plugins/thermoAlternateView/desktop/css/thermoAlternateView.css">').appendTo("head");
}

if (!$("script[src='plugins/thermoAlternateView/3rparty/jquery.fittext.js']").length) {
    $('<script type="text/javascript" src="plugins/thermoAlternateView/3rparty/jquery.fittext.js"><//script>').appendTo("head");
}

function initialize (parametres) {

    //Taille du texte
    $(".thermo-temperature").fitText(0.165);
    $(".thermo-consigne").fitText(0.165);

    //Position des boutons mode
    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-mode").css("margin-top", ($(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-cover").innerHeight() - $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-widget-name").innerHeight() - $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-mode").innerHeight()) / 2);

    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .switch-off, .eqLogic[data-eqlogic_uid=" + parametres.uid + "] .switch-on").click(function () {
        var cmdId = $(this).find(".mode-switch").attr("data-cmd_id");
        if (cmdId != undefined) {
            jeedom.cmd.execute({
                id: cmdId
            });
        }
    });


    //Plus / Moins
    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-button").click(function () {
        var cmdId = $(this).attr("data-cmd_id");
        var cmdId2 = $(this).attr("data-cmd_id2");
        var consigne = $(this).attr("data-cmd_val");

        if ($(this).hasClass("button-plus")) {
            var $self = $(this);
            consigne = parseFloat(consigne) + 0.5;

            jeedom.cmd.execute({
                id: cmdId,
                value: {
                    slider: consigne
                },
                success: function (cmds) {
                    //$self.attr("data-cmd_val", consigne)                    
                }
            });
        } else {
            if ($(this).hasClass("button-moins")) {
                var $self = $(this);
                consigne = parseFloat(consigne) - 0.5;

                jeedom.cmd.execute({
                    id: cmdId,
                    value: {
                        slider: consigne
                    },
                    success: function (cmds) {
                        //$self.attr("data-cmd_val", consigne)
                    }
                });
            } else {
                jeedom.cmd.execute({
                    id: cmdId
                });
                if (cmdId2 != undefined) {
                    $(this).attr("data-cmd_id", cmdId2);
                    $(this).attr("data-cmd_id2", cmdId);
                }
            }
        }

    });


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

                case parametres.consigneId:
                    var $domElement = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .thermo-consigne").children();
                    $domElement.text("");

                    this.value.toString().split(/\./).forEach(function (elem) {
                        $domElement.first().text(elem);
                        $domElement = $domElement.next("span").first();
                    });

                    $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .button-plus, .eqLogic[data-eqlogic_uid=" + parametres.uid + "] .button-moins").attr("data-cmd_val", this.value);
                    break;

                case parametres.statutId:
                    this.value === "Arrêté" ? $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .mode-icon use").attr("xlink:href", "") : $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .mode-icon use").attr("xlink:href", "#shape-icon-flame");

                    break;

                case parametres.lockId:
                    this.value === 1 ? $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .lock-icon use").attr("xlink:href", "#shape-lock-on") : $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .lock-icon use").attr("xlink:href", "#shape-lock-off");

                    break;

                case parametres.modeId:
                    var $cmdMode = $(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] [data-mode=" + this.value.toLowerCase() + "]").next(".eqLogic[data-eqlogic_uid=" + parametres.uid + "] .mode-switch");
                    toggleSwitchMode($cmdMode, parametres.uid);
                    break;
            }

        });


    });
}

function toggleSwitchMode(switchElement, uid) {
    //Le switch sur ON passe à OFF
    $(".eqLogic[data-eqlogic_uid=" + uid + "] .switch-on").each(function () {
        $(this).removeClass("switch-on");
        $(this).addClass("switch-off");
        $(this).find(".mode-switch use").attr("xlink:href", "#shape-switch-off");
        $(this).find(".mode-label").removeClass("mode-label-on");
    });

    //On bascule le switch cliqué sur ON
    var $parentSwitch = $(switchElement).closest(".switch-off");
    $parentSwitch.removeClass("switch-off");
    $parentSwitch.addClass("switch-on");
    $(switchElement).find("use").attr("xlink:href", "#shape-switch-on");
    $(switchElement).prev(".mode-label").addClass("mode-label-on");
}