    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"> <?= htmlspecialchars($_SESSION['db_user']['user_name'], ENT_QUOTES, 'UTF-8');?></strong>
                             </span> <span class="text-muted text-xs block"> <?= htmlspecialchars($_SESSION['db_user']['user_email'], ENT_QUOTES, 'UTF-8');?> <b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="Src/Login/logout.php?csrf=<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">Logout</a></li>
                            </ul>
                    </div>
                    <div class="logo-element">
                        <?= APP_TITLE;?>
                    </div>
                </li>
                <li><a href="<?= URL_ROOT.'view/home/';?>"><i class="fa fa-th-large fa-fw"></i> <span class="nav-label">Dashboard</span></a></li>
                <li><a href="<?= URL_ROOT.'view/ticket/';?>"><i class="fa fa-ticket fa-fw"></i> <span class="nav-label">Tickets</span></a></li>
                <li><a href="<?= URL_ROOT.'view/tools/';?>"><i class="fa fa-wrench fa-fw"></i> <span class="nav-label">Tools</span></a></li>
				
				<?php if(htmlentities($_SESSION['db_user']['user_role'], ENT_QUOTES, 'UTF-8') == 1){ ?>
                <li><a href="<?= URL_ROOT.'view/user/';?>"><i class="fa fa-users fa-fw"></i> <span class="nav-label">Users</span></a></li>
                <li><a href="<?= URL_ROOT.'view/logging/';?>"><i class="fa fa-file-text fa-fw"></i> <span class="nav-label">Users</span></a></li>
                <li><a href="<?= URL_ROOT.'view/settings/';?>"><i class="fa fa-gear fa-fw"></i> <span class="nav-label">Settings</span></a></li>
				<?php }; ?>
				
            </ul>

        </div>
	
    </nav>