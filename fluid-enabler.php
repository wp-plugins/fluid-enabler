<?php
/*
Plugin Name: Fluid Enabler
Plugin URI: http://wordpress.org/extend/plugins/fluid-enabler/
Description: Fluid Enabler allow you to use great features from <a href="http://fluidapp.com/">Fluid</a>'s <i>Site Specific browsers</i> (SSb), for MacOS X. It shows how many comments awaiting moderation there are in the Dock using a Mail-like badge. It also uses <a href="http://growl.info/">Growl</a> Notifications to tell you about Wordpress and Plugins updates and comments awaiting moderation.
Author: Guillaume Mahieux
Version: 0.3
Author URI: http://guillomftp.free.fr/
*/

/*  Copyright 2008  Guillaume MAHIEUX  (email : gmahieux@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Cette fonction ajoute au pannel d'administration le code Javascript
   servant à afficher les notifications, s'il y a lieu. */

// Initialize this plugin. Called by 'init' hook.
add_action( 'init', 'fluid_enabler_init' );
function fluid_enabler_init()
{
	load_plugin_textdomain( 'fluid-enabler', 'wp-content/plugins/fluid-enabler/' );
}

// Main plugin
function fluid_enabler_main() {
?>
<script type="text/javascript">
<? /* Quelques lignes de PHP pour faciliter les choses. */
$cur = get_option( 'update_core' );
if (isset($cur->response) && $cur->response == 'upgrade' && current_user_can('manage_options'))
		$wordpressUpdateAvailable = "true";
	else
		$wordpressUpdateAvailable = "false";
		
$comment_count = get_comment_count();
$commentsAwaitingModeration = $comment_count['awaiting_moderation'];
$newSpamComments = $comment_count['spam'];

$update_plugins = get_option( 'update_plugins' );
$pluginsUpdates = count($update_plugins->response);
?>

<? /* Définition de quelques variables : */ ?>
var wordpressUpdateAvailable = <? echo $wordpressUpdateAvailable; ?>; // (bool) indique si une MAJ de WP est disponible.
var commentsAwaitingModeration = <? echo $commentsAwaitingModeration; ?>; // nombre de commentaires à modérer.
var newSpamComments = <? echo $newSpamComments; ?>; // nombre de commentaires marqués comme SPAM.
var pluginsUpdates = <? echo $pluginsUpdates; ?>; // nombre de mises à jour de plugins disponibles.

<? /* Mise à jour de Wordpress */ ?>
if(wordpressUpdateAvailable == true)
{
	window.fluid.showGrowlNotification({
		title: "<? printf(__('WordPress %s available', 'fluid-enabler'), $cur->current); ?>",
		description: "<? printf(__('A new version of WordPress is now available. Please upgrade to WordPress %s.', 'fluid-enabler'), $cur->current); ?>",
		priority: 1,
		sticky: false
	});
}

<? /* Commentaires en attente de modération */ ?>
if(commentsAwaitingModeration > 0)
{
	var growlTitle;
	var growlDescription;
	
	if (commentsAwaitingModeration == 1) <? /* 1 Commentaire */ ?>
	{
		growlTitle = "<? _e('New comment', 'fluid-enabler'); ?>";
		growlDescription = "<? _e('There is 1 comment awaiting moderation.', 'fluid-enabler'); ?>";
	}
	else <? /* Plusieurs commentaire */ ?>
	{
		growlTitle = "<? _e('New comments', 'fluid-enabler'); ?>";
		growlDescription = "<? printf(__('There are %d comments awaiting moderation.', 'fluid-enabler'), $commentsAwaitingModeration); ?>";
		// "Il y a " + commentsAwaitingModeration + " commentaires en attente de modération."
	}
	
	<? /* Affichage des informations */ ?>
	window.fluid.dockBadge = commentsAwaitingModeration;
	window.fluid.playSoundNamed("Hero");
	window.fluid.showGrowlNotification({
		title: growlTitle,
		description: growlDescription,
		priority: 1,
		sticky: false
	});
}

<? /* Mises à jour de plugins */ ?>
if(pluginsUpdates > 0)
{
	var growlTitle;
	var growlDescription;
	
	if (pluginsUpdates == 1) <? /* 1 Mise à jour */ ?>
	{
		growlTitle = "<? _e('Plugin update available', 'fluid-enabler'); ?>";
		growlDescription = "<? _e('There is an update available for one of your plugins. Please update.', 'fluid-enabler'); ?>";
	}
	else <? /* Plusieurs mises à jour */ ?>
	{
		growlTitle = "<? _e('Plugin updates available', 'fluid-enabler'); ?>";
		growlDescription = "<? printf(__('There are %d updates available for your plugins. Please update.', 'fluid-enabler'), $pluginsUpdates); ?>";
	}
	
	<? /* Affichage des informations */ ?>
	window.fluid.showGrowlNotification({
		title: growlTitle,
		description: growlDescription,
		priority: 1,
		sticky: false
	});
}
</script>
<?php
}

/* Ici, on indique à WordPress qu'on ajoute notre fonction au header de l'administration. */
add_action('admin_head', 'fluid_enabler_main');

?>