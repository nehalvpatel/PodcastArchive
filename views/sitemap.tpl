{{ "<?" }}xml version="1.0" encoding="utf-8"{{ "?>" }}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
	<url>
		<loc>{{ @domain }}</loc>
		<changefreq>weekly</changefreq>
	</url>
	<url>
		<loc>{{ @domain }}credits</loc>
		<changefreq>weekly</changefreq>
	</url>
	<url>
		<loc>{{ @domain }}feedback</loc>
		<changefreq>weekly</changefreq>
	</url>
	<repeat group="{{ @episodes }}" value="{{ @episode }}">
		<url>
			<loc>{{ @domain }}episode/{{ @episode->getNumber(); }}</loc>
			<changefreq>monthly</changefreq>
		</url>
	</repeat>
	<repeat group="{{ @people }}" value="{{ @person }}">
		<url>
			<loc>{{ @domain }}person/{{ @person->getID(); }}</loc>
			<changefreq>monthly</changefreq>
		</url>
	</repeat>
</urlset>