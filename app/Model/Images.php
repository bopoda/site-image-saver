<?php

namespace Model;

class Images
{
	public function getTableName()
	{
		return 'images';
	}

	public function saveImage($domain, $imgUrl, $imgBinary)
	{
		$sql = "INSERT INTO {$this->getTableName()} (domain, img_url, img_url_md5, image, added_at)
 			VALUES (:domain, :img_url, :img_url_md5, :image, :added_at)
			ON DUPLICATE KEY UPDATE img_url = :img_url, image= :image, updated_at = :updated_at";
		$preparedResult = DB::getConnection()->prepare($sql);
		$preparedResult->execute(array(
			':domain' => $domain,
			':img_url' => $imgUrl,
			':img_url_md5' => $this->getImgUrlHash($imgUrl),
			':image' => $imgBinary,
			':added_at' => date('Y-m-d H:i:s'),
			':updated_at' => date('Y-m-d H:i:s'),
		));
	}

	private function getImgUrlHash($imgUrl)
	{
		return md5(mb_strtolower(preg_replace('@^https?://@i', '', trim($imgUrl))), true);
	}
}