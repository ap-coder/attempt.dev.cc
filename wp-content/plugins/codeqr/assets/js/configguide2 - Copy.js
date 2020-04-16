//table of contents links scroll to header
function subsection_scroll(a, b) {

    add_scroll = 0;
    if (document.getElementById('initial_splash_barcodes_div').value == 0 || jQuery('#ageVerification').css('display') != 'none') {
        jQuery('#selectLinkTD').show();
        jQuery('#printLinkTD').show();
        jQuery('#saveLinkTD').show();
        jQuery('#showLinkTD').show();
        jQuery('#printAVLink').hide();
        jQuery('#saveAVLink').hide();
        jQuery('#printAVLink2').hide();
        jQuery('#saveAVLink2').hide();
        jQuery('#printAVLink4').hide();
        jQuery('#printAVLink3').hide();
        jQuery("#search2").show();
        jQuery("#tree_div").show("blind", 0, 500);
        jQuery('#AVlink').val("");
        jQuery('#ageVerification').hide("blind", 0, 500);
        jQuery("#barcodes_spash_initial").hide("blind", 0, 500);
        jQuery("#barcodes1").show("blind", 0, 500);
        //jQuery("#the_hr2").show("blind", 0, 500);
        document.getElementById('initial_splash_barcodes_div').value = 1;
        add_scroll = 329;
    }


    if (b && b.checked) {
        var topPos = document.getElementById('barcode_subsection' + a).offsetTop;
        jQuery('div.barcodes').animate({
            scrollTop: topPos - 329 + add_scroll
        }, 'slow');
        jQuery('#barcode_subsection' + a).animate({
            color: '#8D1A21'
        }, 800);
        jQuery('#barcode_subsection' + a).animate({
            color: '#808080'
        }, 800);
    }
    if (!b) {
        //alert(a);
        var topPos = document.getElementById('wrapper').offsetTop;
        jQuery('div.barcodes').animate({
            scrollTop: topPos - 329 + add_scroll
        }, 'slow');

        jQuery('input:checkbox').each(function(i) {
            if (this.value == "tocTitle") {
                if (jQuery(this).prop('checked') && jQuery(this).attr('id') != 'primAge' && jQuery(this).attr('id') != 'secAge' && jQuery(this).attr('id') != 'rtc') {
                    jQuery(this).prop('checked', false);
                }
            }
        });

        event.preventDefault();
        event.stopPropagation();
        return false;
    }
}

function hid_vid() {

    jQuery("#vid").hide("blind", 0, 500);
    jQuery("#no_product").show("blind", 0, 500);
    jQuery("#shovidlink").show("blind", 0, 500);
    event.preventDefault();
    event.stopPropagation();
    return false;
}

function hid_vid2() {
    jQuery("#vid").hide("blind", 0, 500);
    jQuery("#description_box").show("blind", 0, 500);
    jQuery("#table_contents").show("blind", 0, 500);
    jQuery("#main_config_section").show("blind", 0, 500);
    jQuery("#shovidlink2").show("blind", 0, 500);
    event.preventDefault();
    event.stopPropagation();
    return false;
}

function thisMovie(movieName) {
    if (navigator.appName.indexOf("Microsoft") != -1) {
        return window[movieName];
    } else {
        return document[movieName];
    }
}

function sho_vid() {
    jQuery("#vid").show("blind", 0, 500);
    jQuery("#no_product").hide("blind", 0, 500);
    jQuery("#shovidlink").hide("blind", 0, 500);
    thisMovie("FLVPlayer").Play();
    event.preventDefault();
    event.stopPropagation();
    return false;
}

function sho_vid2() {
    jQuery("#vid").show("blind", 0, 500);
    jQuery("#description_box").hide("blind", 0, 500);
    jQuery("#table_contents").hide("blind", 0, 500);
    jQuery("#main_config_section").hide("blind", 0, 500);
    jQuery("#shovidlink2").hide("blind", 0, 500);
    thisMovie("FLVPlayer").Play();
    event.preventDefault();
    event.stopPropagation();
    return false;
}

//table of contents links scroll to header
function barcode_scroll(a) {

    var topPos2 = document.getElementById(a).offsetTop;
    jQuery('div.barcodes').animate({
        scrollTop: topPos2 - 329
    }, 'slow');
    jQuery('#' + a).animate({
        backgroundColor: '#8D1A21',
        color: '#fff'
    }, 800);
    jQuery('#' + a).animate({
        backgroundColor: '#fff',
        color: '#808080'
    }, 800);
}

