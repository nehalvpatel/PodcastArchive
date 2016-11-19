				<ul id="footer-links">
					<li><a href="<?php echo $domain; ?>credits">Developers and Contributors</a></li>
					<li><a href="<?php echo $domain; ?>feedback">Provide us with Feedback</a></li>
				</ul>
			</div>
		</section>
		<div id="loader"></div>
		<script type="application/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="application/javascript">var domain = "<?php echo $domain; ?>";var site_name = "<?php echo $Podcast->getName(); ?>";var site_title = "<?php echo $Podcast->getTitle(); ?>";</script>
		<script type="application/javascript" src="<?php echo $domain; ?>js/main.js?ver=<?php echo filemtime("js/main.js"); ?>"></script>