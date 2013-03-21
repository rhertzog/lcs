/*
 $Id: claroline.js 14237 2012-08-08 07:33:44Z zefredz $
 
 Main Claroline javascript library
 */

// Claroline namespace
var Claroline = {};

Claroline.version = '1.11 rev. $Revision: 14237 $';

Claroline.lang = {};

/*
 * Usage : Claroline.setLangArray({
 *  'Not allowed': 'Non autorise'
 * });
 */
Claroline.setLangArray = function ( langArray ) {
    for ( var langVar in langArray ) {
        Claroline.lang[langVar] = langArray[langVar];
    }
}

Claroline.getLang = function(langVar, arr) {
    if ( Claroline.lang[langVar] ){
        str = Claroline.lang[langVar];
        
        for (var i in arr)
        {
            str = str.replace(i, arr[i]);
        }
        return str;
    }
    else {
        return langVar;
    }
}

Claroline.json = {
    isResponse: function( response ) {
        return (typeof response.responseType != 'undefined') && (typeof response.responseBody != 'undefined');
    },
    isError: function( response ) {
        return Claroline.json.isResponse(response) && (response.responseType == 'error');
    },
    isSuccess: function( response ) {
        return Claroline.json.isResponse(response) && (response.responseType == 'success');
    },
    getResponseBody: function( response ) {
        return response.responseBody;
    },
    handleJsonError: function( response ) {
        error = Claroline.json.getResponseBody( response );
        
        var errStr = Claroline.getLang('[Error] ')+error.error;
        
        if ( error.errno ) {
            errStr += '('+error.errno+')';
        }
        
        if ( error.file ) {
            errStr += Claroline.getLang(' in ')+error.file;
            
            if ( error.line ) {
                errStr += Claroline.getLang(' at line ')+error.line;
            }
        }
        
        if ( error.trace ) {
            errStr += '\n\n'+error.trace;
        }
        
        alert( errStr );
    }
};

// here should also come :

// - a standard confirmation box function
// - some object to set up standard environment vars ? (base url (module,...) courseId, userId, groupId, ...)
// - get_icon

function array_indexOf(arr,val)
{
    for ( var i = 0; i < arr.length; i++ )
    {
        if ( arr[i] == val )
        {
            return i;
        }
    }
    return -1;
}

function isDefined(a)
{
    return typeof a != 'undefined';
}

function isNull(a)
{
    return typeof a == 'object' && !a;
}

function dump(arr,level) {
    var dumped_text = "";
    if(!level) level = 0;
    
    //The padding given at the beginning of the line.
    var level_padding = "";
    for(var j=0;j<level+1;j++) level_padding += "    ";
    
    if(typeof(arr) == 'object') { //Array/Hashes/Objects
        for(var item in arr) {
            var value = arr[item];
            
            if(typeof(value) == 'object') { //If it is an array,
                dumped_text += level_padding + "'" + item + "' ...\n";
                dumped_text += dump(value,level+1);
            } else {
                dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
            }
        }
    } else { //Stings/Chars/Numbers etc.
        dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
    }
    return dumped_text;
}