function checkboxes(c, d, search) {

    var select = document.getElementById('select');
    var selectImage = document.getElementById('selectImage');
    var print = document.getElementById('print');
    var print_grey = document.getElementById('print_grey');
    var save = document.getElementById('save');
    var save_grey = document.getElementById('save_grey');

    var max = document.getElementById('hidden').value;
    var state = document.getElementById('hidden2');
    //var show_all = document.getElementById('show_all');
    var show_all_link = document.getElementById('show_all_link');
    var barcodesall = document.getElementById('barcodes-all');
    barcodesall.value = null;
    var count = 0;


    if (c == "show") {
        if (document.getElementById('initial_splash_barcodes_div').value == 0) {
            jQuery("#barcodes_spash_initial").hide("blind", 0, 500);
            document.getElementById('initial_splash_barcodes_div').value = 1;
            //jQuery("#the_hr2").show("blind", 0, 500);
        }
        //hide toc and barcode sections
        if (state.value != "selected") {
            document.getElementById('selectLink').style.display = 'none';
            //document.getElementById('select_all_div').style.display = "none";
            jQuery("#barcodes1").hide("blind", 0, 500);
            jQuery("#tree_div").hide("blind", 0, 500);
            jQuery("#the_hr").hide("blind", 0, 500);
            //show new barcode section
            jQuery("#barcodes2").show("blind", 0, 500);
            select.innerHTML = "";
            state.value = "selected";
            document.getElementById("search_words").style.display = "block";
            document.getElementById("search_words").innerHTML = "<span style='color: #F6F6F6;'>No search.</span>";
        } else if (search != 1) {
            document.getElementById("search_words").style.display = "none";
            select.innerHTML = jQuery("#GTselectall").val();
            state.value = "all";
            document.getElementById('selectLink').style.display = 'inline';
            //document.getElementById('select_all_div').style.display = "block";
            jQuery("#barcodes1").show("blind", 0, 500);
            jQuery("#the_hr").show("blind", 0, 1);


            //show new barcode section
            jQuery("#barcodes2").hide("blind", 0, 500);
            jQuery("#tree_div").show("blind", 0, 500);

        }
    }
    if (c != "show") {
        document.getElementById("SE" + c).style.display = "none";

    }
    if (search != 1) {
        jQuery('input:checkbox').each(function(i) {

            if (this.value != "tocTitle" && this.value.substr(0, 2) != "SE") {
                if (jQuery('#AL' + this.value).prop('checked')) {
                    count++;
                    if (!barcodesall.value) {
                        barcodesall.value = "'" + this.value;
                    } else {
                        barcodesall.value = barcodesall.value + "', '" + this.value;
                    }
                    if (c == "show") {
                        if (!search) {
                            document.getElementById("SE" + this.value).style.display = "block";
                        }
                        document.getElementById("SEL" + this.value).checked = true;

                    }
                } else {
                    if (!search) {
                        document.getElementById("SE" + this.value).style.display = "none";
                    }
                }
            }
        });
    }
    if (search == 1) {
        var searchContent = new RegExp(jQuery('#search_field').val(), "gi");
        jQuery("#show_all").hide();
        document.getElementById("search_words").style.display = "block";
        document.getElementById("search_words").innerHTML = "Searching: " + jQuery('#search_field').val();
        jQuery('input:checkbox').each(function(i) {
            if (this.value != "tocTitle" && this.value.substr(0, 2) != "SE") {
                var barcodeContent = jQuery('#' + this.value + '_description').html().substr(50);
                if (barcodeContent.match(searchContent)) {
                    document.getElementById("SE" + this.value).style.display = "block";
                    if (jQuery('#AL' + this.value).prop('checked')) {
                        jQuery('#SEL' + this.value).prop('checked', true);
                        count++;
                    } else {
                        jQuery('#SEL' + this.value).prop('checked', false);
                    }
                } else {
                    document.getElementById("SE" + this.value).style.display = "none";
                }
            }
        });
        if (jQuery('#ageVerification').css('display') != 'none') {
            jQuery('#selectLinkTD').show();
            jQuery('#printLinkTD').show();
            jQuery('#saveLinkTD').show();
            jQuery('#showLinkTD').show();
            jQuery('#printAVLink').hide();
            jQuery('#saveAVLink').hide();
            jQuery('#printAVLink2').hide();
            jQuery('#saveAVLink2').hide();
            jQuery('#printAVLink4').hide();
            jQuery('#printAVLink3').hide();
            jQuery('#ageVerification').hide("blind", 0, 500);
        }
    }
    barcodesall.value = barcodesall.value + "'";
    //Austin SEction
    if (count == 0) {
        //hide show selected
        //show_all.style.display = "none";
        show_all_link.innerHTML = '';
        if (search != 1) {
            jQuery("#show_all").show();
        }
        //set select/print/save/email to all
        selectImage.setAttribute('src', 'image/checkoff.png?v=1');
        select.innerHTML = "&nbsp;<br />" + jQuery("#GTselectall").val();
        print.innerHTML = "";
        print_grey.innerHTML = '<img src="image/printgrey.png"><br />&nbsp;<br />' + jQuery("#GTprintall").val() + '<br />';

        save.innerHTML = "";
        save_grey.innerHTML = '<img src="image/savegrey.png"><br />&nbsp;<br />' + jQuery("#GTsaveall").val() + '<br />';
        barcodesall.value = "!";


    } else if (count == max) {
        //hide show selected
        //show_all.style.display = "none";
        show_all_link.innerHTML = '';
        if (search != 1) {
            jQuery("#show_all").show();
        }
        //set select to deselect all
        selectImage.setAttribute('src', 'image/nocheckoff.png?v=1');
        select.innerHTML = "&nbsp;<br />" + jQuery("#GTdeselectall").val();
        //set print/save/email to all
        print_grey.innerHTML = '';

        print.innerHTML = "<img src='image/printsoff.png' /><br />&nbsp;<br />" + jQuery("#GTprintall").val();
        save_grey.innerHTML = '';
        save.innerHTML = "<img src='image/saveoff.png' /><br />&nbsp;<br />" + jQuery("#GTsaveall").val();
        barcodesall.value = "!";

    } else {
        //show 'show selected'
        //show_all.style.display = "block";
        jQuery("#show_all").hide();
        show_all_link.innerHTML = '<img src="image/eyeoff.png" /><br />' + jQuery("#GTviewselected").val() + ' (' + count + ')';
        //set select to all
        selectImage.setAttribute('src', 'image/checkoff.png?v=1');
        select.innerHTML = "&nbsp;<br />" + jQuery("#GTselectall").val();
        //set print/save/email to count
        print_grey.innerHTML = '';
        print.innerHTML = "<img src='image/printsoff.png' /><br />&nbsp;<br />" + jQuery("#GTprint").val() + " (" + count + ")";
        save_grey.innerHTML = '';
        save.innerHTML = "<img src='image/saveoff.png' /><br />&nbsp;<br />" + jQuery("#GTsave").val() + " (" + count + ")";

    }
    //if(search != 1) { 
    //document.getElementById("search_words").style.display = "none"; }
    if (c == "show") {

        if (state.value == "selected") {
            document.getElementById('show_all_link').innerHTML = '<img src="image/eyeoff.png" /><br />&nbsp;<br />' + jQuery("#GTviewall").val();
        } else if (count > 0 && count != max) {
            document.getElementById('show_all_link').innerHTML = '<img src="image/eyeoff.png" /><br />' + jQuery("#GTviewselected").val() + ' (' + count + ')';
        }
    }
}

