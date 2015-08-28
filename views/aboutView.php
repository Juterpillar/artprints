<?php

class AboutView extends View
{
	protected function displayContent()
	{
		$html = "\t\t\t\t".'<div id="flexslider">'."\n";
		$html .= "\t\t\t\t\t".'<ul id="slides">'."\n";
		$html .= "\t\t\t\t\t\t".'<li>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a class="banner" href="#"><img class="first_banner" src="images/banners/'.$this->device['type'].'_artprints_team.jpg" alt="the artprints team" width="'.$this->device['banner'].'" style="display: block"/></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</li>'."\n"; 
		$html .= "\t\t\t\t\t\t".'<li>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a class="banner" href="#"><img class="more_banners" src="images/banners/'.$this->device['type'].'_nz_banner.jpg" alt="artists from New Zealand, printed in New Zealand by New Zealanders who love this place" width="'.$this->device['banner'].'" style="display: none"/></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</li>'."\n";
		$html .= "\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t".'</div> <!-- flexslider -->'."\n";
		$html .= $this->model->textTo2Cols($this->pageInfo['pageContent'], 'about us', 'home_text');
		return $html;
	}
}
?>
