<?php
/**
 * RoundedBlue
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/** */
require_once('includes/SkinTemplate.php');

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class SkinRoundedBlue extends SkinTemplate {
	/** Using RoundedBlue. */
	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'RoundedBlue';
		$this->stylename = 'RoundedBlue';
		$this->template  = 'RoundedBlueTemplate';
	}
}

/**
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class RoundedBlueTemplate extends QuickTemplate {
	/**
	 * Template filter callback for RoundedBlue skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
	<head>
		<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
		<?php $this->html('headlinks') ?>
		<title><?php $this->text('pagetitle') ?></title>
		<link rel="stylesheet" media="screen,projection" href="<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/main.css" />
		<link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('stylepath') ?>/common/commonPrint.css" />
		<link rel="stylesheet" type="text/css" media="handheld" href="<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/handheld.css" />
		<meta http-equiv="imagetoolbar" content="no" /><!-- [endif] -->
		<script type="<?php $this->text('jsmimetype') ?>">var skin = '<?php $this->text('skinname')?>';var stylepath = '<?php $this->text('stylepath')?>';</script>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?1"><!-- wikibits js --></script>
<?php	if($this->data['jsvarurl'  ]) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl'  ) ?>"><!-- site js --></script>
<?php	} ?>
<?php	if($this->data['pagecss'   ]) { ?>
		<style type="text/css"><?php $this->html('pagecss'   ) ?></style>
<?php	}
		if($this->data['usercss'   ]) { ?>
		<style type="text/css"><?php $this->html('usercss'   ) ?></style>
<?php	}
		if($this->data['userjs'    ]) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
<?php	}
		if($this->data['userjsprev']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
<?php	}
		if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
		<!-- Head Scripts -->
		<?php $this->html('headscripts') ?>
	</head>
<body <?php if($this->data['body_ondblclick']) { ?>ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload'    ]) { ?>onload="<?php     $this->text('body_onload')     ?>"<?php } ?>
 class="<?php $this->text('nsclass') ?> <?php $this->text('dir') ?>">
	<div id="globalWrapper">
		<div id="masthead">
			<h1><a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>" style="text-transform: none;">Vitenfabrikken - wiki</a></h1> 
			<h2><?php $this->html('subtitle') ?>Wiki for oppl&aelig;ring og rutiner</h2>
		</div>
		<div id="column-content">
			<div id="content">
				<a name="top" id="top"></a>
				<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
				<h3 id="siteSub"><?php $this->msg('tagline') ?></h3>
				<div class="post">
					<div class="top_left_corner"></div>
        				<div class="top_right_corner"></div>
					<div class="postop">
						<div class="pheadfill">
							<h1 class="firstHeading"><?php $this->data['displaytitle']!=""?$this->text('title'):$this->html('title') ?></h1>
						</div><!--//pheadfill -->
					</div><!--//postop -->
					<div id="bodyContent">
						<div id="bodyContentInnerWrapper">
							<div class="spacer">&nbsp;</div>
							<div id="contentSub"><?php $this->html('subtitle') ?></div>
							<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php     $this->html('undelete') ?></div><?php } ?>
							<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
							<?php if($this->data['showjumplinks']) { ?><div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div><?php } ?>
							<!-- start content -->
							<?php $this->html('bodytext') ?>
							<?php if($this->data['catlinks']) { ?><div id="catlinks"><?php       $this->html('catlinks') ?></div><?php } ?>
							<!-- end content -->
							<div class="visualClear"></div> 
						</div><!--//bodyContentInnerWrapper -->
					</div><!--//bodyContent -->
					<div class="bottom_left_corner"></div> 
					<div class="bottom_right_corner"></div>
				</div><!--//post --> 
			</div><!--//content -->
		</div><!--//column-content -->
		
		
		<script type="<?php $this->text('jsmimetype') ?>"> if (window.isMSIE55) fixalpha(); </script>
		<div id="column-one">
			<div id="p-cactions" class="portlet">
				<h5><?php $this->msg('views') ?></h5>
				<div class="pBody">
					<ul>
<?php					foreach($this->data['content_actions'] as $key => $tab) {
					echo '
						<li id="ca-' . Sanitizer::escapeId($key).'"';
					if( $tab['class'] ) {
						echo ' class="'.htmlspecialchars($tab['class']).'"';
					}
					echo'><a href="'.htmlspecialchars($tab['href']).'"';
					# We don't want to give the watch tab an accesskey if the
					# page is being edited, because that conflicts with the
					# accesskey on the watch checkbox.  We also don't want to
					# give the edit tab an accesskey, because that's fairly su-
					# perfluous and conflicts with an accesskey (Ctrl-E) often
					# used for editing in Safari.
				 	if( in_array( $action, array( 'edit', 'submit' ) )
				 	&& in_array( $key, array( 'edit', 'watch', 'unwatch' ))) {
				 		echo $skin->tooltip( "ca-$key" );
				 	} else {
				 		//echo $skin->tooltipAndAccesskey( "ca-$key" );
				 	}
				 	echo '>'.htmlspecialchars($tab['text']).'</a></li>';
				} ?>
					</ul>
				</div>
			</div>
			<div class="portlet" id="p-personal">
				<h5><?php $this->msg('personaltools') ?></h5>
				<div class="pBody">
					<ul>
<?php 					foreach($this->data['personal_urls'] as $key => $item) { ?>
						<li id="pt-<?php echo Sanitizer::escapeId($key) ?>"<?php
						if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
						echo htmlspecialchars($item['href']) ?>"<?php //echo $skin->tooltipAndAccesskey('pt-'.$key) ?><?php
						if(!empty($item['class'])) { ?> class="<?php
						echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
						echo htmlspecialchars($item['text']) ?></a></li>
<?php					} ?>
					</ul>
				</div>
			</div>
			<div class="portlet" id="p-logo">
				<a style="background-image: url(<?php $this->text('logopath') ?>);" <?php
				?>href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>"<?php
				//echo $skin->tooltipAndAccesskey('n-mainpage') ?>></a>
			</div>
			<script type="<?php $this->text('jsmimetype') ?>"> if (window.isMSIE55) fixalpha(); </script>
<?php
		$sidebar = $this->data['sidebar'];		
		if ( !isset( $sidebar['SEARCH'] ) ) $sidebar['SEARCH'] = true;
		if ( !isset( $sidebar['TOOLBOX'] ) ) $sidebar['TOOLBOX'] = true;
		if ( !isset( $sidebar['LANGUAGES'] ) ) $sidebar['LANGUAGES'] = true;
		foreach ($sidebar as $boxName => $cont) {
			if ( $boxName == 'SEARCH' ) {
				$this->searchBox();
			} elseif ( $boxName == 'TOOLBOX' ) {
				$this->toolbox();
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} else {
				$this->customBox( $boxName, $cont );
			}
		}
?>
			<div class="visualClear"></div>
			
		</div>
		<div id="footer">
			<div class="fboxhead">
				<div class="fheadfill">&nbsp;</div>
			</div> 
		<div id="footercontent">

<?php
		if($this->data['poweredbyico']) { ?>
			<div id="f-poweredbyico"><?php $this->html('poweredbyico') ?></div>
<?php 	}
		if($this->data['copyrightico']) { ?>
			<div id="f-copyrightico"><?php $this->html('copyrightico') ?></div>
<?php	}

		// Generate additional footer links
?>
			<ul id="f-list">
<?php
		$footerlinks = array(
			'lastmod', 'viewcount', 'numberofwatchingusers', 'credits', 'copyright',
			'privacy', 'about', 'disclaimer', 'tagline',
		);
		foreach( $footerlinks as $aLink ) {
			if( $this->data[$aLink] ) {
?>				<li id="<?php echo$aLink?>"><?php $this->html($aLink) ?></li>
<?php 		}
		}
?>
			</ul> 
		</div><!-- //footercontent-->
	</div>
	<script type="text/javascript"> if (window.runOnloadHook) runOnloadHook();</script>
</div>
</body></html>
<?php
	wfRestoreWarnings();
	} // end of execute() method
	
	
	/*************************************************************************************************/
	function searchBox() {
?>
<div class="sideitem">
	<div class="boxhead">
		<div id="p-search" class="portlet">
			<div class="headfill">
				<h5><label for="searchInput"><?php $this->msg('search') ?></label></h5>
			</div><!--//headfill -->
			<div class="boxbody">
				<div id="searchBody" class="pBody">
					<form action="<?php $this->text('searchaction') ?>" id="searchform">
					<div>
						<input id="searchInput" name="search" type="text" <?php
						if($this->haveMsg('accesskey-search')) {
							?>accesskey="<?php $this->msg('accesskey-search') ?>"<?php }
						if( isset( $this->data['search'] ) ) {
							?> value="<?php $this->text('search') ?>"<?php } ?> /><br />
						<input type='button' name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg('go') ?>" alt="go" title="go" />&nbsp;
						<input type='button' name="fulltext" class="searchButton" value="<?php $this->msg('search') ?>" alt="search" title="search" />
					</div>
					</form>
				</div><!--//searchBody -->
			</div><!--//boxbody -->
		</div><!--//p-search -->
	</div><!--//boxhead -->
</div><!--//sideitem -->
<?php
	}

	/*************************************************************************************************/
	function toolbox() {
?>
<div class="sideitem">
<div class="boxhead">
	<div class="portlet" id="p-tb">
	<div class="headfill">
		<h5><?php $this->msg('toolbox') ?></h5>
	</div><!--//headfill -->
	<div class="boxbody">
		<div class="pBody">
			<ul>
<?php
		if($this->data['notspecialpage']) { ?>
				<li id="t-whatlinkshere"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
				?>"><?php $this->msg('whatlinkshere') ?></a></li>
<?php
			if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
				<li id="t-recentchangeslinked"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
				?>"><?php $this->msg('recentchangeslinked') ?></a></li>
<?php 		}
		}
		if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
			<li id="t-trackbacklink"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"><?php $this->msg('trackbacklink') ?></a></li>
