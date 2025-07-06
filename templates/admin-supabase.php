<?php
script('mail', 'admin-supabase');
style('mail', 'admin-supabase');
?>

<div id="supabase-settings" class="section">
	<h2>Supabase</h2>

	<label for="supabase-url">Supabase URL</label>
	<input type="text" id="supabase-url" name="supabase-url" value="<?php p($_['supabaseUrl']); ?>">

	<label for="supabase-service-token">Supabase Service Token</label>
	<input type="text" id="supabase-service-token" name="supabase-service-token" value="<?php p($_['supabaseServiceToken']); ?>">

	<button id="save-supabase-settings">Save</button>
</div>
