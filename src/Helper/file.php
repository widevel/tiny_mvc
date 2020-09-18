<?php

function copy_file_contents(string $source, string $target) {
	file_put_contents($target, file_get_contents($source));
}