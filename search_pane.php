<?php

/***************************************************************************

Generates the Buurtlinux search-pane.
Copyright (C) 2013	Ruud Beukema

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

***************************************************************************/

// Prevent file from being read directly
define("PAGE", "search_pane.php");
if ( strpos($_SERVER["REQUEST_URI"], constant("PAGE") ) ) {
	die("Het lezen van '".constant("PAGE")."' is niet toegestaan, sorry!");
}

require_once LIBDIR.'/db_lookup_criteria.php';

class SearchPane
{
	// Flags indicating if a certain search-filter option should be available 
	// to the user or not.
	protected $m_distros_en;
	protected $m_desktops_en;
	protected $m_actions_en;
	protected $m_groups_en;
	protected $m_targetgrp_en;
	protected $m_reward_en;


	// Class constructor
	public function __construct()
	{
		// By default show no search-criteria on the search-pane.
		$this->criteria_set_all( false );
	}

	
	// Returns the javascript-code required to make the search-pane behave 
	// dynamically.
	public function insert_javascript()
	{
		// Add javascript that taces care of folding/unfolding any search options
		$l_jscript = '
			<script type="text/javascript">
				function toggleCollapse(a){
					var img = "img_"+a;
					var e=document.getElementById(a);
					if(!e)return true;
					if(e.style.display=="none"){
						e.style.display="block"						
						document.getElementById(img).src="'.MAP_URL.'img/uitgeklapt.gif";
					}
					else{
						e.style.display="none"						
						document.getElementById(img).src="'.MAP_URL.'img/ingeklapt.gif";
					}
					return true;
				}
			</script>';
		return $l_jscript;
	}
	

	// Enable/Disable showing of search-criteria all at once.
	public function criteria_set_all( $a_enable )
	{
		$this->m_distros_en		= $a_enable;
		$this->m_desktops_en 	= $a_enable;
		$this->m_actions_en 	= $a_enable;
		$this->m_groups_en 		= $a_enable;
		$this->m_targetgrp_en 	= $a_enable;
		$this->m_reward_en		= $a_enable;
	}
	

	// Enable/Disable showing of search-criterium 'distributions'
	public function criteria_set_distros( $a_enable )
	{
		$this->m_distros_en	= $a_enable;
	}
	

	// Enable/Disable showing of search-criterium 'desktop environments'
	public function criteria_set_desktops( $a_enable )
	{		
		$this->m_desktops_en 	= $a_enable;
	}
	
	public function criteria_set_actions( $a_enable ) {		
		$this->m_actions_en 	= $a_enable;
	}
	

	// Enable/Disable showing of search-criterium 'user groups'
	public function criteria_set_groups( $a_enable )
	{		
		$this->m_groups_en 	= $a_enable;
	}
	

	// Enable/Disable showing of search-criterium 'target groups'
	public function criteria_set_targetgrp( $a_enable )
	{		
		$this->m_targetgrp_en 	= $a_enable;
	}
	

	// Enable/Disable showing of search-criterium 'reward types'
	public function criteria_set_reward( $a_enable )
	{		
		$this->m_reward_en	= $a_enable;
	}


