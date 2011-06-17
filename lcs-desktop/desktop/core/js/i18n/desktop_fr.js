(function($){
    $._i18n = { trans: {}, 'default':  'en', language: 'en' };
    $.i18n = function() {
        var getTrans = function(ns, str) {
            var trans = false;
            // check if string exists in translation
            if ($._i18n.trans[$._i18n.language] 
                && $._i18n.trans[$._i18n.language][ns]
                && $._i18n.trans[$._i18n.language][ns][str]) {
                trans = $._i18n.trans[$._i18n.language][ns][str];
            }
            // or exists in default
            else if ($._i18n.trans[$._i18n['default']] 
                     && $._i18n.trans[$._i18n['default']][ns]
                     && $._i18n.trans[$._i18n['default']][ns][str]) {
                trans = $._i18n.trans[$._i18n['default']][ns][str];
            }
            // return trans or original string
            return trans || str;
        };
        // Set language (accepted formats: en or en-US)
        if (arguments.length < 2) {
            $._i18n.language = arguments[0]; 
            return $._i18n.language;
        }
        else {
            // get translation
            if (typeof(arguments[1]) == 'string') {
                var trans = getTrans(arguments[0], arguments[1]);
                // has variables for string formating
                if (arguments[2] && typeof(arguments[2]) == 'object') {
                    return $.format(trans, arguments[2]);
                }
                else {
                    return trans;
                }
            }
            // set translation
            else {
                var tmp  = arguments[0].split('.');
                var lang = tmp[0];
                var ns   = tmp[1] || 'jQuery';
                if (!$._i18n.trans[lang]) {
                    $._i18n.trans[lang] = {};
                    $._i18n.trans[lang][ns] = arguments[1];
                }
                else {
                    $.extend($._i18n.trans[lang][ns], arguments[1]);
                }
            }
        }
    };
})(jQuery);



$.i18n('en.jqd', {
    'Month': 'Month',
    'Year':  'Year',
    'Day':   'Day',
    'derniere_connexion':  'Derni&egrave;re connexion le : ',
    'nombre_connexions': 'Vous vous &ecirc;tes connect&eacute; ',
    'premiere_connexion':  'F&eacute;licitations, vous venez de vous connecter pour la 1&#232;re fois sur votre espace perso Lcs. Afin de garantir la confidentialit&#233; de vos donn&#233;es, nous vous encourageons, &agrave; changer votre mot de passe <a class=\"open_win ext_link\" href=\"../Annu/mod_pwd.php\" rel=\"annu\" title=\"\">en suivant ce lien... </a>',
    'copier_courriel_info': 'Cliquez sur l\'adresse et apppuyez sur les touches Ctrl + c (Pomme + c pour Mac) pour copier votre adresse courriel',
    'Lcs-Desktop':  'Lcs-Desktop',
    'unicorn':   'There is a unicorn in the garden',
    'z':		'z'
});

$.i18n('fr.jqd', {
    'Month': 'Mois',
    'Year':  'Année',
    'Day':   'Jour',
    'derniere_connexion':  'Derni&egrave;re connexion le : ',
    'nombre_connexions': 'Vous vous &ecirc;tes connect&eacute; ',
    'premiere_connexion':  'F&eacute;licitations, vous venez de vous connecter pour la 1&#232;re fois sur votre espace perso Lcs. Afin de garantir la confidentialit&#233; de vos donn&#233;es, nous vous encourageons, &agrave; changer votre mot de passe <a class=\"open_win ext_link\" href=\"../Annu/mod_pwd.php\" rel=\"annu\" title=\"\">en suivant ce lien... </a>',
    'copier_courriel_info': 'Cliquez sur l\'adresse et apppuyez sur les touches Ctrl + c (Pomme + c pour Mac) pour copier votre adresse courriel',
    'Lcs-Desktop':  'Lcs-Bureau',
    'unicorn':   'Il y a une licorne dans le jardin',
    'z':		'z'
});

$.i18n('es.jqd', {
    'Month': 'Mes',
    'Year':  'Year',
    'Day':   'año',
    'derniere_connexion':  'Last connect : ',
    'nombre_connexions': 'Vous vous &ecirc;tes connect&eacute; ',
    'premiere_connexion':  'Congratulations, it\'s your first connect (waouuu!!) on your own Lcs-Desktop. To ensure the protection of your data, we encourage you to modify your password  <a class=\"open_win ext_link\" href=\"../Annu/mod_pwd.php\" rel=\"annu\" title=\"\">throught this link... </a>',
    'copier_courriel_info': 'Cliquez sur l\'adresse et apppuyez sur les touches Ctrl + c (Pomme + c pour Mac) pour copier votre adresse courriel',
    'Lcs-Desktop':  'Lcs-Oficina',
    'unicorn':   'Hay un unicornio en el jardín',
    'z':		'z'
});

