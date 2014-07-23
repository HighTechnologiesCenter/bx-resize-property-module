<?php
namespace CustomPropertiesModule;


class ImageProcessingHelper {
	/**
	 * Проверка параметров ширины и высоты ресайза.
	 * В случае, если значения одного из параметров нет происходит подсчет пропорционального значения из исходного изображения
	 *
	 * @param $width
	 * @param $height
	 * @param $currentHeight
	 * @param $currentWidth
	 * @return array
	 */
	public static function processResizeSizes($width, $height, $currentHeight, $currentWidth)
	{
		$width = intval($width);
		$height = intval($height);
		if (($width > 0) && ($height == 0)) {
			if ($currentWidth > 0) {
				$height = intval($width * $currentHeight / $currentWidth);
			}
		} elseif (($height > 0) && ($width == 0)) {
			if ($currentHeight > 0) {
				$width = intval($height * $currentWidth / $currentHeight);
			}
		}
		return array(
			'HEIGHT' => $height,
			'WIDTH' => $width
		);
	}
} 