	// Generates and returns the HTML that makes up the search-form.
	public function insert_search_pane()
	{	
		$l_pane = '<form name="map_search_form" method="get" action=""><div id="map_search_pane_title">Zoek een Linux-hulp</div>';
		
		// Insert address-field (and fill it with a value if given)		
		$l_pane .= '<div id="map_search_pane_label">Straat/Postcode/Plaats/Provincie:</div>';
		$l_pane .= $this->insert_address();		
		
		// Insert radius-field (and fill it with a value if given)
		$l_pane .= '<div id="map_search_pane_label">In een straal van:</div>';
		$l_pane .= $this->insert_radius();
		$l_pane .= '<div>&nbsp;</div>';
		
		// Optionally append more search criteria
		if ( $this->m_distros_en ) 		$l_pane .= $this->insert_distros();
		if ( $this->m_desktops_en ) 	$l_pane .= $this->insert_desktops();
		if ( $this->m_actions_en ) 		$l_pane .= $this->insert_actions();
		if ( $this->m_groups_en ) 		$l_pane .= $this->insert_groups();
		if ( $this->m_targetgrp_en ) 	$l_pane .= $this->insert_targetgrps();
		if ( $this->m_reward_en ) 		$l_pane .= $this->insert_rewards();
		
		// Add buttons and capturing of keyboard RETURN-key to the form for form
		// submission or clearing the form.
		$l_pane .= '
					<div>&nbsp;<input type="hidden" onKeyPress="if ( ( window.event.keyCode == 13 ) || (event.which == 13) ) { this.form.submit(); return false; } return true;" /></div>					
					<div><input id="map_search_pane_button" value="Zoek" type="submit" title="Zoek een Linux-hulp"></div>
					<div><input id="map_search_pane_button" value="Leeg velden" type="button" title="Leeg alle zoekvelden" onclick="location.href=\''.MAP_URL.'\';" ></div>
					<div>&nbsp;</div>
				</form>
		';
		
		// Get PHP arguments from address line		
#FIXME: this is not clean
#		$l_query = strstr($_SERVER["REQUEST_URI"], "?");
#		$l_ref = MAP_WP_URL.$l_query;
#		$l_ref = urldecode($l_ref);

		// Add URL's for registration, saving search string and more. Note that
		// some future options are present in HTML-comments
		$l_pane .= '
			<div id="map_search_pane_menu">';
#FIXME: create sane url's, not vunerable to XSS
#				<!--<div id="map_url"><a href="'.MAP_URL.'profiel" target="_self" title="Maak een Linux-hulp profiel aan">Nieuw profiel aanmaken</a></div>
#				<div id="map_url"><a href="'.MAP_URL.'profiel/inloggen" target="_self" title="Bewerk je Linux-hulp profiel">Mijn profiel bewerken</a></div>-->
#				<div id="map_url"><a href="'.MAP_BIG_URL.$l_query.'" target="_self" title="Open de Buurtlinux kaart in een nieuw venster">Open in apart venster</a></div>
#				<div id="map_url" onclick="return toggleCollapse(\'toggle_link\')" title="Klik hier om zoek-URL weer te geven of te verbergen"><a href="#">Link naar deze zoekopdracht</a></div>
#				<div id="toggle_link" style="display: none; margin-top: 15px;"><textarea rows="10" cols="25">'.$l_ref.'</textarea></div>
#				<div id="map_url"><a href="'.MAP_URL.'kaartgebruik" target="_blank" title="Bekijk de mogelijkheden voor het linken naar of inbedden van deze kaart">Deze kaart extern gebruiken</a></div>
		$l_pane .= '	<div id="map_url"><a href="'.ROOT_URL.'" target="_self" title="Ga naar de Buurtlinux website">Bezoek de Buurtlinux website</a></div>
				<div id="map_url"><a href="https://github.com/NLLGG/LocalGuru" target="_self" title="Bekijk de bron-code van deze site">Help mee de bron-code te verbeteren!</a></div>
				</div>
		';
		return $l_pane;
	}
	
	
	// Generates and returns 'address' search criterium input field, either with
	// last known address or empty.
	protected function insert_address()
	{		
		global $search;
		// Check if an address is given
		if( isset( $search['address'] ) && ( $search['address'] != "" ) )
		{
			$l_html = '<input type="text" id="map_search_pane_inputbox" name="address" value="'.$search['address'].'">';
		}
		else
		{
			$l_html = '<input type="text" id="map_search_pane_inputbox" name="address">';
		}
		return $l_html;	
	}
	

	// Generates and returns 'radius' search criterium either with last known
	// radius selected or none.
	protected function insert_radius()
	{
		global $search;
		// Open drop-down box
		$l_html = '<select id="map_search_pane_dropdownbox" name="radius">';
		
		// Get radius variable and get ready to see if and which radius is given
		$l_radius = $search['radius'];
		$l_opts = "";
		$l_found_radius = false;
		
		if( $l_radius == "1000")
		{
			$l_opts .= '<option value="1000" selected="selected">1 km</option>';
			$l_found_radius = true;
		}
		else
		{
			$l_opts .= '<option value="1000">1 km</option>';
		}
		
		if( $l_radius == "5000")
		{
			$l_opts .= '<option value="5000" selected="selected">5 km</option>';
			$l_found_radius = true;
		}
		else
		{
			$l_opts .= '<option value="5000">5 km</option>';
		}
		
		if( $l_radius == "10000")
		{
			$l_opts .= '<option value="10000" selected="selected">10 km</option>';
			$l_found_radius = true;
		}
		else
		{
			$l_opts .= '<option value="10000">10 km</option>';
		}
		
		if( $l_radius == "25000")
		{
			$l_opts .= '<option value="25000" selected="selected">25 km</option>';
			$l_found_radius = true;
		}
		else
		{
			$l_opts .= '<option value="25000">25 km</option>';
		}
		
		if( $l_radius == "50000")
		{
			$l_opts .= '<option value="50000" selected="selected">50 km</option>';
			$l_found_radius = true;
		}		
		else
		{
			$l_opts .= '<option value="50000">50 km</option>';
		}
		
		if( $l_radius == "100000")
		{
			$l_opts .= '<option value="100000" selected="selected">100 km</option>';
			$l_found_radius = true;
		}
		else
		{
			$l_opts .= '<option value="100000">100 km</option>';
		}
		
		if( $l_radius == "500000")
		{
			$l_opts .= '<option value="500000" selected="selected">500 km</option>';
			$l_found_radius = true;
		}
		else
		{
			$l_opts .= '<option value="500000">500 km</option>';
		}
		
		if( $l_radius == "1000000")
		{
			$l_opts .= '<option value="1000000" selected="selected">1000 km</option>';
			$l_found_radius = true;
		}
		else
		{
			$l_opts .= '<option value="1000000">1000 km</option>';
		}
		
		// If no radius was found, check "Geen"-field (which should always be 
		// the first option in the list)
		if( $l_found_radius )
		{
			$l_html .= '<option value="">Geen</option>'.$l_opts;
		}
		else
		{
			$l_html .= '<option value="" selected="selected">Geen</option>'.$l_opts;
		}
		
		// Close selection-box
		$l_html .= '</select>';

		return $l_html;
	}
	

