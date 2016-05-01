# site-image-saver

[![Build Status](https://travis-ci.org/bopoda/site-image-saver.svg?branch=master)](https://travis-ci.org/bopoda/site-image-saver)

<div>sql:</div>
<pre>
CREATE TABLE `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NOT NULL,
  `img_url` varchar(1024) NOT NULL,
  `img_url_sha1` binary(16) NOT NULL,
  `image` longblob NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `img_url_sha1` (`img_url_sha1`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
</pre>

<div>Run script as cli:</div>
<ul>
<li><code>php cli.php --domain=www.domain.com</code>  #it will save all images from site without any limits</li>
<li><code>php cli.php --domain=www.domain.com --maxIterations=50</code>  #you can limit iterations count (count http-queries to unique internal pages)</li>
<li><code>php cli.php --domain=www.domain.com --maxImages=100</code>  #you can limit images count</li>
</ul>

<div>TODO:</div>
<ol>
  <li>Учитывать, что ссылки страницы могут строиться относительно &lt;base href=""&gt;, если такой тег задан в head страницы.</li>
  <li>Приводить строки к utf8 перед работой с DOMDocument либо использовать регулярки</li>
</ol>