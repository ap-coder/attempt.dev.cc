<?php /* Partial: Notifications Settings */ ?>
<div class="cell">
	<h4>Notifications</h4>
	<p>Choose what to be notified about.</p>
	<hr>
	<table class="notifications">
			<tr>
				<td>Messages</td>
				<td>
					<input id="notify['messages']" type="checkbox" class="tgl tgl-light"/> 
					<label for="notify['messages']" class="tgl-btn"></label>
				</td>
			</tr> 
			<tr>
				<td>Radar Hits</td>
				<td>
					<input id="notify['radar']" type="checkbox" class="tgl tgl-light"/> 
					<label for="notify['radar']" class="tgl-btn"></label>
				</td>
			</tr>
			<tr>
				<td>Account Changes</td>
				<td>
					<input id="notify['account']" type="checkbox" class="tgl tgl-light"/> 
					<label for="notify['account']" class="tgl-btn"></label>
				</td>
			</tr>
			<tr>
				<td>Profile Changes</td>
				<td>
					<input id="notify['profile']" type="checkbox" class="tgl tgl-light"/> 
					<label for="notify['profile']" class="tgl-btn"></label>
				</td>
			</tr>
	</table>
</div>