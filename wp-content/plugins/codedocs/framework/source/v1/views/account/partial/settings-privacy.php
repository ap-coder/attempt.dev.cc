<?php /* Partial: Settings Privacy */?>
<div class="cell">
	<h4>Privacy</h4>
	<p>Toggle settings as public or private.</p>
	<hr>
	<table>
		<tr>
			<td>Online Status <span class="genericon genericon-help">
				<div class="tooltip">When enabled, other users can see when you are logged in. <a href="#">Learn More</a></div>
			</span></td>
			<td>
				<input id="privacy['ostatus']" type="checkbox" class="tgl tgl-light"/> 
				<label for="privacy['ostatus']" class="tgl-btn"></label>
			</td>
		</tr>
		<tr>
			<td>Allow Radar Hits <span class="genericon genericon-help">
				<div class="tooltip">When enabled, radar Hits by default are limited to once per week, per person. <a href="#">Learn More</a></div>
			</span></td>
			<td>
				<input id="privacy['ostatus']" type="checkbox" class="tgl tgl-light"/> 
				<label for="privacy['ostatus']" class="tgl-btn"></label>
			</td>
		</tr>
	</table>

	<p><br />Enter the email of those you wish to block from contacting you or any linked accounts.</p>
	<hr>
	<div class="row">
		<div class="col-sm-12 ">
			<textarea name="blocked_users" id="blocked_users" s="30" rows="10"></textarea>
		</div>
	</div>
</div>