function checkboxes2(e) {

    if (jQuery('#AL' + e).prop('checked')) {
        document.getElementById("AL" + e).checked = false;
    } else {
        document.getElementById("AL" + e).checked = true;
    }

    if (document.getElementById("search_words").innerHTML.substr(0, 2) == "<s") {
        document.getElementById("SE" + e).style.display = "none";
    }
    //alert(document.getElementById("search_words").innerHTML.substr(0, 2));


    var select = document.getElementById('select');
    var print = document.getElementById('print');
    var print_grey = document.getElementById('print_grey');
    var save = document.getElementById('save');
    var save_grey = document.getElementById('save_grey');
    var barcodesall = document.getElementById('barcodes-all');
    barcodesall.value = null;
    var max = document.getElementById('hidden').value;
    var state = document.getElementById('hidden2');
    //var show_all = document.getElementById('show_all');
    var show_all_link = document.getElementById('show_all_link');
    var count = 0;

    jQuery('input:checkbox:checked').each(function(i) {

        if (this.value != "tocTitle" && this.value.substr(0, 2) != "SE") {
            if (!barcodesall.value) {
                barcodesall.value = "'" + this.value;
            } else {
                barcodesall.value = barcodesall.value + "', '" + this.value;
            }
            count++;
        }
    });
    barcodesall.value = barcodesall.value + "'";

    //set select to all
    select.innerHTML = "";
    //set print/save/email to count
    print.innerHTML = "<img src='image/printsoff.png' /><br />&nbsp;<br />" + jQuery("#GTprint").val() + " (" + count + ")";
    save.innerHTML = "<img src='image/saveoff.png' /><br />&nbsp;<br />" + jQuery("#GTsave").val() + " (" + count + ")";
    print_grey.innerHTML = '';
    save_grey.innerHTML = '';

    if (count == 0) {
        //hide show selected
        //show_all.style.display = "none";
        //jQuery("#show_all").hide("puff", 0, 500);
        //set select/print/save/email to all
        //select.innerHTML = "&nbsp;<br />"+jQuery("#GTselectall").val();

        print_grey.innerHTML = '<img src="image/printgrey.png"><br />&nbsp;<br />' + jQuery("#GTprintall").val() + '<br />';
        save_grey.innerHTML = '<img src="image/savegrey.png"><br />&nbsp;<br />' + jQuery("#GTsaveall").val() + '<br />';

        print.innerHTML = "";
        save.innerHTML = "";
        barcodesall.value = "!";
    } else if (count == max) {
        //hide show selected
        //show_all.style.display = "none";
        //show_all_link.innerHTML = '';
        //if(search != 1) { jQuery("#show_all").show(); }
        //set select to deselect all
        selectImage.setAttribute('src', 'image/nocheckoff.png?v=1');
        select.innerHTML = "&nbsp;<br />" + jQuery("#GTdeselectall").val();
        //set print/save/email to all
        print_grey.innerHTML = '';

        print.innerHTML = "<img src='image/printsoff.png' /><br />&nbsp;<br />" + jQuery("#GTprintall").val();
        save_grey.innerHTML = '';
        save.innerHTML = "<img src='image/saveoff.png' /><br />&nbsp;<br />" + jQuery("#GTsaveall").val();
        barcodesall.value = "!";

    }
}

