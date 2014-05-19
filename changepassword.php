<?php 
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggeIn()){
	Redirect::to('index.php');
}
	
if(Input::exists()){
	if(Token::check(Input::get('token'))){

		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'password_current' => array(
				'required' => true,
				'min' => 6
			),
			'password_new' => array(
				'required' => true,
				'min' => 6
			),
			'password_new_again' => array(
				'required' => true,
				'min' => 6,
				'matches' => 'password_new'
			)
		));

		if($validation->passed()){

			if(Hash::make(Input::get('password_current'), $user->data()->salt) !==$user->data){
				echo 'Password is wrong.';
			} else {
				$salt = Hash::salt(32);
				$user->update(array(
					'password' => Hash::make(Input::get('password_new'), $salt),
					'salt' => $salt
				));

				Session::flash('home', 'Your pass has been changed.');
				Redirect::to('index.php');
			}

		} else {
			foreach ($validation-errors() as $error) {
				echo $error, '<br>';
			}
		}
	}
}	
?>

<form action="" method="post">
	<div class="field">
		<label for="password_current">Current Password</label>
		<input type="text" name="password_current" id="password_current">
	</div>

	<div class="field">
		<label for="password_new">Current Password</label>
		<input type="text" name="password_new" id="password_new">
	</div>

	<div class="field">
		<label for="password_new_again">Current Password again</label>
		<input type="text" name="password_new_again" id="password_new_again">
	</div>

		<input type="submit" value="Change">
		<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
	</div>
</form>