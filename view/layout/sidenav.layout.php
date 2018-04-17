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
				
                <li><a href="<?= URL_ROOT.'/view/home/';?>"><i class="fa fa-area-chart fa-fw"></i> <span class="nav-label"></span></a></li>
                <li><a href="<?= URL_ROOT.'/view/devices/';?>"><i class="fa fa-th-large fa-fw"></i> <span class="nav-label"></span></a></li>
                <li><a href="<?= URL_ROOT.'/view/location/';?>"><i class="fa fa-map-marker fa-fw"></i> <span class="nav-label"></span></a></li>
                <li><a href="<?= URL_ROOT.'/view/group/';?>"><i class="fa fa-cubes fa-fw"></i> <span class="nav-label"></span></a></li>
				
				<li><br></li>
                <li><a href="<?= URL_ROOT.'/view/user/';?>"><i class="fa fa-user fa-fw"></i> <span class="nav-label"></span></a></li>			
				<?php if(htmlentities($_SESSION[SES_NAME]['user_role'], ENT_QUOTES, 'UTF-8') == 1){ ?>
				<li><a href="<?= URL_ROOT.'/view/users/';?>"><i class="fa fa-users fa-fw c-accent"></i> <span class="nav-label"></span></a></li>
				<?php }; ?>
				<li><br></li>

				<?php if(htmlentities($_SESSION[SES_NAME]['user_role'], ENT_QUOTES, 'UTF-8') == 1){ ?>
				<li><a href="<?= URL_ROOT.'/view/logging/';?>"><i class="fa fa-file-text fa-fw c-accent"></i> <span class="nav-label">Logging</span></a></li>
				<li><a href="<?= URL_ROOT.'/view/settings/';?>"><i class="fa fa-gear fa-fw c-accent"></i> <span class="nav-label"></span></a></li>
				<?php }; ?>				
            </ul>

        </div>
	
    </nav>