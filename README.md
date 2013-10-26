Elgg File Viewer
================

An extension to the file plugin that integrates third-party services
and providers to render files inline:

* Microsoft Office files (Office Web Viewer integration)
* PDFs and other common file types (Google Docs Viewer integration)
* Video/Audio (Projekktor & DivX Web Player)
* Text/Code (SyntaxHighlighter integration)

## Notes ##

1. Access to non-public files by Office Web Viewer and
Google Docs Viewer is managed via Elgg's web services API. You must therefore
enable Enable Web Services API via Administration > Configure > Settings >
Advanced Settings. (Please note that these viewers will not work when you are testing
locally)

2. Please make sure you comply with the Terms of Service for individual service
providers
* Office Web Viewer - http://office.microsoft.com/en-us/web-apps/view-office-documents-online-FX102727753.aspx
* Google Docs Viewer - https://docs.google.com/viewer
* Projekktor - http://www.projekktor.com/
* DivX - http://labs.divx.com/
* SyntaxHighlighter - http://alexgorbatchev.com/SyntaxHighlighter/
* ffmpeg - http://www.ffmpeg.org/

3. DivX player requires viewers to install the player on their computer. In case
when the player is not available the browser will try to use another application
installed on the user's computer. This method is very unreliable.

4. Since 1.2, you can enable ffmpeg conversion of uploaded video and audio files
to web compatible formats thus ensuring that Projekktor works properly.
To read more about ffmpeg go to http://www.ffmpeg.org/. You will need ffmpeg
library installed on your server for this to work. It is usually located at
/usr/local/bin/ffmpeg. Google the rest.


## Acknowledgements ##

* Initial development of the plugin was commissioned and sponsored by
ArckInteractive (www.arckinteractive.com)


## Screenshots ##

![alt text](https://raw.github.com/hypeJunction/elgg_file_viewer/master/screenshots/pdf.png "PDF")
![alt text](https://raw.github.com/hypeJunction/elgg_file_viewer/master/screenshots/powerpoint.png "Powerpoint")
![alt text](https://raw.github.com/hypeJunction/elgg_file_viewer/master/screenshots/video.jpg "Video")