//**********************************************************************************************
//NEW CR5000RTC Settings

function check(checkbox) {
    if (jQuery('#' + checkbox).prop('checked')) {
        jQuery('#' + checkbox + 'Input').prop("disabled", false); // Element(s) are now enabled.
    } else if (checkbox != "rtc") {
        jQuery('#' + checkbox + 'Input').prop("disabled", true); // Element(s) are now disabled.
    }

    if (checkbox == 'pin') {
        if (jQuery('#' + checkbox).prop('checked')) {
            //jQuery('#unlockcheck').prop("disabled", true);
            jQuery('#pinForm').prop("disabled", false);
            jQuery('#unlockcheck').prop("checked", false);
            jQuery('#primAge').prop("disabled", false);
            jQuery('#secAge').prop("disabled", false);
            jQuery('#rtc').prop("disabled", false);
            if (jQuery('#primAge').prop('checked')) {
                jQuery('#primAgeInput').prop("disabled", false); // Element(s) are now enabled.
            } else {
                jQuery('#primAgeInput').prop("disabled", true); // Element(s) are now disabled.
            }
            if (jQuery('#secAge').prop('checked')) {
                jQuery('#secAgeInput').prop("disabled", false); // Element(s) are now enabled.
            } else {
                jQuery('#secAgeInput').prop("disabled", true); // Element(s) are now disabled.
            }
        } else {
            jQuery('#pinForm').prop("disabled", true);
            //jQuery('#unlockcheck').prop("disabled", false);
        }
    }
    if (checkbox == 'unlockcheck') {
        if (jQuery('#' + checkbox).prop('checked')) {
            //jQuery('#pin').prop("disabled", true);
            jQuery('#pinForm').prop("disabled", false);
            jQuery('#pin').prop("checked", false);
            jQuery('#primAge').prop("disabled", true);
            jQuery('#primAgeInput').prop("disabled", true);
            jQuery('#secAge').prop("disabled", true);
            jQuery('#secAgeInput').prop("disabled", true);
            jQuery('#rtc').prop("disabled", true);
        } else {
            jQuery('#pinForm').prop("disabled", true);
            //jQuery('#pin').prop("disabled", false);
            jQuery('#primAge').prop("disabled", false);
            jQuery('#secAge').prop("disabled", false);
            jQuery('#rtc').prop("disabled", false);
            if (jQuery('#primAge').prop('checked')) {
                jQuery('#primAgeInput').prop("disabled", false); // Element(s) are now enabled.
            } else {
                jQuery('#primAgeInput').prop("disabled", true); // Element(s) are now disabled.
            }
            if (jQuery('#secAge').prop('checked')) {
                jQuery('#secAgeInput').prop("disabled", false); // Element(s) are now enabled.
            } else {
                jQuery('#secAgeInput').prop("disabled", true); // Element(s) are now disabled.
            }
        }
    }

    //If any are checked, make buttons to print/save live. Else, grey out.
    if (jQuery('#rtc').prop('checked') || jQuery('#primAge').prop('checked') || jQuery('#secAge').prop('checked')) {
        if (!checkbox) {
            jQuery('#printAVLink').hide();
            jQuery('#saveAVLink').hide();
            jQuery('#printAVLink2').show();
            jQuery('#saveAVLink2').show();
            //jQuery('#printAVLink3').hide();
            //jQuery('#printAVLink4').show(); 
        }
    } else {
        //disable buttons
        jQuery('#printAVLink2').hide();
        jQuery('#saveAVLink2').hide();
        jQuery('#printAVLink').show();
        jQuery('#saveAVLink').show();
        //jQuery('#printAVLink4').show();
        //jQuery('#printAVLink3').show();
    }
}

