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
                <li ><a href="<?= URL_ROOT.'/view/home/';?>"><i class="fa fa-th-large fa-fw"></i> <span class="nav-label"></span></a></li>
                <li ><a href="<?= URL_ROOT.'/view/user/';?>"><i class="fa fa-user fa-fw"></i> <span class="nav-label"></span></a></li>
				
                <li ><a href="<?= URL_ROOT.'/view/zone/?id=1';?>">#1</a></li>
                <li ><a href="<?= URL_ROOT.'/view/zone/?id=2';?>">#2</a></li>
                <li ><a href="<?= URL_ROOT.'/view/zone/?id=3';?>">#3</a></li>
 				
            </ul>

        </div>
	
    </nav>