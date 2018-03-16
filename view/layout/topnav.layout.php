        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary hidden" href="#" id="menu_bar"><i class="fa fa-bars"></i> </a>
                    <!--<form role="search" class="navbar-form-custom" method="post" action="#">
                        <div class="form-group">
                            <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                        </div>
                    </form>-->
                </div>
                <ul class="nav navbar-top-links navbar-right">
					<li>
						<a href="<?= URL_ROOT.'view/user/';?>"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['db_user']['user_email'], ENT_QUOTES, 'UTF-8');?></a>
					</li>
                    <li>
                        <a href="/mdb/Src/controllers/login.controller.php?logout&csrf=<?= $_SESSION['db_token'];?>">
                            <i class="fa fa-sign-out"></i> <span data-i18n="[html]layout.topnav.logout"> Log out </span>
                        </a>
                    </li>
					<?php if(APP_LANG == 'en'){ ?>
                    <li><a class="set_en active"><img src="/mdb/img/flags/16/United-Kingdom.png"> EN</a></li>
					<?php } elseif(APP_LANG == 'nl'){ ?>
                    <li> <a class="set_en active"><img src="/mdb/img/flags/16/Netherlands.png"> NL</a></li>					
					<?php } elseif(APP_LANG == 'pt') { ?>
					<li><a class="set_es"><img src="/mdb/img/flags/16/Brazil.png"> PT</a></li>
					<?php };?>
                </ul>

            </nav>
        </div>