<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Insighthub</title>

	@if ($siteSettings && $siteSettings->favicon)    
		<link rel="icon" href="{{$siteSettings->favicon}}">  
	@endif

	<script>
		window.siteSettings = @json($siteSettings)
	</script>

	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen h-full">
	<div id="app" class="h-full min-h-screen"></div>
</body>
</html>