	// Generates and returns 'distributions' search criterium either with last 
	// known distribution(s) selected or none.
	protected function insert_distros()
	{
		global $search;

		// Obtain the available distributions from the database
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_distros_criteria("");		
		
		// If there are any, add them to the search criterium field
		if ( count( $l_criteria ) )
		{	
			$l_html = '<div id="map_search_pane_label" onclick="return toggleCollapse(\'toggle_distros\')" title="Klik hier om de opties in/uit te klappen"><img src="'.MAP_URL.'img/ingeklapt.gif" id="img_toggle_distros" alt="Klik hier om de opties in/uit te klappen">Distributies</div>';
			$l_html .= '<div id="toggle_distros" style="display: none">';

			// Check for active user search criteria
			$l_selection = NULL;
			if( isset( $search['distros']) )
			{
				$l_selection = explode(",", $search['distros'] );
			}

			// Add all available distributions to the search criterium field and
			// check the user-selected ones.
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($l_selection) )
				{
					foreach( $l_selection as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input id="map_search_pane_checkbox" type=checkbox '.$l_checked.' name="distros[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'<br>';
			}
			$l_html .= '<div>&nbsp;</div></div>';
		}		

		return $l_html;
	}
	

	// Generates and returns 'desktop environments' search criterium either with
	// last known desktop environment(s) selected or none.
	protected function insert_desktops()
	{
		global $search;

		// Obtain the available desktop environments from the database
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_desktops_criteria("");		

		// If there are any, add them to the search criterium field
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div id="map_search_pane_label" onclick="return toggleCollapse(\'toggle_desktops\')" title="Klik hier om de opties in/uit te klappen"><img src="'.MAP_URL.'img/ingeklapt.gif" id="img_toggle_desktops" alt="Klik hier om de opties in/uit te klappen">Bureaublad-omgevingen</div>';
			$l_html .= '<div id="toggle_desktops" style="display: none">';

			// Check for active user search criteria
			$l_selection = NULL;
			if( isset( $search['desktops']) )
			{				
				$l_selection = explode(",", $search['desktops'] );
			}

			// Add all available desktop environments to the search criterium 
			// field and check the user-selected ones.
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($l_selection) )
				{
					foreach( $l_selection as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input id="map_search_pane_checkbox" type=checkbox '.$l_checked.' name="desktops[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'<br>';
			}
			$l_html .= '<div>&nbsp;</div></div>';
		}
		
