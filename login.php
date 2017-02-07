<?php if ( !isset($_SESSION) ) session_start(); ?>
<?php include('header.php'); 
include('dbconfig.php'); ?>
<?php  error_reporting(E_ALL);
ini_set('display_errors', 1); ?>
<div class="container">
	<header>
		<h1 style="text-align:center;">GECI</h1>	
		<h2 style="text-align:center;">User Management Utility</h2>
		<h3 style="text-align:center;">By TEAM GECI</h3>
	</header>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 well" style="box-shadow: 10px 10px 5px #888888;">
			<div class="panel panel-primary">
				<div class="panel panel-heading">
					<p><strong>Login using Registered Credentials</strong></p>
				</div>
				<div class="panel-body">		
					<form class="form-horizontal" id="loginform" action="" method="POST">
					<div class="form-group form-group-sm">
							<label class="col-sm-4 control-label" for="user_level">User Level</label>
							<div class="col-sm-8">
					<?php 
								$stmt = $DB_con->prepare("SELECT * FROM userlevel WHERE status = 'Active' order by user_level");
								$stmt->execute(array());
								echo '<select class="myCombo" id="user_level" name="user_level" autofocus required tabindex="1">';
								while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									echo '<option value="'.$row['user_level'].'">'.$row['ul_name'].'</option>';
								} 
									echo '</select>';
								echo '</div></div>'; ?>
						<div class="form-group form-group-sm">
							<label class="col-sm-4 control-label" for="inputEmail">Username</label>
							<div class="col-sm-8">
								<input type="text" id="txt_username" name="username" placeholder="Registered Username" required class="form-control" autofocus>
							</div>
						</div>
						<div class="form-group form-group-sm">
							<label class="col-sm-4 control-label" for="inputPassword">Password</label>
							<div class="col-sm-8">
								<input type="password" id="txt_password" name="password" placeholder="Password" placeholder="Password" required class="form-control">
							</div>
						</div>
						<div class="form-group form-group-sm">
							<div class="col-sm-2 col-sm-offset-4">
								<button id="btn_login" name="btn_login" type="submit" class="btn btn-primary">&nbsp;Submit</button>
							</div>
							<div class="col-sm-2">
								<button id="btn_cancel" name="btn_cancel" type="reset" class="btn btn-success">&nbsp;Cancel</button>
							</div>
						</div>
					</form>
					<?php
					if (isset($_POST['btn_login'])){
						$username = $_POST['username'];
						$password = $_POST['password'];
						$password= sha1($password);
						
						try {
							$stmt = $DB_con->prepare("SELECT user_id FROM users WHERE 1");
							$stmt->execute(array());
						}
						catch(PDOException $e) {
							try {
								//include('database.php');
								$stmt = $DB_con->prepare("SELECT user_id FROM users WHERE 1");
								$stmt->execute(array());
							}
							catch(PDOException $e) {
								echo "Error Accessing Data: " . $e->getMessage();
							}
						}
						
						$count = $stmt->rowCount();
						if( $count == 0 ) {
							$password = sha1('admin');
							$stmt = $DB_con->prepare("insert into users (doj, firstname, username, password, user_level, status)
								values(CURDATE(), 'Administrator', :username, :password, :level, 'Active')");
							$stmt->execute(array(':username' => 'admin', ':password' => $password, ':level' => 1));
						}
						try
							{
							$stmt = $DB_con->prepare("SELECT * FROM users WHERE username=:username AND password =:password AND status =:status");
							$stmt->execute(array(':username' => $username, ':password' => $password, ':status' => 'Active'));
							$count = $stmt->rowCount();
						}
						catch(PDOException $e) {
							echo "Error: " . $e->getMessage();
						}
	
						if( $count == 1 ) {
							$row=$stmt->fetch(PDO::FETCH_ASSOC);
			
							$_SESSION['id']=$row['user_id'];
							$_SESSION['username']=$row['firstname'].' '.$row['lastname'];
							$_SESSION['user_level']= $row['user_level'];
							echo '<script language="javascript">window.location.href ="users.php";</script>';
						}
						else
							{
							echo '<script>cmodal("Access Denied!", "No Active User account with the given Username/Password Combination!", "error", "index.php")</script>';
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>