function generate(option, flag) {

    GTsetprimaryage = "Under Age";
    GTsetsecondaryage = "Over Age";
    GTsetrealtime = "Real Time Clock";
    GTsetpin = "Pin";
    if (flag) {
        //alert(flag);
        if (flag == "es_") {
            GTsetrealtime = "Configurar Reloj en Tiempo Real";
            GTsetprimaryage = "Ajuste de Primaria";
            GTsetsecondaryage = "Establecer Edad Secundaria";
            GTsetpin = "PIN";
        } else if (flag == "jp_") {
            GTsetrealtime = "リアルタイムクロックを設定する";
            GTsetpin = "ピン";
        } else if (flag == "de_") {
            GTsetrealtime = "Echtzeituhrkonfigurator einstellen";
            GTsetpin = "Polig";
        } else if (flag == "fr_" || flag == "cf") {
            GTsetrealtime = "Configurer l’horloge en Temps Réel";
            GTsetprimaryage = "Réglez d'âge Scolaire";
            GTsetsecondaryage = "Réglez Age Secondaire";
            GTsetpin = "Broche";
        } else if (flag == "ko_") {
            GTsetrealtime = "실시간 클록 설정";
            GTsetpin = "핀";
        } else if (flag == "zh_") {
            GTsetrealtime = "设置实时时钟";
        } else if (flag == "pt_") {
            GTsetrealtime = "Ajustar Relógio em Tempo Real";
            GTsetprimaryage = "Ajuste de Escola Primária";
            GTsetsecondaryage = "Definir Secundário Idade";
            GTsetpin = "PIN";
        }
    }
    //var barcodeString = "%01";
    var barcodeString = "";
    var barcodeString2 = "";
    var barcodeString3 = "";
    var pinprint = 0;
    var barcodeStringNoPackets = "";
    var displayText = "<span>";

    // ALERT IF NON CHECKED
    // -----------------------------------
    if (!jQuery('#rtc').prop('checked') && !jQuery('#primAge').prop('checked') && !jQuery('#secAge').prop('checked') && !jQuery('#pin').prop('checked') && !jQuery('#unlockcheck').prop('checked')) {
        jQuery('#avImg').attr("src", "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMwAAADMCAMAAAAI/LzAAAAAA1BMVEX///+nxBvIAAAAP0lEQVR4nO3BgQAAAADDoPlTX+AIVQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADAN6NcAAHIPI2QAAAAAElFTkSuQmCC");
        jQuery('#displayText').html("");
        alert("Please select one or more options below.");
        return 0;
    }

    // CONDITIONAL LOGIC PER CHECKBOX
    // -----------------------------------
    if (jQuery('#primAge').prop('checked')) {
        var age = jQuery('#primAgeInput').val();
        var age2 = jQuery('#secAgeInput').val();

        if (age.length == 0 || isNaN( jQuery('#secAgeInput').val() )) {
            alert("Please enter a number for field 'Over Age'.");
            return 0;
        }

        if( age < age2 || isNaN(age2) ){
        	  alert("'Under Age' must be less than 'Over Age'.");
            return 0;
        }

        if (age.length == 1) {
            age = "0" + age;
        }

        barcodeString = barcodeString + "%01X%1d%02C(2E5)%23" + age + "%04";
        barcodeStringNoPackets = "C(2E5)%23" + age;
        displayText = displayText + GTsetprimaryage + ": " + age2 + "<br />";
    }

    if (jQuery('#secAge').prop('checked')) {
        var age = jQuery('#primAgeInput').val();
        var age2 = jQuery('#secAgeInput').val();
        if (age2.length == 0 || isNaN( jQuery('#secAgeInput').val() )) {
            alert("Please enter a number for field 'Under Age'.");
            return 0;
        }

        if( age < age2 || isNaN(age) ){
        	  alert("'Over Age' must be more than 'Under Age'.");
            return 0;
        }

        if (age2.length == 1) {
            age2 = "0" + age2;
        }
        barcodeString = barcodeString + "%01X%1d%02C(2E6)%23" + age2 + "%04";
        barcodeStringNoPackets = barcodeStringNoPackets + "C(2E6)%23" + age2;
        displayText = displayText + GTsetsecondaryage + ": " + age + "<br />";
    }

    if (jQuery('#rtc').prop('checked')) {
        var x = new Date();
        var currDate = x.getFullYear() + "-" + ("0" + (x.getMonth() + 1)).slice(-2) + "-" + ("0" + x.getDate()).slice(-2) + "%20" + ("0" + x.getHours()).slice(-2) + ":" + ("0" + x.getMinutes()).slice(-2) + ":" + ("0" + x.getSeconds()).slice(-2);
        var currDate2 = x.getFullYear() + "-" + ("0" + (x.getMonth() + 1)).slice(-2) + "-" + ("0" + x.getDate()).slice(-2) + " " + ("0" + x.getHours()).slice(-2) + ":" + ("0" + x.getMinutes()).slice(-2) + ":" + ("0" + x.getSeconds()).slice(-2);
        jQuery('#date').val(currDate2);
        barcodeString = barcodeString + "%01X%1d%02@" + currDate + "%04";
        barcodeStringNoPackets = barcodeStringNoPackets + "@" + currDate;
        displayText = displayText + GTsetrealtime + ": " + currDate2 + "<br />";
    }

    //barcodeString = barcodeString + "X%1d%02W%04";
    //Making some changes to support pin and lock functionality
    if (jQuery('#pin').prop('checked') || jQuery('#unlockcheck').prop('checked')) {

    		// PIN NUMBER VALIDATION
    		// -------------------------------------

        if (jQuery('#pinForm').val().length < 4 || jQuery('#pinForm').val().length > 8 || isNaN( jQuery('#pinForm').val() )) {
            alert("Input valid 4-8 digit PIN.");
            return 0;
        }

        displayText = displayText + GTsetpin + ": " + jQuery('#pinForm').val() + "<br />";
        
        if (jQuery('#pin').prop('checked')) {
            jQuery('#pincodesdiv').css('display', 'block');
            pinprint = 1;
        }

        // Variable to hold request
        var request;

        // Bind to the submit event of our form


        // Abort any pending request
        if (request) {
            //request.abort();
        }

        var pin = "HjQueryL(" + jQuery('#pinForm').val() + ")";

        // Serialize the data in the form
        var serializedData = {
            data: pin + barcodeStringNoPackets,
            pin: jQuery('#pinForm').val()
        };

        // Fire off the request to /form.php
        request = jQuery.ajax({
            url: qrroute+"cron/encrypt",
            type: "post",
            data: serializedData,
            async: false
        });

        // Callback handler that will be called on success
        request.done(function(response, textStatus, jqXHR) {
            // Log a message to the console
            var data = jQuery.parseJSON(response);
            //alert(response);
            responses = response.split('|');
            jQuery('#converted').val(data[0]);
            jQuery('#converted2').val(responses[1]);
            jQuery('#converted3').val(responses[2]);
        });

        // Callback handler that will be called on failure
        request.fail(function(jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });

        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function() {
            // Reenable the inputs

        });

        // Prevent default posting of form
        barcodeString = "%01X%1d%02" + jQuery('#converted').val() + "%04" + barcodeString;
        barcodeString2 = "%01X%1d%02" + jQuery('#converted2').val() + "%04";
        barcodeString3 = "%01X%1d%02" + jQuery('#converted3').val() + "%04";

        // Make a conditional to check whether we are in a development environment
        // -------------------------------------------------------------------------
        
        // if( wphome_url != 'https://www.codecorp.com/'){
	       //  barcodeString = "1234";
	       //  barcodeString2 = "2345";
	       //  barcodeString3 = "2345";
        // }

        if (jQuery('#unlockcheck').prop('checked')) {
            barcodeString = "%01X%1d%02" + jQuery('#converted3').val() + "%04";
            jQuery('#pincodesdiv').css('display', 'none');
            displayText = "Unlock Reader and Clear Pin";
            pinprint = 2;
        }
        
    } else {
        jQuery('#pincodesdiv').css('display', 'none');
    }

    //End making some changes to support pin and lock functionality
    if (!option) {

        //make buttons live
        jQuery('#printAVLink').hide();
        jQuery('#saveAVLink').hide();
        jQuery('#printAVLink2').show();
        jQuery('#saveAVLink2').show();
        jQuery('#printAVLink3').hide();
        jQuery('#printAVLink4').show();
        var x = new Date();
        jQuery('#avImg').attr("src", "images/ajax-loader-large.gif");
        var currDate = x.getFullYear() + "-" + ("0" + (x.getMonth() + 1)).slice(-2) + "-" + ("0" + x.getDate()).slice(-2) + "_" + ("0" + x.getHours()).slice(-2) + "-" + ("0" + x.getMinutes()).slice(-2) + "-" + ("0" + x.getSeconds()).slice(-2);

        var primAgeInput = jQuery('#primAgeInput').val() > 0 ? jQuery('#primAgeInput').val() + "-" : '';
        var secAgeInput = jQuery('#secAgeInput').val() > 0 ? jQuery('#secAgeInput').val() + "-" : '';
        var pinForm = jQuery('#pinForm').val() > 0 ? "-"+jQuery('#pinForm').val() : '';

        var filename = primAgeInput + secAgeInput + currDate + pinForm;
        
        jQuery('#avImgHolder').attr("src", qrroute + "print/config?data_text=" + barcodeString + "&matrix_size=20&matrix_type=TEXT&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks&border-size=20&filename=" + filename + "&converted=1");
        jQuery('#avImgHolder').load(
            function() {
                jQuery('#avImg').attr("src", qrpath + "include/dm_code/download/H-" + filename + ".jpg");
            }
        );

        jQuery('#setPinImgHolder').attr("src", qrroute + "print/config?data_text=" + barcodeString2 + "&matrix_size=20&matrix_type=TEXT&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks&border-size=20&filename=" + filename + "&converted=2");
        jQuery('#setPinImgHolder').load(
            function() {
                jQuery('#setPinImg').attr("href", "#setPinImgHolder");
            }
        );
        
        jQuery('#resetPinImgHolder').attr("src", qrroute + "print/config?data_text=" + barcodeString3 + "&matrix_size=20&matrix_type=TEXT&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks&border-size=20&filename=" + filename + "&converted=3");
        jQuery('#resetPinImgHolder').load(
            function() {
                jQuery('#resetPinImg').attr("href", "#resetPinImgHolder");
            }
        );

        console.log('converted should be set');

        jQuery('input[name="converted"]').val('1');
        jQuery('#displayText').html(displayText + "</span>");
        jQuery('#avImgDiv').css('display', 'block');
        jQuery('#AVbarcodeDiv').css('height', '129%');
        jQuery("#ageVerification").animate({
            scrollTop: jQuery('#ageVerification').prop("scrollHeight")
        }, 700);

    } else {

        //This is for print/save buttons clicked before submitting to verifiy content is ok to submit to pdf generator
        
        jQuery('#age1').val(age);
        jQuery('#age2').val(age2);
        jQuery('#pinprint').val(pinprint);
        
        if (option == 'save') {
            document.getElementById('barcodes-print-save').value = option;
        } else {
            document.getElementById('barcodes-print-save').value = "";
        }

        jQuery('#AVlink').val(barcodeString);

        document.forms["print-save"].submit();
    }
}