		return $l_html;
	}


	// Generates and returns 'help actions' search criterium either with
	// last known help action(s) selected or none.
	protected function insert_actions()
	{
		// Obtain the available help actions from the database
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_actions_criteria("");
		
		// If there are any, add them to the search criterium field
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div id="map_search_pane_label" onclick="return toggleCollapse(\'toggle_actions\')" title="Klik hier om de opties in/uit te klappen"><img src="'.MAP_URL.'img/ingeklapt.gif" id="img_toggle_actions" alt="Klik hier om de opties in/uit te klappen">Onderwerpen</div>';
			$l_html .= '<div id="toggle_actions" style="display: none">';

			// Check for active user search criteria
			$l_selection = NULL;
			if( isset( $search['actions']) )
			{
				$l_selection = explode(",", $search['actions'] );
			}

			// Add all available help actions to the search criterium field and
			// check the user-selected ones.
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($l_selection) )
				{
					foreach( $l_selection as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input id="map_search_pane_checkbox" type="checkbox" '.$l_checked.' name="actions[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'<br>';
			}
			$l_html .= '<div>&nbsp;</div></div>';
		}
		
		return $l_html;
	}
	

	// Generates and returns 'helper groups' search criterium either with
	// last known helper group(s) selected or none.
	protected function insert_groups()
	{
		global $search;

		// Obtain the available helper groups from the database
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_usergroups_criteria("");
		
		// If there are any, add them to the search criterium field
		if ( count( $l_criteria ) )
		{			
			$l_html =  '<div id="map_search_pane_label" onclick="return toggleCollapse(\'toggle_groups\')" title="Klik hier om de opties in/uit te klappen"><img src="'.MAP_URL.'img/ingeklapt.gif" id="img_toggle_groups" alt="Klik hier om de opties in/uit te klappen">Hulp van</div>';
			$l_html .= '<div id="toggle_groups" style="display: none"><select id="map_search_pane_dropdownbox" name="groups[]">';			
			$l_html .= '<option value="">Wie dan ook</option>';

			// Check for active user search criteria
			$l_selection = NULL;
			if( isset( $search['groups']) ) 
			{
				$l_selection = explode(",", $search['groups'] );
			}

			// Add all available helper groups to the search criterium field and
			// check the user-selected ones.
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($l_selection) )
				{
					foreach( $l_selection as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'selected';
							break 1;
						}
					}
				}
				$l_html .= '<option id="map_search_pane_dropdownbox" '.$l_checked .' value="'.$l_pair['name'].'">'.$l_pair['name'].'</option>';
			}
			$l_html .= '</select><div>&nbsp;</div></div>';
		}		
		return $l_html;
	}


	// Generates and returns 'target groups' search criterium either with
	// last known target group(s) selected or none.	
	protected function insert_targetgrps()
	{
		global $search;
		// Obtain the available target groups from the database
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_targetgroups_criteria("");
		
		// If there are any, add them to the search criterium field
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div id="map_search_pane_label" onclick="return toggleCollapse(\'toggle_targetgrps\')" title="Klik hier om de opties in/uit te klappen"><img src="'.MAP_URL.'img/ingeklapt.gif" id="img_toggle_targetgrps" alt="Klik hier om de opties in/uit te klappen">Hulp aan</div>';
			$l_html .= '<div id="toggle_targetgrps" style="display: none"><select id="map_search_pane_dropdownbox" name="targets[]">';
			$l_html .= '<option value="">Iedereen</option>';

			// Check for active user search criteria
			$l_selection = NULL;
			if( isset( $search['targets']) )
			{
				$l_selection = explode(",", $search['targets'] );
			}

			// Add all available target groups to the search criterium field and
			// check the user-selected ones.
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($l_selection) )
				{
					foreach( $l_selection as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'selected';
							break 1;
						}
					}
				}
				$l_html .= '<option id="map_search_pane_dropdownbox" '.$l_checked .' value="'.$l_pair['name'].'">'.$l_pair['name'].'</option>';
			}			
			$l_html .= '</select><div>&nbsp;</div></div>';
		}		
		return $l_html;
	}
	

	// Generates and returns 'reward types' search criterium either with
	// last known reward type(s) selected or none.	
	protected function insert_rewards()
	{
		global $search;

		// Obtain the available reward types from the database
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_rewards_criteria("");

		// If there are any, add them to the search criterium field		
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div id="map_search_pane_label" onclick="return toggleCollapse(\'toggle_rewards\')" title="Klik hier om de opties in/uit te klappen"><img src="'.MAP_URL.'img/ingeklapt.gif" id="img_toggle_rewards" alt="Klik hier om de opties in/uit te klappen">Beloning</div>';
			$l_html .= '<div id="toggle_rewards" style="display: none">';

			// Check for active user search criteria
			$l_selection = NULL;
			if( isset( $search['rewards']) )
			{
				$l_selection = explode(",", $search['rewards'] );
			}

			// Add all available reward types to the search criterium field and
			// check the user-selected ones.
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($l_selection) )
				{
					foreach( $l_selection as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input id="map_search_pane_checkbox" type=checkbox '.$l_checked.' name="rewards[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'<br>';
			}
			$l_html .= '</div>';
		}		
		return $l_html;
	}
}
?>
