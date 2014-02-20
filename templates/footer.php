				<ul id="footer-links">
					<li><a href="<?php echo $domain; ?>credits.php">Developers and Contributors</a></li>
					<li><a href="<?php echo $domain; ?>feedback.php">Provide us with Feedback</a></li>
				</ul>
			</div>
		</section>
		<div id="loader"></div>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript">var domain = "<?php echo $domain; ?>";</script>
		<script type="text/javascript" src="<?php echo $domain; ?>js/main.js?ver=<?php echo $commit_count; ?>"></script>
		<!--[if lt IE 9]>
			<script type="text/javascript">
				$(document).ready(function(){
					$(".toggle-menu").click(function(){
						$(".main").css({"display": "none"});
						$(".main").css({"display": "block"});
					});
				});
			</script>
		<![endif]-->