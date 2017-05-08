<a name="2.0.2"></a>
## [2.0.2](https://github.com/hypeJunction/elgg_file_viewer/compare/2.0.1...v2.0.2) (2017-05-08)


### Bug Fixes

* **manifest:** fix manifest version ([728ede9](https://github.com/hypeJunction/elgg_file_viewer/commit/728ede9))



<a name="2.0.1"></a>
## [2.0.1](https://github.com/hypeJunction/elgg_file_viewer/compare/2.0.0...v2.0.1) (2017-05-08)


### Bug Fixes

* **views:** do not restrict special content views by file subtype ([aac782d](https://github.com/hypeJunction/elgg_file_viewer/commit/aac782d))



<a name="2.0.0"></a>
# 2.0.0 (2016-03-18)


### Features

* **composer:** add composer support ([0b41a49](https://github.com/hypeJunction/elgg_file_viewer/commit/0b41a49))
* **composer:** use composer autoloading for PHP classes ([af3c917](https://github.com/hypeJunction/elgg_file_viewer/commit/af3c917))
* **core:** update requirements ([c2f3528](https://github.com/hypeJunction/elgg_file_viewer/commit/c2f3528))
* **grunt:** automate releases ([4923cdf](https://github.com/hypeJunction/elgg_file_viewer/commit/4923cdf))
* **mime:** rely on core for mime type detection ([18c62e4](https://github.com/hypeJunction/elgg_file_viewer/commit/18c62e4))
* **vendor:** switch to prism.js for syntax highlighting ([b99c760](https://github.com/hypeJunction/elgg_file_viewer/commit/b99c760))
* **video:** now extracts video file thumbnails using ffmpeg ([0d2cf0d](https://github.com/hypeJunction/elgg_file_viewer/commit/0d2cf0d))
* **video:** video playback is now handled by video.js instead of projekttor ([48bb595](https://github.com/hypeJunction/elgg_file_viewer/commit/48bb595))

### Performance Improvements

* **core:** do not needlessly load plugin settings ([c4dc4ba](https://github.com/hypeJunction/elgg_file_viewer/commit/c4dc4ba))
* **core:** now uses file serving api instead of web services ([9537731](https://github.com/hypeJunction/elgg_file_viewer/commit/9537731))
* **video:** ffmpeg video conversion now takes place in shutdown event ([aaf5523](https://github.com/hypeJunction/elgg_file_viewer/commit/aaf5523))


### BREAKING CHANGES

* video: Projekktor library has been replaced by video.js. divx is no longer supported
* vendor: Syntax highlighting is now performed by Prism.js hence SyntaxHighlighter API
is no longer loaded and/or avaialable.
* core: ob viewtype is no longer avaialble. efv.download web services method is no
longer available. Public URLs are now built with file serving API
* core: EFV_MIME_REMAP global is now longer available. Use mime_remap plugin setting
instead
* core: Now requires Elgg 2.1



