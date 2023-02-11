			</div>
		</div>

		<div id="footer">
			<div class="jumbotron push-spaces">
				<strong><?= lang('Common.copyrights', ['current_year' => date('Y')]) ?> ·
				<a href="https://opensourcepos.org" target="_blank"><?= lang('Common.website') ?></a>  ·
				<?= esc(config('App')->application_version) ?> - <a target="_blank" href="https://github.com/opensourcepos/opensourcepos/commit/<?= esc(config('OSPOS')->commit_sha1) ?>"><?= esc(substr(config('OSPOS')->commit_sha1, 0, 6)); ?></a></strong>.
			</div>
		</div>
	</body>
</html>
