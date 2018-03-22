        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 hidden" href="#" id="menu_bar"><i class="stroke-hamburgermenu"></i> </a>
                    <!--<form role="search" class="navbar-form-custom" method="post" action="#">
                        <div class="form-group">
                            <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                        </div>
                    </form>-->
                </div>
                <ul class="nav navbar-top-links navbar-right">
					<li>
						<a href="<?= URL_ROOT.'/view/user/?user='.htmlspecialchars($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');?>"><i class="fa fa-user"></i> <span id="user_span"><?= htmlspecialchars($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');?></span></a>
					</li>
                    <li>
                        <a href="<?= URL_ROOT;?>/Src/controllers/login.controller.php?logout&csrf=<?= $_SESSION['db_token'];?>">
                            <i class="fa fa-sign-out"></i> <span data-i18n="[html]layout.topnav.logout" id="log_out_span"> Log out </span>
                        </a>
                    </li>
					<?php if(APP_LANG == 'en'){ ?>
                    <li><a class="set_en active"><img src="<?= URL_ROOT_IMG;?>/flags/16/United-Kingdom.png"> EN</a></li>
					<?php } elseif(APP_LANG == 'nl'){ ?>
                    <li> <a class="set_en active"><img src="<?= URL_ROOT_IMG;?>/flags/16/Netherlands.png"> NL</a></li>					
					<?php } elseif(APP_LANG == 'pt') { ?>
					<li><a class="set_es"><img src="<?= URL_ROOT_IMG;?>/flags/16/Brazil.png"> PT</a></li>
					<?php };?>
                </ul>

            </nav>
        </div>