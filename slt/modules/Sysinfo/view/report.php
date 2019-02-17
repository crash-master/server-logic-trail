<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
<link rel="stylesheet" href="/<?= \Kernel\Module::pathToModule('Sysinfo') ?>assets/css/normalize.css">
<link rel="stylesheet" href="/<?= \Kernel\Module::pathToModule('Sysinfo') ?>assets/css/milligram.css">
<link rel="stylesheet" href="/<?= \Kernel\Module::pathToModule('Sysinfo') ?>assets/css/sysinfo.css">

<div class="milligram sysinfo-container">
	<button class="button-outline show-info">Sysinfo</button>
	<div class="report">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Result</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Work time</td>
					<td><?= $work_time ?> s</td>
				</tr>
				<tr>
					<td>Total sql queries</td>
					<td><?= $total_sql_queries ?></td>
				</tr>
				<tr>
					<td>Total components for page</td>
					<td><?= $total_components_for_page ?></td>
				</tr>
				<tr>
					<td>Worked controller</td>
					<td>
						<strong>
							<?= $controller_for_this_page['controller'] ?>@<?= $controller_for_this_page['action'] ?>
						</strong>
					</td>
				</tr>
				<tr>
					<td>Total files of cache used</td>
					<td>
						<?= $total_files_of_cache_used ?>
					</td>
				</tr>

				<?php if ($total_files_of_cache_used): ?>
					<tr>
						<td colspan="2">
							<br><br>
							<h3>Cache files</h3>
							<ul>
								<?php foreach ($cache_use_list as $key => $item): ?>
									<li><?= $item ?></li>
								<?php endforeach ?>
							</ul>
						</td>
					</tr>
				<?php endif ?>

				<?php if ($total_sql_queries): ?>
					<tr>
						<td colspan="2">
							<br><br>
							<h3>SQL queries</h3>
							<ul>
								<?php foreach ($sql_queries_list as $key => $item): ?>
									<li><?= $item ?></li>
								<?php endforeach ?>
							</ul>
						</td>
					</tr>
				<?php endif ?>

				<?php if ($total_components_for_page): ?>
					<tr>
						<td colspan="2">
							<br><br>
							<h3>Components for this page</h3>
							<?php foreach ($components_list as $key => $item): ?>
								<? 
									list($name) = array_keys($item);
									list($template) = array_keys($item[$name]);
									$controller = $item[$name][$template][0];
								?>
								
<pre><code>[<b><?= $name ?>]</b>: <?= $template ?><br>
<?= $controller ?></code></pre>
								
							<?php endforeach ?>
						</td>
					</tr>
				<?php endif ?>
			</tbody>
		</table>
	</div>
</div>

<script src="/<?= \Kernel\Module::pathToModule('Sysinfo') ?>assets/js/sysinfo.js"></script>

