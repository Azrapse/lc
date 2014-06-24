<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('LegaleCloud'); ?>
		<?php echo $title_for_layout; ?> 
	</title>

	<style type="text/css">
			div.disabled {
					display: inline;
					float: none;
					clear: none;
					color: #C0C0C0;
			}
	</style>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('cake.generic');
		echo $this->Html->css('extranet');
		echo $this->Html->script('prototype');
		echo $this->Html->script('scriptaculous');
		echo $this->Html->script('jquery-1.11.0');
		echo $this->Html->script('jquery-migrate-1.2.1');
	?>
		<script language="javascript" type="text/javascript">
			jQuery.noConflict();
		</script>
	<?php
        echo $this->Html->script('jquery.printElement.min');
		echo $this->Html->script('dropdown/script');
		echo $this->Html->script('language');
        echo $this->Html->script('periodic/action_mail_process');
		
		echo $scripts_for_layout;
	?>
	<?
		// Subsección para contenido para el <head> desde las vistas.
		$this->Layout->output($viewhead_for_layout);
	?>
	
</head>
<?php
    if (isset($bodyAttr)) {
        $bodyAttr = " $bodyAttr";
    } else {
        $bodyAttr = null;
    }
?>
<body <?php echo $bodyAttr; ?>
    data-mail-process-url="<?php echo $html->url(array('controller' => 'mails', 'action'=>'process'))?>"
<?php if(array_key_exists('User', $session->read('Auth'))): ?>
    data-user-id="<?php echo $currentUserId; ?>"
    data-imported-actions-url="<?php echo $html->url(array('controller' => 'imported_actions'))?>"
<?php endif ?>
    >
	<div id="container">
		<div id="header">			
			<div class="sitetitle">				
				<a href="<? echo $this->Html->url(array('controller'=>'home', 'action'=>'index')); ?>" title="<?$multilang->__("HomeLinkTooltip")?>">
					LegaleCloud
				</a>					
			</div>
			<div class="loginInfo">
			
			<? if(!array_key_exists('User', $session->read('Auth'))): ?>
				<span class="loginRequest">
					<?$multilang->__("LoginText")?>
				</span>
			<? else: 
					$auth = $session->read('Auth'); ?>
				<span class="username">
					<?$multilang->__("Bienvenido", $auth['User']['fullname'])?>					
				</span>
				<span id="flagTray" data-url="<?=$this->Html->url(array('controller'=>'users', 'action'=>'ajaxChangeLanguage'))?>">
					<span class="flag spanish" data-langid="2">
						<img src="<?php echo $this->webroot?>/img/flags/Spain-icon.png">
					</span>
					<span class="flag portuguese" data-langid="3">
						<img src="<?php echo $this->webroot?>/img/flags/Portugal-icon.png">
					</span>
					<span class="flag english" data-langid="1">
						<img src="<?php echo $this->webroot?>/img/flags/United-Kingdom-icon.png">
					</span>
				</span>
				<span class="linkBlock">					
					<? if($currentUserRole['Role']['codename'] == 'LAWYER' or $currentUserRole['Role']['codename'] == 'ADMIN'): ?>
					<span class="qaLink">
						<? echo $this->Html->link("Oráculo", 'https://legalecloud.com/oraculo', array('escape'=>false)).' '; ?>
					</span>
					<? endif; ?>
					<? if($currentUserRole['Role']['codename'] == 'LAWYER'): ?>
					<span class="walletLink">
						<? echo $this->Html->link($multilang->text("VerCartera"), array('controller'=>'user_items', 'action'=>'index'), array('escape'=>false)).' '; ?>
					</span>

                    <span id="importedActionsMenu" style="display: none;">
                    </span>
					<? endif; ?>
					<span class="logoutLink">
						<? echo $this->Html->link($multilang->text("Desconectar"), array('controller'=>'users', 'action'=>'logout'), array('escape'=>false)); ?>
					</span>					
				</span>
				
			<? endif; ?>			
			</div>
			<? 
				// Subsección para el encabezado desde las vistas
				$this->Layout->output($subheader_for_layout);
			?>			
		</div>

        <div id="loadingIndicator" class="loadingIndicator" style="display: none">
                <?php echo $this->Html->image('spinner.gif', array('alt'=>'Waiting', 'height'=>'16', 'width'=>'16')) ?>
            <?$multilang->__("Cargando")?>...
        </div>
        <div id="savingIndicator" class="savingIndicator" style="display: none">
                <?php echo $this->Html->image('spinner.gif', array('alt'=>'Waiting', 'height'=>'16', 'width'=>'16')) ?>
            <?$multilang->__("Guardando")?>...
        </div>

		<div id="content">
			<?php				
					echo $session->flash();
					echo $session->flash('auth');				
			?>
			<?php echo $content_for_layout; ?>
		</div>
		<?php echo $this->Html->image('spinner.gif', array('id' => 'busy-indicator')); ?>
		<div id="footer">
			<?php 
				// Subsección para el pie desde las vistas
				$this->Layout->output($footer_for_layout);			
			?>		
		</div>	
					
	</div>	
	<div class="termsBlock">
		<? echo $this->Html->link($multilang->text("TermsLink"),array('controller'=>'users', 'action'=>'terms')); ?>
	</div>
	<div class="contactBlock">
		<?$multilang->__("HelpFooter")?>		
	</div>

    <?php if($currentUserRole['Role']['codename'] == 'LAWYER'): ?>
        <div id="importedActionsInbox" style="display: none;">
            <h1><?$multilang->__("ImportedActions")?></h1>
            <div class="contents">
            </div>
        </div>
    <?php endif ?>

	<?php // echo $this->element('sql_dump'); ?>
</body>
</html>
