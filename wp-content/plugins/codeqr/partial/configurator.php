
<div id="AVbarcodeDiv" style="width: 500px; margin-top: 25px; margin-left: 88.5px; text-align: left;">

    <table style="width: 450px; border: 1px double green; border-collapse: collapse; margin-left: 25px; margin-top: 25px;" width="" align="">

      <tbody>

        <tr>
          <td class="select-col-checkbox" style="text-align: left; border: 1px double rgb(166, 166, 166); padding: 4px 8px 4px 8px; width: 12%;"><span style="font-weight: bold;">Select</span></td>
          <td style="text-align: left; border: 1px double rgb(166, 166, 166); padding: 4px 8px 4px 8px; width: 27%;"><span style="font-weight: bold;">Configuration Options</span></td>
          <td class="value-column" style="text-align: left; border: 1px double rgb(166, 166, 166); padding: 4px 8px 4px 8px; width: 12%;"><span style="font-weight: bold;">Value</span></td>
          <td style="text-align: left; border: 1px double rgb(166, 166, 166); padding: 4px 8px 4px 8px; width: 34%;"><span style="font-weight: bold;">Reader Behavior</span></td>
        </tr>

        <tr id="set-lock-pin-row" style=" height: 37px;">
          <td class="select-col-checkbox" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
            <input id="pin" value="tocTitle" type="checkbox" onchange="check('pin');" /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Set Lock PIN<br />(4-8 numeric characters)
            </td>
          <td class="value-column" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;" rowspan="2">
            <input id="pinForm" style="font-size: 11px; width: 100%; height: 20px;" maxlength="8" disabled /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Lock reader settings until unlock PIN provided<br />
          <input id="converted" value="" type="hidden" /><input id="converted2" value="" type="hidden" /><input id="converted3" value="" type="hidden" />
            </td>
        </tr>
        
        <tr id="unlock-reader-row" style=" height: 37px;">
          <td class="select-col-checkbox" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
            <input id="unlockcheck" value="tocTitle" type="checkbox" onchange="check('unlockcheck');" /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Unlock Reader and Clear PIN<br />
            </td>
          
          <td class="value-column" style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Allows reader settings to be changed and clears PIN<br />
          <input id="converted" value="" type="hidden" /><input id="converted2" value="" type="hidden" /><input id="converted3" value="" type="hidden" />
            </td>
        </tr>
        
        <tr id="under-age-row" style=" height: 37px;">
          <td class="select-col-checkbox" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
            <input id="secAge" value="tocTitle" type="checkbox" onchange="check('secAge');" /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px; vertical-align: inherit;">Set Under Age<br />
            </td>
          <td class="value-column" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
            <input id="secAgeInput" style="font-size: 11px; width: 100%; height: 20px;" maxlength="2" disabled="disabled" /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Red LED<br />
            </td>
        </tr>
                
        <tr id="over-age-row" style=" height: 37px;">
          <td class="select-col-checkbox" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
            <input id="primAge" value="tocTitle" type="checkbox" onchange="check('primAge');" /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Set Over Age<br />
            </td>
          <td class="value-column" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
            <input id="primAgeInput" style="font-size: 11px; width: 100%; height: 20px;" maxlength="2" disabled="disabled" /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Green LED<br />
            </td>
        </tr>
        
                
        <tr id="over-age-row" style=" height: 37px;">
          <td class="select-col-checkbox" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
            <input id="rtc" value="tocTitle" type="checkbox" onchange="check('rtc');" /><br />
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Set Real Time Clock (RTC)<br />
            </td>
          <td class="value-column" style="text-align: left; border: 1px double #DDDDDD; padding: 4px 8px 4px 8px;">
             - 
            </td>
          <td style="text-align: left; border: 1px double rgb(221, 221, 221); padding: 4px 8px 4px 8px;">Sets RTC to current date and time<br />
            </td>
        </tr>
      </tbody>
      
    </table>

    <input type="button" onclick="generate();" value="Generate" id="avGenButton" style="margin-left: 25px; margin-top: 10px;" /><br />&nbsp;
    
    <div id="displayText" style="margin-left: 25px;"></div>
    
    <div style="width: 100%; text-align: center; margin-top: 20px; margin-bottom: 20px; display: none;" id="avImgDiv">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMwAAADMCAMAAAAI/LzAAAAAA1BMVEX///+nxBvIAAAAP0lEQVR4nO3BgQAAAADDoPlTX+AIVQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADAN6NcAAHIPI2QAAAAAElFTkSuQmCC" id="avImg" style="height: 200px; width: 200px; position: static;" />
        <img id="avImgHolder" style="display: none;" />
        <br />
    </div>

</div>
                    
<div id="pincodesdiv" style="display: none; height: 300px; width: 500px; margin-left: 88.5px;">

    <div style="width: 100%; text-align: left; margin-bottom: 5px;">Additional PIN Settings:<br style="clear: both;" /></div>
    
    <div class="barcode" style="margin-left: 0px;"><br><br>
        <span>Set Pin </span><br>
        <div id="scanlink">
            <a href="" rel="fancybox-button1" class="openLink fancybox-button" title="Set Pin " id="setPinImg">[View]<img alt="" style="display: none;"></a>
            <img id="setPinImgHolder" style="display: none;" />
        </div>
    </div>
    
    <div class="barcode" style="margin-left: 22px;"><br><br>
        <span>Unlock Reader and Clear PIN </span><br>
        <div id="scanlink">
            <a href="" rel="fancybox-button1" class="openLink fancybox-button" title="Unlock Reader and Reset Pin " id="resetPinImg">[View]<img alt="" style="display: none;"></a>
            <img id="resetPinImgHolder" style="display: none;" />
        </div>
    </div>
    
    <div class="barcode" style="margin-left: 22px;"><br><br>
        <span>Force Unlock Reader and Clear PIN (Manually Restart Reader Within 30 Seconds of Scanning) </span><br>
        <div id="scanlink">
            <a href="<?php echo plugin_dir_url( __DIR__ ) . 'include'; ?>/dm_code/download/reset.jpg" rel="fancybox-button1" class="openLink fancybox-button" title="Force Unlock Reader and Reset Pin (Manually Restart Reader Within 30 Seconds of Scanning) ">[View]<img alt="" style="display: none;"></a>
        </div>
    </div>

</div>

<script src="<?php echo plugin_dir_url( __DIR__ ). '/assets/js/configguide2.js'?>"></script>

<script>
  jQuery(document).ready(function($) {

		$j = 0;

    $(".fancybox-button").fancybox({
        minWidth: 60,
        minHeight: 60,
        padding: 40,
        arrows: false,
        prevEffect      : 'none',
        nextEffect      : 'none',
        closeBtn        : false,
        helpers     : {
          title   : { type : 'outside' },
          buttons : {},
          overlay : {
            css : {
              'background' : 'rgba(0, 0, 0, 0.85)'
            }
          }
        },
        beforeShow: function () {

          /* Disable right click */
          $.fancybox.wrap.bind("contextmenu", function (e) {
            //return false; 
          });
            
          if($j == 0) {
            $j++;
            this.width = "95px";
            this.height = "95px"; }
          else {
            this.width = "175px";
            this.height = "175px"; 
            $j = 0; 
          }
       	},
				beforeClose: function () {
					$j = 0;
				}
            
    });
  });
</script>