function showAV(option) {

    if (option) {
        document.getElementById('rtcForm').value = 1;
        jQuery('#primAge').prop('checked', false);
        jQuery('#secAge').prop('checked', false);
        jQuery('#primAgeInput').prop("disabled", true);
        jQuery('#secAgeInput').prop("disabled", true);
        jQuery('#rtc').prop('checked', true);
        jQuery('#avTitle').hide();
        jQuery('#avTitleBackup').show();
        jQuery('#primAge').hide();
        jQuery('#secAge').hide();
        jQuery('#primAgeInput').hide();
        jQuery('#secAgeInput').hide();
        jQuery('#primText').hide();
        jQuery('#secText').hide();
        jQuery('#rtc').hide();
        jQuery('#under-age-row').hide();
        jQuery('#middle-age-row').hide();
        jQuery('#over-age-row').hide();
        jQuery('#set-lock-pin-row').hide();
        jQuery('#unlock-reader-row').hide();
        jQuery('.select-col-checkbox').hide();
        jQuery('.value-column').hide();
        //jQuery('#rtcText').hide();
    } else {
        document.getElementById('rtcForm').value = "";
        jQuery('#avTitle').show();
        jQuery('#avTitleBackup').hide();
        jQuery('#primAge').show();
        jQuery('#secAge').show();
        jQuery('#primAgeInput').show();
        jQuery('#secAgeInput').show();
        jQuery('#primText').show();
        jQuery('#secText').show();
        jQuery('#rtc').show();
        jQuery('#rtc').prop('checked', false);
        jQuery('#under-age-row').show();
        jQuery('#middle-age-row').show();
        jQuery('#over-age-row').show();
        jQuery('#set-lock-pin-row').show();
        jQuery('#unlock-reader-row').show();
        jQuery('.select-col-checkbox').show();
        jQuery('.value-column').show();
        //jQuery('#rtcText').show();
    }
    jQuery('#selectLinkTD').hide();
    jQuery('#printLinkTD').hide();
    jQuery('#saveLinkTD').hide();
    jQuery('#showLinkTD').hide();
    jQuery('#printAVLink').show();
    jQuery('#saveAVLink').show();
    jQuery('#printAVLink4').show();
    jQuery("#tree_div").hide("blind", 0, 500);
    jQuery("#search2").hide();
    jQuery('#barcodes1').hide("blind", 0, 500);
    jQuery('#barcodes_spash_initial').hide("blind", 0, 500);
    jQuery('#ageVerification').show("blind", 0, 500);
    jQuery('#CR5000AV_toc').prop("checked", false);
    check();
}

