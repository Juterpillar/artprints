<?php

class HomeView extends View
{
	protected function displayContent()
	{
		if ($this->device['type'] == 'desktop') {
			$height = 'height="290"';
		}
		$banners = $this->model->getBanners('', 'visible');
		if (is_array($banners)) {
			$html = "\t\t\t\t".'<div id="flexslider" '.$height.'>'."\n";
			$html .= "\t\t\t\t\t".'<ul id="slides">'."\n";
			$i = 0;
			foreach ($banners as $banner) {
				extract($banner);
				// set the first banner class to 'first_banner' and display: block
				if ($i == 0 && $bannerVisibility == 'visible') {
					$html .= "\t\t\t\t\t\t".'<li>'."\n";
					$html .= "\t\t\t\t\t\t\t".'<a class="banner" href="'.$bannerLink.'"><img class="first_banner" src="images/banners/'.$this->device['type'].'/'.$bannerName.'" alt="'.$bannerAlt.'" width="'.$this->device['banner'].'" style="display: block"/></a>'."\n";
					$html .= "\t\t\t\t\t\t".'</li>'."\n";
					$i = 1;
				}
				// set each subsequent banner class to 'more_banners' and display: none
				else if ($i == 1 && $bannerVisibility == 'visible') {
					$html .= "\t\t\t\t\t\t".'<li>'."\n";
					$html .= "\t\t\t\t\t\t\t".'<a class="banner" href="'.$bannerLink.'"><img class="more_banners" src="images/banners/'.$this->device['type'].'/'.$bannerName.'" alt="'.$bannerAlt.'" width="'.$this->device['banner'].'" style="display: none;"/></a>'."\n";
					$html .= "\t\t\t\t\t\t".'</li>'."\n";
					$i = 1;
				}	
			}
			$html .= "\t\t\t\t\t".'</ul>'."\n";
			$html .= "\t\t\t\t".'</div> <!-- flexslider -->'."\n";
		}
		// only show the icon navigation if not on mobile
		if ($this->device['type'] != 'mobile') {
			$html .= "\t\t\t\t".'<div class="icon_nav">'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=framed"><figure class="icon_set">'."\n";
			$html .= "\t\t\t\t\t\t".'<img src="images/framed_icon.jpg" title="view our selection of framed art" alt="example of a framed art" width="240" height="180" />'."\n";
			$html .= "\t\t\t\t\t\t".'<figcaption>framed art</figcaption>'."\n";
			$html .= "\t\t\t\t\t".'</figure></a> <!-- icon_set -->'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=canvas"><figure class="icon_set">'."\n";
			$html .= "\t\t\t\t\t\t".'<img src="images/canvas_icon.jpg" title="view our selection of canvas prints" alt="example of a canvas print" width="240" height="180" />'."\n";
			$html .= "\t\t\t\t\t\t".'<figcaption>canvas</figcaption>'."\n";
			$html .= "\t\t\t\t\t".'</figure></a> <!-- icon_set -->'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=prints"><figure class="icon_set">'."\n";
			$html .= "\t\t\t\t\t\t".'<img src="images/print_icon.jpg" title="view our selection of art prints" alt="example of an art print" width="240" height="180" />'."\n";
			$html .= "\t\t\t\t\t\t".'<figcaption>prints</figcaption>'."\n";
			$html .= "\t\t\t\t\t".'</figure></a> <!-- icon_set -->'."\n";
			$html .= "\t\t\t\t".'</div> <!-- icon_nav -->'."\n";
		}
		$html .= $this->model->textTo2Cols($this->pageInfo['pageContent'], '' , 'home_text');
		return $html;
	}
}
?>
