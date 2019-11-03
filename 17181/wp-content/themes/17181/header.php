<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="utf-8">  
<title><?php wp_title('|',1,'right'); ?> <?php bloginfo('name'); ?></title>  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<meta name="description" content="">  
<meta name="author" content="">    
<!-- Styles -->  
<link href="<?php bloginfo('stylesheet_url');?>" rel="stylesheet">  
   
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->  
<!--[if lt IE 9]>  
<script src="../assets/js/html5shiv.js"></script>  
<![endif]-->    
<?php wp_enqueue_script("jquery"); ?> <?php wp_head(); ?>  
<!-- Fav and touch icons -->  
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">  
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">  
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">  
<link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">  
<link rel="shortcut icon" href="../assets/ico/favicon.png">  
</head>    
<body>   
<div class="navbar navbar-fixed-top">
        <div class="navbar-inner">  
            <div class="container">
            <div class="logo-name pull-left">
            	<a class="brand" href="<?php echo get_option('home'); ?>/"><?php bloginfo( 'name' ); ?></a>
            </div>
                <ul class="nav pull-right">
                     <?php 
								wp_nav_menu( array(
									'menu'       => 'top_menu',
									'depth'      => 3,
									'container'  => false,
									'menu_class' => 'nav',
									'fallback_cb' => 'wp_page_menu',
									//Process nav menu using our custom nav walker
									'walker' => new wp_bootstrap_navwalker())
								);
                           ?>
                           <li class="search-screen" style="padding-bottom:-5px; font-family: myFont2;"><?php get_search_form(); ?></li>
                </ul>
               
            </div>
            
        </div>
        
    </div> 
<div class="container">
