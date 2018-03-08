    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"> <?= htmlspecialchars($_SESSION['user']['user_name'], ENT_QUOTES, 'UTF-8');?></strong>
                             </span> <span class="text-muted text-xs block"> <?= htmlspecialchars($_SESSION['user']['user_email'], ENT_QUOTES, 'UTF-8');?> <b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="Src/Login/logout.php?csrf=<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8');?>">Logout</a></li>
                            </ul>
                    </div>
                    <div class="logo-element">
                        DB+
                    </div>
                </li>
                <li><a href="<?= URL_ROOT.'view/home/';?>"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a></li>
                <li><a href="<?= URL_ROOT.'view/ticket/';?>"><i class="fa fa-ticket"></i> <span class="nav-label">Tickets</span></a></li>
                <li><a href="<?= URL_ROOT.'view/tools/';?>"><i class="fa fa-wrench"></i> <span class="nav-label">Tools</span></a></li>
				
				<?php if(htmlentities($_SESSION['user']['user_role'], ENT_QUOTES, 'UTF-8') == 1){ ?>
                <li><a href="<?= URL_ROOT.'view/settings/';?>"><i class="fa fa-gear"></i> <span class="nav-label">Settings</span></a></li>
				<?php }; ?>
            </ul>

        </div>
	
    </nav>