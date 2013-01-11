<?php
require 'gitwiki.php';
$g=New Gitwiki('https://github.com/mootools/mootools-core/wiki',$_GET['wikiurl']);
$html=$g->getWiki();
echo $html;
