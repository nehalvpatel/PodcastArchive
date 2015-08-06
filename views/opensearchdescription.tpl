{{ "<?" }}xml version="1.0" encoding="utf-8"{{ "?>" }}
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
	<ShortName>{{ @Core->getName() }}</ShortName>
	<Description>Search {{ @Core->getName() }} episodes</Description>
	<Url type="text/html" method="get" template="{{ @domain }}?query={searchTerms}" />
	<Image width="16" height="16">{{ @domain }}favicon.ico</Image>
	<InputEncoding>UTF-8</InputEncoding>
	<SearchForm>{{ @domain }}</SearchForm>
</OpenSearchDescription>