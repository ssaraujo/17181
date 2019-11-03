<form style="height:15px; margin-top:5px;" method="get" id="searchform" action="<?php bloginfo('url'); ?>/"> 	
<label class="hidden" for="s"><?php _e(''); ?></label> 	<input class="search-query" type="text" placeholder="Search" value="<?php the_search_query(); ?>" name="s" id="s" /> 
</form>
