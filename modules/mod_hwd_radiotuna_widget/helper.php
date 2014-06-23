<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_radiotuna_widget
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdRadioTunaWidget extends JObject
{
	/**
	 * Class data
	 * @var array
	 */    
	public $params;
	public $module;

	public function __construct($module, $params)
	{
                // Get data.
                $this->module = $module;                
                $this->params = $params;  
	}

	public function getCode()
	{
                switch($this->params->get('playerSize'))
                {
                        case '200':
                                $this->params->set('playerHeight', 244);
                                break;
                        case '220':
                                $this->params->set('playerHeight', 268);
                                break;
                        default:
                                $this->params->set('playerHeight', 292);
                }
                
                $code = '{"styleSelection0":'.$this->params->get('styleSelection0','96') .',"styleSelection1":'.$this->params->get('styleSelection1','100') .',"styleSelection2":'. $this->params->get('styleSelection2','51') .',"textColor":'. hexdec($this->params->get('textColor','000000')) .',"backgroundColor":'. hexdec($this->params->get('backgroundColor','bdbfbf')) .',"buttonColor":'. hexdec($this->params->get('buttonColor','f63c77')) .',"glowColor":'. hexdec($this->params->get('glowColor','f779a1')) .',"playerSize":'. $this->params->get('width','240') .',"playerType":"style"}';
                
                return $code;
	}
        
	public function getLinkString()
	{
                return '';
                
                switch($this->params->get('styleSelection0','96'))
                {
                        case '96':$code='alternative-rock-radio';break;
                        case '96':$code='Alternative Rock';break;
                        case '116':$code='Avantgarde';break;
                        case '138':$code='Darkwave';break;
                        case '208':$code='Emo';break;
                        case '124':$code='Goth Rock';break;
                        case '76':$code='Grunge';break;
                        case '69':$code='Indie Rock';break;
                        case '95':$code='Lo-Fi';break;
                        case '33':$code='Noise';break;
                        case '184':$code='Oi';break;
                        case '117':$code='Post Rock';break;
                        case '213':$code='Psychobilly';break;
                        case '61':$code='Punk';break;
                        case '123':$code='Ska';break;
                        case '100':$code='Blues';break;
                        case '100':$code='Blues';break;
                        case '274':$code='Chicago Blues';break;
                        case '195':$code='Country Blues';break;
                        case '244':$code='Delta Blues';break;
                        case '228':$code='Electric Blues';break;
                        case '245':$code='Harmonica Blues';break;
                        case '250':$code='Modern Electric Blues';break;
                        case '305':$code='Texas Blues';break;
                        case '7':$code='Chilled';break;
                        case '6':$code='Abstract';break;
                        case '7':$code='Ambient';break;
                        case '13':$code='Dark Ambient';break;
                        case '29':$code='Downtempo';break;
                        case '19':$code='Dub Techno';break;
                        case '153':$code='Ethereal';break;
                        case '22':$code='Experimental';break;
                        case '30':$code='Future Jazz';break;
                        case '17':$code='IDM';break;
                        case '41':$code='Lounge';break;
                        case '90':$code='Modern Classical';break;
                        case '129':$code='New Age';break;
                        case '82':$code='Smooth Jazz';break;
                        case '24':$code='Tribal';break;
                        case '31':$code='Trip Hop';break;
                        case '178':$code='Classical';break;
                        case '178':$code='Classical';break;
                        case '253':$code='Baroque';break;
                        case '107':$code='Contemporary';break;
                        case '165':$code='Modern';break;
                        case '233':$code='Renaissance';break;
                        case '230':$code='Romantic';break;
                        case '192':$code='Country &amp; Folk';break;
                        case '235':$code='Bluegrass';break;
                        case '192':$code='Country';break;
                        case '57':$code='Country Rock';break;
                        case '252':$code='Doo Wop';break;
                        case '87':$code='Folk';break;
                        case '56':$code='Folk Rock';break;
                        case '114':$code='Rock &amp; Roll';break;
                        case '226':$code='Rockabilly';break;
                        case '238':$code='Southern Rock';break;
                        case '192':$code='Country';break;
                        case '10':$code='Dance';break;
                        case '21':$code='Bass Music';break;
                        case '21':$code='Drum n Bass';break;
                        case '19':$code='Dub Techno';break;
                        case '154':$code='Dubstep';break;
                        case '207':$code='Grime';break;
                        case '32':$code='Jungle';break;
                        case '8':$code='Breaks &amp; Electro';break;
                        case '39':$code='Big Beat';break;
                        case '12':$code='Breakbeat';break;
                        case '27':$code='Breakcore';break;
                        case '8':$code='Breaks';break;
                        case '28':$code='Electro';break;
                        case '73':$code='Freestyle';break;
                        case '62':$code='Hard Dance &amp; Techno';break;
                        case '23':$code='Acid';break;
                        case '93':$code='Gabber';break;
                        case '53':$code='Happy Hardcore';break;
                        case '62':$code='Hard House';break;
                        case '40':$code='Hardcore';break;
                        case '180':$code='Hardstyle';break;
                        case '179':$code='Jumpstyle';break;
                        case '20':$code='Minimal';break;
                        case '83':$code='Speedcore';break;
                        case '3':$code='Techno';break;
                        case '5':$code='-House &amp; Garage';break;
                        case '2':$code='Deep House';break;
                        case '42':$code='Euro House';break;
                        case '14':$code='Garage House';break;
                        case '5':$code='House';break;
                        case '25':$code='Latin';break;
                        case '10':$code='Trance';break;
                        case '50':$code='Progressive House';break;
                        case '4':$code='Tech House';break;
                        case '10':$code='Trance';break;
                        case '10':$code='Trance';break;
                        case '38':$code='Goa Trance';break;
                        case '45':$code='Hard Trance';break;
                        case '49':$code='Progressive Trance';break;
                        case '118':$code='Psy-Trance';break;
                        case '84':$code='Electronica';break;
                        case '143':$code='Chiptune';break;
                        case '84':$code='EBM';break;
                        case '22':$code='Experimental';break;
                        case '17':$code='IDM';break;
                        case '36':$code='Industrial';break;
                        case '15':$code='Leftfield';break;
                        case '33':$code='Noise';break;
                        case '63':$code='Funk &amp; Soul';break;
                        case '99':$code='Afrobeat';break;
                        case '51':$code='Disco';break;
                        case '252':$code='Doo Wop';break;
                        case '63':$code='Funk';break;
                        case '202':$code='Gospel';break;
                        case '145':$code='Jazz-Funk';break;
                        case '166':$code='Neo Soul';break;
                        case '194':$code='P.Funk';break;
                        case '125':$code='RnB/Swing';break;
                        case '101':$code='Soul';break;
                        case '127':$code='Soul-Jazz';break;   
                        case '220':$code='Swingbeat';break; 
                        case '141':$code='Hard Rock &amp; Metal';break;
                        case '81':$code='Black Metal';break;
                        case '188':$code='Death Metal';break;
                        case '168':$code='Doom Metal';break;
                        case '177':$code='Grindcore';break;
                        case '141':$code='Hard Rock';break;
                        case '142':$code='Heavy Metal';break;
                        case '193':$code='Nu Metal';break;
                        case '236':$code='Speed Metal';break;
                        case '219':$code='Thrash';break;
                        case '283':$code='Viking Metal';break;
                        case '82':$code='Jazz';break;   
                        case '66':$code='Fusion &amp; World';break; 
                        case '48':$code='Acid Jazz';break; 
                        case '99':$code='Afrobeat';break; 
                        case '121':$code='Afro-Cuban Jazz';break; 
                        case '66':$code='Fusion';break; 
                        case '145':$code='Jazz-Funk';break; 
                        case '201':$code='Jazz-Rock';break; 
                        case '113':$code='Latin Jazz';break; 
                        case '82':$code='Smooth Jazz';break; 
                        case '127':$code='Soul-Jazz';break; 
                        case '46':$code='Modern';break; 
                        case '46':$code='Contemporary Jazz';break; 
                        case '110':$code='Free Improvisation';break; 
                        case '109':$code='Free Jazz';break; 
                        case '223':$code='Modal';break; 
                        case '211':$code='Post Bop';break; 
                        case '171':$code='Space-Age';break; 
                        case '218':$code='Traditional';break; 
                        case '60':$code='Big Band';break; 
                        case '216':$code='Bop';break; 
                        case '218':$code='Cool Jazz';break; 
                        case '163':$code='Dixieland';break; 
                        case '72':$code='Easy Listening';break; 
                        case '59':$code='Hard Bop';break; 
                        case '162':$code='Ragtime';break;
                        case '164':$code='Swing';break;
                        case '47':$code='Latin';break;
                        case '197':$code='Afro-Cuban';break;
                        case '197':$code='Afro-Cuban';break;
                        case '241':$code='Bolero';break;
                        case '298':$code='Charanga';break;
                        case '187':$code='Cubano';break;
                        case '186':$code='Descarga';break;
                        case '122':$code='Mambo';break;
                        case '151':$code='Rumba';break;
                        case '47':$code='Salsa';break;
                        case '288':$code='Son';break;
                        case '281':$code='Caribbean';break;
                        case '280':$code='Bachata';break;
                        case '148':$code='Merengue';break;
                        case '281':$code='Reggaeton';break;
                        case '210':$code='Zouk';break;
                        case '285':$code='Central American';break;
                        case '285':$code='Mariachi';break;
                        case '296':$code='Marimba';break;
                        case '258':$code='Ranchera';break;
                        case '282':$code='Tejano';break;
                        case '198':$code='North American';break;
                        case '198':$code='Boogaloo';break;
                        case '292':$code='Pachanga';break;
                        case '120':$code='South American';break;
                        case '112':$code='Bossa Nova';break;
                        case '67':$code='Bossanova';break;
                        case '150':$code='Cumbia';break;
                        case '269':$code='Lambada';break;
                        case '248':$code='MPB';break;
                        case '120':$code='Samba';break;
                        case '182':$code='Tango';break;
                        case '312':$code='Vallenato';break;
                        case '227':$code='Spanish';break;
                        case '227':$code='Flamenco';break;
                        case '82':$code='Pop';break;
                        case '119':$code='Ballad';break;
                        case '119':$code='Brit Pop';break;
                        case '133':$code='Europop';break;
                        case '55':$code='Hi NRG';break;
                        case '54':$code='Italo-Disco';break;
                        case '176':$code='J-pop';break;
                        case '103':$code='New Wave';break;
                        case '78':$code='Pop Rap';break;
                        case '77':$code='Pop Rock';break;
                        case '172':$code='Power Pop';break;
                        case '102':$code='Reggae-Pop';break;
                        case '71':$code='Synth-pop';break;
                        case '167':$code='Vocal';break;
                        case '64':$code='Rap &amp; Hip Hop';break;	
                        case '169':$code='Bass Music';break;
                        case '75':$code='Conscious';break;
                        case '257':$code='Crunk';break;
                        case '94':$code='Cut-up/DJ';break;
                        case '174':$code='Gangsta';break;
                        case '207':$code='Grime';break;
                        case '64':$code='Hip Hop';break;
                        case '232':$code='Horrorcore';break;
                        case '104':$code='Instrumental';break;
                        case '78':$code='Pop Rap';break;
                        case '135':$code='Ragga HipHop';break;
                        case '264':$code='Screw';break;
                        case '190':$code='Thug Rap';break;
                        case '11':$code='Reggae &amp; Dub';break;
                        case '136':$code='Dancehall';break;
                        case '11':$code='Dub';break;
                        case '254':$code='Lovers Rock';break;
                        case '302':$code='Mento';break;
                        case '189':$code='Ragga';break;
                        case '97':$code='Reggae';break;
                        case '284':$code='Reggae Gospel';break;
                        case '102':$code='Reggae-Pop';break;
                        case '255':$code='Rocksteady';break;
                        case '98':$code='Roots Reggae';break;
                        case '123':$code='Ska';break;
                        case '181':$code='Soca';break;
                        case '58':$code='Rock';break;
                        case '58':$code='Classic Rock';break;
                        case '158':$code='Art &amp; Prog Rock';break;
                        case '158':$code='Art Rock';break;
                        case '92':$code='Garage Rock';break;
                        case '105':$code='Glam';break;
                        case '201':$code='Jazz-Rock';break;
                        case '155':$code='Krautrock';break;
                        case '159':$code='Prog Rock';break;
                        case '44':$code='Psychedelic Rock';break;
                        case '160':$code='Symphonic Rock';break;
                        case '114':$code='Rock &amp; Roll';break;
                        case '114':$code='Rock &amp; Roll';break;
                        case '100':$code='Blues Rock';break;
                        case '57':$code='Country Rock';break;
                        case '252':$code='Doo Wop';break;
                        case '226':$code='Rockabilly';break;
                        case '238':$code='Southern Rock';break;
                        case '126':$code='Soft Rock';break;
                        case '126':$code='Soft Rock';break;
                        case '131':$code='Acoustic';break;
                        case '56':$code='Folk Rock';break;
                        case '147':$code='Other';break;
                        case '133':$code='European';break;
                        case '156':$code='Chanson';break;
                        case '133':$code='Europop';break;
                        case '55':$code='Hi NRG';break;
                        case '54':$code='Italo-Disco';break;
                        case '128':$code='Schlager';break;
                        case '210':$code='Zouk';break;
                        case '74':$code='Non-Music';break;
                        case '37':$code='Comedy';break;
                        case '108':$code='Religious';break;
                        case '265':$code='Sermon';break;
                        case '74':$code='Spoken Word';break;
                        case '225':$code='Technical';break;
                        case '147':$code='Novelty';break;
                        case '147':$code='Novelty';break;
                        case '80':$code='Parody';break;
                        case '275':$code='Stage &amp; Screen';break;
                        case '290':$code='Bollywood';break;
                        case '256':$code='Music Hall';break;
                        case '275':$code='Musical';break;
                        case '137':$code='Score';break;
                        case '85':$code='Soundtrack';break;
                        case '205':$code='Theme';break;            
                        default:$code='alternative-rock-radio';break;
                }
                
                return $code;
	}
}