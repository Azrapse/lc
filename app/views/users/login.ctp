<?php echo $session->flash('auth'); ?>

<div class="loginContainer">
	
	<div class="loginPanel universal">
		<fieldset>
			<legend>
				<?php $multilang->__("Acceso")?>
			</legend>
		<?php 	
			echo $form->create('User', array('action' => 'uni_login'));
			echo $form->input('identificator', array('label'=>$multilang->text('NombreDeAcceso'), 'autocomplete'=>'off'));
			echo $form->input('wordofpass', array('label'=>$multilang->text('Contrasena'), 'type'=>'password'));
			echo $form->end($multilang->text('Acceder'));
		?>
		<div class="loginPanel newAccount">
			<span>						
				<a href="<?php  echo $html->url(array('controller'=>'users', 'action'=>'create_account')); ?>">
					<?php $multilang->__("DateDeAlta")?>				
				</a>
			</span>	
		</div>
		</fieldset>
	</div>
	<div class="loginDescription"> 
		<ul>
			<li>
				<img src="<?php echo $this->webroot?>img/metro/cloud.png"/>
				<?php $multilang->__("EnLaNube")?>				
			</li>
			<li>
				<img src="<?php echo $this->webroot?>img/metro/law.png"/>
				<?php $multilang->__("MantenInformados")?>				
			</li>
			<li>
				<img src="<?php echo $this->webroot?>img/metro/email.png"/>				
				<?php $multilang->__("Recordatorios")?>
				<img class="hoverShow" src="/img/reminders.gif" alt="Demo">
			</li>
			<li>
				<img src="<?php echo $this->webroot?>img/metro/paperplane.png"/>
				<?php $multilang->__("MailActionsFeature")?>
				<img class="hoverShow" src="/img/email_sincro.gif" alt="Demo">
			</li>
			<!--<li>
				<img src="<?php echo $this->webroot?>img/metro/oraculo.png"/>
				<?php $multilang->__("ResuelveDudas")?>				
			</li>
			<li>
				<img src="<?php echo $this->webroot?>img/metro/blog.png"/>
				<?php $multilang->__("BlogNoticias")?>					
			</li>-->
			<li>
				<img src="<?php echo $this->webroot?>img/metro/qrcode.png"/>
				<?php $multilang->__("QrCodes")?>	
				<img class="hoverShow" src="/img/codigo_qr.gif" alt="Demo">
			</li>
		</ul>
	</div>
	
	<div class="video">
		<?php $multilang->__("DemoVideo")?>		
	</div>
	<div id="appDownloadLink">
		<?php $multilang->__("AppDownloadLink")?>		
	</div>
</div>