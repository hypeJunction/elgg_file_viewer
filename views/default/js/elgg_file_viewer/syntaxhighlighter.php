//<script>

	elgg.provide('elgg.syntaxhighlighter');

	elgg.syntaxhighlighter.init = function() {
		SyntaxHighlighter.all();
	}

	elgg.register_hook_handler('init', 'system', elgg.syntaxhighlighter.init);