//********************************************
//END AV SECTION

jQuery(document).ready(function() {
    jQuery('#selectLink').click(function() {
        var select = document.getElementById('select');
        var selectImage = document.getElementById('selectImage');
        var print = document.getElementById('print');
        var print_grey = document.getElementById('print_grey');
        var save = document.getElementById('save');
        var save_grey = document.getElementById('save_grey');
        var show_all_link = document.getElementById('show_all_link');
        var barcodesall = document.getElementById('barcodes-all');
        barcodesall.value = "!";
        //var email = document.getElementById('email');

        if (select.innerHTML == "&nbsp;<br>" + jQuery("#GTselectall").val()) {
            //alert(select.innerHTML);
            jQuery('input:checkbox').each(function(i) {
                //alert(this.value);
                if (this.value != "tocTitle" && this.value.substr(0, 2) != "SE") {

                    document.getElementById("AL" + this.value).checked = true;
                }
                jQuery("#show_all").show();
                show_all_link.innerHTML = '';
                selectImage.setAttribute('src', 'image/nocheckoff.png?v=1');
                select.innerHTML = "&nbsp;<br />" + jQuery("#GTdeselectall").val();
                //set print/save/email to all
                print_grey.innerHTML = '';
                save_grey.innerHTML = '';
                print.innerHTML = "<img src='image/printsoff.png' /><br />&nbsp;<br />" + jQuery("#GTprintall").val();
                save.innerHTML = "<img src='image/saveoff.png' /><br />&nbsp;<br />" + jQuery("#GTsaveall").val();
                //email.innerHTML = "&nbsp;<br />Email All";
            });
        } else {

            jQuery('input:checkbox').each(function(i) {
                //alert(this.value);
                if (this.value != "tocTitle" && this.value.substr(0, 2) != "SE") {

                    document.getElementById("AL" + this.value).checked = false;
                    document.getElementById("SE" + this.value).style.display = "none";
                }
                jQuery("#show_all").show();
                show_all_link.innerHTML = '';
                selectImage.setAttribute('src', 'image/checkoff.png?v=1');
                select.innerHTML = "&nbsp;<br />" + jQuery("#GTselectall").val();
                //set print/save/email to all
                print.innerHTML = '';
                save.innerHTML = '';

                print_grey.innerHTML = '<img src="image/printgrey.png"><br />&nbsp;<br />' + jQuery("#GTprintall").val() + '<br />';
                save_grey.innerHTML = '<img src="image/savegrey.png"><br />&nbsp;<br />' + jQuery("#GTsaveall").val() + '<br />';
                //email.innerHTML = "&nbsp;<br />Email All";
            });
        }
    });
    jQuery("div.manuLinksContainer").mouseenter(function() {
        var src = jQuery("img:first", this).attr("src").replace("off", "on");
        jQuery("img:first", this).attr("src", src);
        jQuery("div:first", this).css("display", "block");
    });
    jQuery("div.manuLinksContainer").mouseleave(function() {
        var src = jQuery("img:first", this).attr("src").replace("on", "off");
        jQuery("img:first", this).attr("src", src);
        jQuery("div:first", this).css("display", "none");
    });
    jQuery("a.hover").mouseenter(function() {
        //alert('test');
        var src = jQuery("img:first", this).attr("src").replace("off", "on");
        jQuery("img:first", this).attr("src", src);
    });
    jQuery("a.hover").mouseleave(function() {
        var src = jQuery("img:first", this).attr("src").replace("on", "off");
        jQuery("img:first", this).attr("src", src);
    });
    jQuery("div.barcode").mouseenter(function() {
        if (jQuery(this).attr('id') != "AVbarcodeDiv") {
            jQuery("#" + jQuery(this).attr('id') + "_description").stop(true, true).show(0, 0, 1);
            jQuery('#description_box').stop(true, true).animate({
                backgroundColor: '#D0D0D0',
                color: '#8D1A21'
            }, 800);
            jQuery('#description_box').stop(true, true).animate({
                backgroundColor: '#f6f6f6',
                color: '#808080'
            }, 800);
        }

    });
    jQuery("div.barcode").mouseleave(function() {
        jQuery("#" + jQuery(this).attr('id') + "_description").stop(true, true).hide(0, 0, 500);
        jQuery("#description_default_text").stop(true, true).delay(2001).show(0, 0, 500);
    });

    //search enter key press submit
    jQuery("#search_field").keyup(function(event) {
        if (event.keyCode == 13) {
            jQuery("#search_button").click();
        }
    });
});

function printsave(f, g) {
    document.getElementById('barcodes-print-save').value = f;
    if (g) {
        document.getElementById('sub').value = g;
    } else {
        document.getElementById('sub').value = "!";
        //alert("You have selected all barcodes. PDF generation may take a few moments.");
    }
    if (document.getElementById('barcodes-all').value == "!") {
        alert("You have selected all barcodes. PDF generation may take a few moments.");
    }
    document.forms["print-save"].submit();
}