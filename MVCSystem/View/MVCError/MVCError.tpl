<style type="text/css">
	.MVCError {
		border: 1px solid red;
		margin: 5px;
		padding: 5px;
	}
	.MVCError * {
		font-size: 14px;
		font-family: monospace;
	}
	.MVCError strong {
		color: red;
		font-size: 1.6em;
	}
	.MVCError p {
		margin: 5px;
		color: darkred;
		font-size: 1.3em;
	}
	.MVCError blockquote {
		color: gray;
		font-size: 1.1em;
	}
</style>
<div class="MVCError">
	<strong>MVC System Error</strong>
	<p>{MVCErrorContent}</p>
	<blockquote>{MVCErrorInformation}</blockquote>
</div>