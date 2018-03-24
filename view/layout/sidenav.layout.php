    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"> <?= htmlspecialchars($_SESSION[SES_NAME]['user_name'], ENT_QUOTES, 'UTF-8');?></strong>
                             </span> <span class="text-muted text-xs block"> <?= htmlspecialchars($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');?> <b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="Src/Login/logout.php?csrf=<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">Logout</a></li>
                            </ul>
                    </div>
                    <div class="logo-element">
                        <?= APP_TITLE;?>
                    </div>
                </li>
                <li ><a href="<?= URL_ROOT.'/view/home/?site='.preg_replace("/[^0-9]/","",$_GET['site']);?>"><i class="fa fa-th-large fa-fw"></i> <span class="nav-label"></span></a></li>
                <li ><a href="<?= URL_ROOT.'/view/user/?site='.preg_replace("/[^0-9]/","",$_GET['site']);?>"><i class="fa fa-user fa-fw"></i> <span class="nav-label"></span></a></li>
				
				<?php
				$obj = new Home(new SafeMySQL(array('db'=>'scs_motion')));
				if(isset($_GET['site'])){
					$obj = new Home(new SafeMySQL(array('db'=>'scs_motion')),$_GET['site']);
					foreach($obj->getZones() as $device){
						
						echo '<li>'.$device['link'].'</li>';
					};					
				}
				?>
 				
            </ul>

        </div>
	
    </nav>