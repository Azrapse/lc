<fieldset class="create_account_panel">
	<legend>
		<?$multilang->__('CrearCuenta')?>

	</legend>
	
	<div>
	<?    
		echo $form->create('User', array('action' => 'create_account'));
	?>
		<div class="identification_data">
	<?
		echo $form->input('username', array('label'=>$multilang->text('IdentificadorUsuario'), 'autocomplete'=>'off','maxLength'=>50));
		echo $form->input('password', array('label'=>$multilang->text('Contrasena'),'maxLength'=>50));
		echo $form->input('password_confirm', array('label'=>$multilang->text('ConfirmarContrasena'), 'type'=>'password','maxLength'=>50, 'div'=>array('class'=>'required')));		
		echo $form->input('email', array('label'=>$multilang->text('Email'), 'maxLength'=>50));		
	?>
		</div>
		<div class="place_data" style="display: none;">
	<?
		echo $form->input('fullname', array('label'=>$multilang->text('NombreCompleto'), 'autocomplete'=>'off','maxLength'=>256));
		echo $form->input('college_number', array('label'=>$multilang->text('NumeroColegiado'), 'autocomplete'=>'off','maxLength'=>100));
		echo $form->input('address', array('label'=>$multilang->text('Dirección'), 'autocomplete'=>'off','maxLength'=>500));
		echo $form->input('town', array('label'=>$multilang->text('Localidad'), 'autocomplete'=>'off','maxLength'=>100));
		echo $form->input('province', array('label'=>$multilang->text('Provincia'), 'autocomplete'=>'off','maxLength'=>50));
		echo $form->input('postal_code', array('label'=>$multilang->text('CodigoPostal'), 'autocomplete'=>'off','maxLength'=>10));		
	?>
		</div>
	<?
		echo $form->end($multilang->text('CrearCuenta'));
	?>
	<form>
		<div class="required">
			<label>
				<?$multilang->__('CampoObligatorio')?>
			</label>
		</div>
	</form>
	</div>
</fieldset>