<?php 	}
		if($this->data['feeds']) { ?>
			<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><span id="feed-<?php echo htmlspecialchars($key) ?>"><a href="<?php
					echo htmlspecialchars($feed['href']) ?>"><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;</span>
					<?php } ?></li><?php
		}

		foreach( array('contributions', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {

			if($this->data['nav_urls'][$special]) {
				?><li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
				?>"><?php $this->msg($special) ?></a></li>
<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>"><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"><?php $this->msg('permalink') ?></li><?php
		}

		wfRunHooks( 'RoundedBlueTemplateToolboxEnd', array( &$this ) );
?>
			</ul>
		</div><!--//pBody -->
	</div><!--//boxbody -->
</div><!--//p-tb -->
</div><!--//boxhead -->
</div><!--//sideitem -->
<?php
	}


	/*************************************************************************************************/
	function languageBox() {
		if( $this->data['language_urls'] ) { 
?>
	<div id="p-lang" class="portlet">
		<h5><?php $this->msg('otherlanguages') ?></h5>
		<div class="pBody">
			<ul>
<?php		foreach($this->data['language_urls'] as $langlink) { ?>
				<li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
				?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"><?php echo $langlink['text'] ?></a></li>
<?php		} ?>
			</ul>
		</div>
	</div>
<?php
		}
	}

	/*************************************************************************************************/
	function customBox( $bar, $cont ) {
?>
<div class="sideitem">
	<div class="boxhead">
		<div class='portlet' id='p-<?php echo htmlspecialchars($bar) ?>'>
			<div class="headfill">
				<h5><?php $out = wfMsg( $bar ); if (wfEmptyMsg($bar, $out)) echo $bar; else echo $out; ?></h5>
			</div><!--//headfill -->
			<div class="boxbody">
				<div class='pBody' id="customBoxBody">
					<ul>
<?php 						foreach($cont as $key => $val) { ?>
						<li id="<?php echo htmlspecialchars($val['id']) ?>"<?php
					if ( $val['active'] ) { ?> class="active" <?php }
				?>><a href="<?php echo htmlspecialchars($val['href']) ?>"><?php echo htmlspecialchars($val['text']) ?></a></li>
<?php			} ?>
					</ul>
				</div><!--//pBody -->
			</div><!--//boxbody -->
		</div><!--//p-navigation --> 
	</div><!--//boxhead -->
</div><!--//sideitem -->

<?php
	}
	
} // end of class
?>
