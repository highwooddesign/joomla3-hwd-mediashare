<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareControllerSample extends JControllerLegacy 
{
        /**
	 * Method to install sample data
	 * @return	void
	 */
	public function install()
	{
                $model = $this->getModel('Dashboard', 'hwdMediaShareModel');
                $nummedia = $model->getCountMedia();
                $numcategories = $model->getCountCategories();
                $numalbums = $model->getCountAlbums();
                $numgroups = $model->getCountGroups();
                $numchannels = $model->getCountChannels();
                $numplaylists = $model->getCountPlaylists();
                
                if ($nummedia > 0 || $numcategories > 0 || $numalbums > 0 || $numgroups > 0 || $numchannels > 0 || $numplaylists > 0)
                {
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_SAMPLE_DATA_EXISTS') );
                        JFactory::getApplication()->redirect( 'index.php?option=com_hwdmediashare' );
                }
                
                $db = JFactory::getDBO();
                
                // Install sample album data
$query = <<<SQL
INSERT INTO `#__hwdms_albums` (`id`, `thumbnail_ext_id`, `key`, `title`, `alias`, `description`, `likes`, `dislikes`, `status`, `published`, `featured`, `checked_out`, `checked_out_time`, `access`, `params`, `ordering`, `created_user_id`, `created_user_id_alias`, `created`, `publish_up`, `publish_down`, `modified_user_id`, `modified`, `hits`, `language`) VALUES
(28, 0, '0f89f7b5a6475021e312d73e7c7f1e0f', 'Nature', 'nature', '<p>\r\nAliquet tincidunt nec auctor auctor pulvinar etiam magnis purus porttitor ultrices integer cursus mus mauris, est. Montes. Ut, a, augue etiam pellentesque integer. Porta proin? Tincidunt! Arcu, nascetur, odio? \r\n</p>\r\n<p>\r\nPulvinar nisi, aliquam mauris porttitor sed, aenean hac mauris aliquam, penatibus magnis, mid magna, enim! Turpis facilisis scelerisque? Tincidunt! A eu lorem, lectus augue pulvinar hac lectus augue adipiscing in! Phasellus eros adipiscing dictumst et a lectus lacus ac magna auctor nunc, porttitor nunc auctor, quis lundium nisi amet amet? \r\n</p>\r\n<p>\r\nDiam amet vut pulvinar, turpis velit nunc, ac, enim augue enim ac! Rhoncus, integer! Nisi nunc, hac lundium? Sed cum. Ridiculus tincidunt lacus aliquet, turpis ut, elit in! Mattis. Adipiscing ac nascetur mus ultrices ut dignissim, magna ultricies! Tincidunt scelerisque.\r\n</p>', 376, 37, 1, 1, 1, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 4726, '*'),
(29, 0, '47f376dc3a88ddc4b3135a38a2a6f3b2', 'Sports', 'sports', '<p>\r\nQuis? Sit phasellus, dignissim a, enim integer. Tincidunt in tristique integer? Mus et tincidunt eros in aliquam parturient tristique, mus proin vel, integer, lorem in dapibus tincidunt lectus nisi. Nunc. Vel adipiscing vel, dictumst ultricies et est dignissim?\r\n</p>\r\n<p>\r\nEt, platea lundium? Pid mid pellentesque. Ut, in ac, sed, mid, augue! Lundium sed et pid, porta aliquam integer nec tortor! Lacus velit sit nunc turpis, rhoncus ac mid et? Scelerisque duis urna hac est dignissim, in augue habitasse ac! \r\n</p>\r\n<p>\r\nDictumst enim in etiam tristique, sed scelerisque urna cursus in augue integer etiam, tincidunt lacus enim! Lectus diam purus dis ultricies. Porttitor. Amet, aliquam in augue porta lacus, porttitor. Dolor dis! Elit, cras sagittis! Urna, purus natoque tristique pid dapibus ultrices.\r\n</p>', 268, 78, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 5276, '*'),
(30, 0, '9835cd076f3d9ccf7b797438d9fb33aa', 'Business', 'business', '<p>\r\nFacilisis elementum pid odio habitasse! Magna platea dapibus turpis. Adipiscing. Sit, sed facilisis magnis. Porttitor sed porttitor? Purus tristique turpis lorem et ac pid tempor in etiam integer vel? Ut vel. Diam facilisis est porttitor! \r\n</p>\r\n<p>\r\nNunc sit sed! Augue! Platea ultrices, enim eu sed parturient nisi! Augue turpis dictumst? Nisi cras ac cum purus ultrices mid porta, cras porttitor ultricies proin eu turpis sagittis, non magna etiam phasellus augue pulvinar et vel sit amet, velit augue diam pid montes enim elementum tortor porttitor odio dignissim porttitor, porta cum lacus risus habitasse platea lorem lorem in? Etiam et integer, vut. \r\n</p>\r\n<p>\r\nMassa magna natoque turpis, phasellus tincidunt? Lorem pellentesque! Eros dapibus mauris mattis dictumst dapibus, tempor tristique urna est. Massa eu.\r\n</p>', 563, 149, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 4987, '*');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Install sample group data
$query = <<<SQL
INSERT INTO `#__hwdms_groups` (`id`, `thumbnail_ext_id`, `key`, `title`, `alias`, `description`, `likes`, `dislikes`, `status`, `published`, `featured`, `checked_out`, `checked_out_time`, `access`, `params`, `ordering`, `created_user_id`, `created_user_id_alias`, `created`, `publish_up`, `publish_down`, `modified_user_id`, `modified`, `hits`, `language`) VALUES
(28, 0, '19e46a96a763f1429f8ebae9257620c7', 'Nature Enthusiasts', 'nature-enthusiasts', '<p>\r\nSit facilisis turpis, habitasse odio vut? Egestas, augue elementum urna ac vut mattis in! Dolor odio dapibus, lacus sagittis, urna, eros, duis nisi, ac ultrices adipiscing ultrices massa ultricies velit ac. Phasellus etiam? \r\n</p>\r\n<p>\r\nQuis ac, et lundium etiam, lundium. Sit in tincidunt adipiscing, dis? Eu pid dis, a nunc. Non? Amet mauris cum. Amet turpis, enim pulvinar augue porttitor. Enim integer eros nunc, pulvinar placerat, turpis tristique est tincidunt? Et porttitor, urna lectus dignissim pulvinar nisi lectus nascetur tortor! Non etiam scelerisque enim? \r\n</p>\r\n</p>\r\nVel, et nec mauris, lorem ut. Massa, mid nisi in quis adipiscing. Aenean? Massa ut! Porttitor turpis mid integer integer egestas rhoncus. Ac mauris tristique dolor, sed a lorem, enim, adipiscing placerat augue? Et in ac.\r\n</p>', 375, 203, 1, 1, 1, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 2768, '*'),
(29, 0, 'de94c4fe1e333ab961ad01ec67a67417', 'Sport Fans', 'sport-fans', '<p>\r\nPhasellus urna diam nunc? Ac porttitor massa adipiscing, risus ac rhoncus? Rhoncus ac duis! Ultricies magna augue natoque ac pellentesque elit, in ut. Aenean auctor velit augue, sed placerat, tincidunt lectus ac eros quis augue? \r\n</p>\r\n<p>\r\nMagnis nisi a scelerisque. Adipiscing? Mid, egestas sed, eu vut facilisis arcu. Amet porta! Dictumst urna ultricies massa. Augue ultricies ac adipiscing odio lundium, magnis porttitor massa! Odio a egestas. Diam sit habitasse amet pulvinar aliquam et adipiscing scelerisque? Tortor. Tristique pulvinar tempor etiam, mid amet duis et?\r\n</p>\r\n<p>\r\nInteger. Odio quis ut cum ultricies! Est nunc vut mattis odio tincidunt magna urna? Magnis? In, pulvinar dictumst cursus cras ut, lacus? Natoque parturient risus lectus! Aliquam amet elementum dolor, sit tincidunt amet nisi natoque porttitor.\r\n</p>', 79, 28, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 1628, '*'),
(30, 0, '223d09680e0751aa6f489cba37951283', 'Business Moguls', 'business-moguls', '<p>Elit sit nascetur nec nec sit rhoncus integer diam? Habitasse augue magnis dignissim elementum risus. Odio porttitor nunc montes, placerat mattis risus elementum. Habitasse turpis aenean. Lundium, vut turpis cras magnis habitasse duis hac et, nec velit nec pulvinar! \r\n</p>\r\n<p>\r\nA magnis elit a! Tempor augue eu elementum diam hac, tempor porta elit elit? Montes scelerisque et lorem a magna, ac enim amet, augue, duis! Lorem tincidunt arcu cras, vel placerat lectus, parturient scelerisque pulvinar, augue nascetur penatibus magna elementum, sed pid cum cras? Magna ac sit. Sagittis cum lacus enim lundium. Elementum vel! Facilisis enim ac lundium et, odio nisi ac eu velit, platea, est risus habitasse urna, a, porta habitasse, porttitor tincidunt sit dolor adipiscing ultrices. Aliquam ac.\r\n</p>', 783, 241, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 4964, '*');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Install sample playlist data
$query = <<<SQL
INSERT INTO `#__hwdms_playlists` (`id`, `thumbnail_ext_id`, `key`, `title`, `alias`, `description`, `likes`, `dislikes`, `status`, `published`, `featured`, `checked_out`, `checked_out_time`, `access`, `params`, `ordering`, `created_user_id`, `created_user_id_alias`, `created`, `publish_up`, `publish_down`, `modified_user_id`, `modified`, `hits`, `language`) VALUES
(28, 0, 'bb48a83ef5d8e64d20d66da56ffb42c7', 'Nature', 'nature', '<p>Mattis, scelerisque placerat. Ridiculus penatibus, lacus urna nec placerat sit ac eu sed amet, cras phasellus diam aenean turpis? Sed, sagittis dolor nec integer tristique in turpis turpis egestas, egestas augue, lacus sit vel eros?</p>\r\n\r\n<p>Penatibus duis porta porta? Habitasse nec! Dignissim ridiculus a, arcu egestas a, porttitor placerat augue penatibus, quis phasellus, placerat? Ut ut integer aenean amet, in dapibus ultricies pid enim aliquet ac, hac scelerisque porta, montes dapibus turpis elementum odio, arcu massa porttitor facilisis tincidunt, cum, pellentesque turpis sit?</p>\r\n\r\n<p>Mid et, nisi enim ut nunc ultrices mauris, integer ultricies urna rhoncus? Mus eu sagittis ut! Odio urna! Etiam sit dignissim, ut massa, risus vut placerat turpis, cursus, et, rhoncus turpis ridiculus, integer auctor augue turpis.</p>', 213, 93, 1, 1, 1, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 10274, '*'),
(29, 0, 'd07c07327d53d1b480d7806bdeb68834', 'Business', 'business', '<p>\r\nTurpis in mid montes, placerat a eros aenean nunc adipiscing? Natoque. Eu amet augue? Penatibus tincidunt diam sit aliquet, placerat, diam cras egestas placerat arcu urna platea placerat aenean, tincidunt elementum rhoncus? \r\n</p>\r\n<p>\r\nSed mus, dapibus nisi sed aliquet rhoncus turpis dignissim quis mid. Vut diam ac! Ac, odio mauris? Massa mauris? Elit! Nisi quis mattis porta risus cum aenean! \r\n</p>\r\n<p>\r\nTincidunt nascetur penatibus eros porttitor, nascetur? Parturient enim, elit tincidunt tristique ac nec a cras augue, nisi porttitor parturient montes ut eros, pulvinar, placerat mauris montes ac pulvinar est sociis a natoque, sit tincidunt dolor magna? Duis habitasse scelerisque natoque porta, amet natoque integer sociis porta ultricies natoque enim pulvinar dignissim? Vel. A integer, urna tristique, vel dis vel cursus.\r\n</p>', 470, 101, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 9528, '*'),
(30, 0, '08f98abdcd416bfea04d1486a58ebf4d', 'Sports', 'sports', '<p>\r\nTurpis tortor tortor, dictumst cras adipiscing augue nec porttitor risus! Parturient? Tempor? Lectus phasellus enim scelerisque! Quis? Sociis integer, adipiscing enim, tortor odio ultrices nascetur amet odio mid natoque amet turpis a! \r\n</p>\r\n<p>\r\nAc urna nisi pid duis platea lectus, purus turpis quis? Tincidunt nec ac, phasellus nec placerat, adipiscing elementum penatibus enim amet rhoncus proin ac enim diam aliquam, in purus amet mattis egestas facilisis scelerisque eros. Ultrices ac adipiscing pulvinar! Etiam ultrices. Augue pid sit amet. Velit sed, nisi, sit magna, magna! \r\n</p>\r\n<p>\r\nDapibus sit natoque pellentesque aliquam est rhoncus. Rhoncus et? Urna nec? Aliquam. Augue augue odio odio augue magna est augue enim penatibus egestas amet. Magna scelerisque ac mid mauris nascetur tincidunt lectus, arcu in sociis, ultricies.\r\n</p>', 428, 72, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '{"metadesc":"","metakey":"","robots":"","author":"","rights":""}', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00', 6934, '*');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Install sample activity data
$query = <<<SQL
INSERT INTO `#__hwdms_activities` (`id`, `activity_type`, `element_type`, `element_id`, `reply_id`, `title`, `alias`, `description`, `likes`, `dislikes`, `status`, `published`, `featured`, `checked_out`, `checked_out_time`, `access`, `params`, `ordering`, `created_user_id`, `created_user_id_alias`, `created`, `publish_up`, `publish_down`, `modified_user_id`, `modified`, `hits`, `language`) VALUES
(28, 3, 2, 28, 0, '', '', '', 36, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(29, 3, 2, 29, 0, '', '', '', 73, 9, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(30, 3, 2, 30, 0, '', '', '', 59, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(31, 4, 3, 28, 0, '', '', '', 17, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(32, 7, 3, 28, 0, '', '', '', 95, 3, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(33, 4, 3, 29, 0, '', '', '', 76, 4, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(34, 7, 3, 29, 0, '', '', '', 32, 8, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(35, 4, 3, 30, 0, '', '', '', 68, 12, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(36, 7, 3, 30, 0, '', '', '', 47, 3, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(37, 5, 4, 28, 0, '', '', '', 17, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(38, 5, 4, 29, 0, '', '', '', 94, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
(39, 5, 4, 30, 0, '', '', '', 45, 5, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Now we download the sample data pack, and save it to the /tmp folder
                $url  = 'http://hwdmediashare.co.uk/media/sample.zip';
                $path = JPATH_SITE.'/tmp/sample.zip';
                $dest = JPATH_SITE.'/media/com_hwdmediashare/files/';

                $fp = fopen($path, 'w');

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_FILE, $fp);

                $data = curl_exec($ch);

                curl_close($ch);
                fclose($fp);

                // Now we extract the sample data
                jimport( 'joomla.filesystem.archive' );
                JArchive::extract($path, $dest);
                
                // If successfull, we inject the rest of the sample data
                // Install sample media data
$query = <<<SQL
INSERT INTO `#__hwdms_media` (`id`, `asset_id`, `ext_id`, `key`, `title`, `alias`, `description`, `type`, `source`, `storage`, `duration`, `streamer`, `file`, `embed_code`, `thumbnail`, `location`, `likes`, `dislikes`, `status`, `published`, `featured`, `checked_out`, `checked_out_time`, `access`, `params`, `ordering`, `created_user_id`, `created_user_id_alias`, `created`, `publish_up`, `publish_down`, `modified_user_id`, `modified`, `hits`, `language`) VALUES
(28, 0, 29, '71c51ac4569155797973416383547d6d', 'Football', 'football', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae felis tortor. Cras ut risus erat. Mauris at ipsum eget augue pharetra congue. Proin feugiat auctor bibendum. Mauris auctor risus mi. Aenean purus diam, feugiat quis placerat a, elementum vel justo. Vestibulum nec augue id odio bibendum pulvinar ut sed nibh. Donec vitae nunc sit amet mi ullamcorper tempus. Donec bibendum risus eget felis mattis a suscipit lectus auctor. Vivamus blandit velit at arcu porttitor pellentesque. Praesent sit amet purus sit amet nisi elementum mattis at quis felis. Sed sit amet diam in nunc luctus feugiat. In volutpat dolor ac elit faucibus pulvinar porta augue luctus.', 1, '', '', '', '', '', '', '', 'Newcastle, UK', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:01:33', '2012-02-22 20:01:33', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(29, 0, 29, '4af276a4caa0113c470cfe45f4391ada', 'Snowboarder', 'snowboarder', 'Vestibulum id tristique purus. Donec tortor orci, porttitor sed mattis ac, dapibus dapibus nisi. Morbi bibendum augue nibh, quis varius nunc. Donec feugiat urna id orci tincidunt eget dignissim mauris molestie. Proin eget magna ut dui euismod blandit. Pellentesque mattis ullamcorper ipsum, sodales tempus mi tristique quis. Donec vel erat tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas lorem diam, placerat quis suscipit ut, rhoncus venenatis nunc.', 1, '', '', '', '', '', '', '', 'Austin, Texas', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:01:30', '2012-02-22 20:01:30', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(30, 0, 29, '3380361fe2253f48031c76f2012c038c', 'Golf Clubs', 'golf-clubs', 'Mauris lacinia magna id ligula tempus non aliquam elit pellentesque. Donec accumsan bibendum eros, at sollicitudin elit faucibus id. Mauris quam nisl, scelerisque id elementum auctor, mollis quis felis. Mauris ac sapien at lorem rhoncus semper. Duis accumsan nulla non mauris fringilla porttitor. Mauris vel erat eu massa ultrices mollis et in velit. Maecenas malesuada libero ac leo mattis quis molestie dolor vulputate. Donec ac eros eu ligula molestie imperdiet sed nec risus.', 1, '', '', '', '', '', '', '', 'Sydney, Australia', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:01:27', '2012-02-22 20:01:27', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(31, 0, 29, 'e145c80bc66a233daf2533323d22d569', 'Football Stadium', 'football-stadium', 'Maecenas tempus augue eget eros dapibus ac porttitor urna ultricies. Maecenas eu lectus risus. Nunc ullamcorper adipiscing fringilla. Phasellus ultricies, erat non vehicula facilisis, nisl augue consequat massa, vitae semper urna nibh vulputate enim. Proin et tincidunt purus. Integer dui lorem, venenatis vel rhoncus in, adipiscing ut elit. Aenean rutrum porta velit vel suscipit. Vestibulum luctus sollicitudin tellus, in condimentum ligula accumsan at. Aenean dapibus elementum leo nec dignissim.', 1, '', '', '', '', '', '', '', 'London, UK', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:01:24', '2012-02-22 20:01:24', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(32, 0, 29, '8a8146d5ba575bae48a7dc57a954566f', 'Rose', 'rose', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam lacinia purus accumsan augue eleifend rhoncus. Proin nec nisi massa. Nullam eget elit at nisi euismod varius in sed sem. Nam mollis varius mauris at sodales. Ut ut blandit nisi. Praesent porttitor purus at purus scelerisque egestas.', 1, '', '', '', '', '', '', '', 'Paris, France', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:01:05', '2012-02-22 20:01:05', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(33, 0, 29, 'df5ba91451755df89bedd5039b60be71', 'Ocean', 'ocean', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse volutpat varius enim, vitae pharetra lacus luctus non. In elementum dui imperdiet augue egestas sed tempor est placerat. Praesent leo diam, feugiat vitae congue eu, sodales ac quam. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer ac mattis purus. Aliquam erat volutpat. Donec aliquam elit eu mauris rhoncus hendrerit at eu sem. ', 1, '', '', '', '', '', '', '', 'Berlin, Germany', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:59', '2012-02-22 20:00:59', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(34, 0, 29, '68f34f58fda15f18d5260a6832002a54', 'Flower', 'flower', 'Sed vehicula, libero eu fringilla vestibulum, urna neque accumsan nulla, vel facilisis elit tortor sit amet nibh. Mauris volutpat, leo sit amet placerat venenatis, erat odio viverra urna, eu gravida dui turpis vitae quam. Vivamus sed massa metus, in blandit felis. Suspendisse non erat risus. Donec volutpat est eu metus tempor mollis. Nunc consequat consequat dolor, quis volutpat eros sagittis a. In sit amet purus ipsum, eu suscipit tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque interdum, eros vel sagittis tristique, ipsum eros faucibus nunc, vitae aliquet metus ipsum ut nibh. Integer nisi enim, venenatis sed condimentum sed, ultrices nec magna. Nullam mattis urna id nibh congue egestas sagittis quam viverra. Sed quis lacus et tellus semper porttitor at at justo. Integer a risus sapien, eget auctor urna. Ut consectetur vulputate nisi, et placerat tortor sodales vulputate. ', 1, '', '', '', '', '', '', '', 'Rome, Italy', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:53', '2012-02-22 20:00:53', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(35, 0, 29, 'b03df5a90bc0135d5ed4e27583b983d9', 'Cloudy', 'cloudy', 'Aenean pretium arcu in mi convallis imperdiet. Aliquam in ligula ut ipsum scelerisque gravida in sed justo. Nunc augue tellus, vehicula a molestie vitae, molestie sed magna. Aenean posuere molestie nulla quis facilisis. Sed sed nunc purus, ut mattis nisl. Donec ornare, metus in ullamcorper pulvinar, metus nisi tristique risus, a auctor risus est in mi. Aenean accumsan velit vel diam vestibulum id fermentum elit posuere. Ut hendrerit sapien sit amet dui dapibus mattis. Mauris iaculis dapibus elit, sit amet euismod purus ullamcorper in. Duis id consequat tortor. Proin posuere libero at orci iaculis sed tempus diam adipiscing. Suspendisse enim nunc, rutrum vitae luctus eget, condimentum id velit. Duis est ligula, tincidunt non varius eu, hendrerit eget eros. Quisque a ligula ac tortor hendrerit egestas vel at velit. Morbi commodo dapibus porttitor.', 1, '', '', '', '', '', '', '', 'New York, USA', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:47', '2012-02-22 20:00:47', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(36, 0, 29, 'cbeac3edb0ea85c9ad0741aef2076634', 'Cliffs', 'cliffs', 'Sed vitae arcu sem, eu molestie massa. Nam eu adipiscing justo. Donec in lacus quam, et tincidunt tellus. Aliquam at tristique quam. Ut euismod aliquam ante vitae lobortis. Maecenas sed tortor id magna consequat tincidunt. Donec vel risus diam, eget hendrerit leo. Sed et diam dui, vel ultrices ipsum. Cras sed lectus eu turpis interdum venenatis et quis arcu. Aliquam ultricies mauris vitae sem imperdiet lacinia. Proin viverra eros sed turpis consequat ac viverra erat pellentesque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam quis sapien eu est vestibulum ultrices.', 1, '', '', '', '', '', '', '', 'Mexico City, Mexico', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:42', '2012-02-22 20:00:42', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(37, 0, 29, '4da450763b77487eea7cfb68ced75e3d', 'Butterfly', 'butterfly', 'Quisque eget tristique erat. Quisque eu arcu quis augue porttitor elementum id commodo diam. Donec malesuada est sed neque semper sollicitudin. Nulla auctor nulla vitae mi consectetur fermentum. Ut accumsan congue est, sit amet bibendum augue sodales nec. Etiam id dui eget tellus ornare facilisis quis ut lorem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse vel neque vel tortor molestie faucibus. Quisque volutpat, mauris eu sagittis feugiat, urna magna egestas dui, ac scelerisque tortor neque in mi. Donec pretium gravida nibh in imperdiet. Morbi a risus purus. Curabitur velit metus, faucibus sit amet facilisis sed, ornare ut augue.', 1, '', '', '', '', '', '', '', 'Aukland, New Zealand', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:39', '2012-02-22 20:00:39', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(38, 0, 29, '47a3719edbaa9969f44415c226b37bf3', 'Mouse', 'mouse', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi eu est nunc. Mauris non tortor neque, et aliquam sem. Sed ante purus, adipiscing non blandit eget, gravida fringilla dolor. Donec augue massa, consectetur sit amet commodo egestas, egestas in turpis. Proin consequat velit eu nulla ullamcorper semper. Fusce at purus eget erat rhoncus auctor nec non purus. Curabitur auctor accumsan tortor ac aliquet. In elementum eleifend lorem, vel lacinia magna auctor at. Suspendisse mattis adipiscing ante ultricies blandit. Nullam vulputate justo gravida nulla volutpat ultricies. Cras sit amet magna ut eros vestibulum ultricies. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed nec elit nec odio posuere aliquam sit amet at mi. Duis rhoncus hendrerit nunc ac interdum. Nunc vehicula turpis sed augue ultrices ac hendrerit augue vehicula. Nam commodo quam ac eros vehicula scelerisque.', 1, '', '', '', '', '', '', '', 'Santiago, Chile ', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:21', '2012-02-22 20:00:21', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(39, 0, 29, '5c6aa1ef4d7e804cb82026aaeb8db736', 'Grouping', 'grouping', 'Nunc facilisis mi fermentum metus vestibulum euismod porta ipsum dignissim. Aliquam erat volutpat. Etiam nec nisl id felis elementum facilisis. Aenean congue pulvinar luctus. Donec ornare, enim nec accumsan viverra, quam lectus fringilla urna, a imperdiet enim risus euismod diam. Morbi auctor sapien ac est ornare faucibus. Aliquam vel ante nunc, ut consequat enim. Pellentesque quis felis in ipsum tincidunt ultrices. Aenean elit diam, eleifend sit amet dignissim a, ultricies vel metus. Etiam venenatis vulputate quam, nec placerat sapien facilisis nec. Donec ut enim non magna laoreet hendrerit. Integer lectus odio, imperdiet id pharetra quis, suscipit in arcu. Quisque ac nisl eget metus fringilla aliquet at vitae est. Cras ac orci justo, et blandit nunc.', 1, '', '', '', '', '', '', '', 'Dublin, Ireland', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:19', '2012-02-22 20:00:19', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(40, 0, 29, 'ce34853be2b836c40e56cefac17f3293', 'Glasses', 'glasses', 'Sed ipsum sapien, viverra at sagittis sit amet, vehicula in sapien. Aenean et eros sapien. Nunc turpis nisl, faucibus eu gravida vel, lobortis eget libero. Nam sapien nulla, bibendum a blandit vel, semper nec diam. Pellentesque dignissim, libero non bibendum sollicitudin, ante lectus mollis libero, vitae rutrum eros libero vel tortor. Duis scelerisque, leo sit amet facilisis volutpat, lorem nisi sagittis sapien, eget venenatis dolor metus nec nisl. Phasellus elementum dui id enim mollis quis pulvinar nibh aliquam. Donec eget mattis mauris. Integer eros massa, sodales sed auctor vitae, bibendum ut arcu. Proin nisi felis, condimentum nec euismod in, gravida eu lacus.', 1, '', '', '', '', '', '', '', 'Istanbul, Turkey', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:16', '2012-02-22 20:00:16', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(41, 0, 29, 'b9048c1b6216458537112432f64fd2ec', 'Executive', 'executive', 'Praesent eget est eros, sed pellentesque metus. Vestibulum dictum turpis neque. Mauris enim quam, pharetra sed tristique at, facilisis vitae odio. Sed quis nunc sit amet quam adipiscing tincidunt. Donec eleifend, libero ac consectetur venenatis, ante sapien semper augue, sit amet tincidunt leo massa non nibh. Morbi turpis mauris, vehicula vel elementum a, rutrum quis elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce faucibus diam ac dolor tincidunt non consectetur quam rhoncus. Aenean pharetra augue eget risus aliquet vel egestas lorem fermentum. Praesent eu molestie augue. Vivamus iaculis scelerisque mauris non placerat. Maecenas rutrum, tortor pulvinar feugiat facilisis, nulla augue placerat orci, sed tempus quam risus nec turpis. Nam et mauris elementum libero interdum imperdiet in vitae odio. Nunc eros felis, lobortis in adipiscing tincidunt, ornare tristique lacus.', 1, '', '', '', '', '', '', '', 'Moscow, Russia', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:13', '2012-02-22 20:00:13', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(42, 0, 29, 'e3610d299736bb2b263865d97136f44d', 'Buildings', 'buildings', 'Ut lacus tortor, commodo sit amet mollis sit amet, semper vel purus. Pellentesque in massa nec arcu volutpat consequat a at magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam nisl odio, commodo nec eleifend eu, elementum sed nulla. Nullam leo eros, posuere egestas tincidunt adipiscing, mollis sit amet tellus. Proin ullamcorper tristique tristique. Nulla molestie mi tortor. Praesent aliquet bibendum dolor, ut gravida est accumsan vel. Donec vestibulum sodales nibh, non bibendum ligula mollis vitae. Pellentesque pulvinar erat commodo nulla viverra rhoncus. Quisque adipiscing velit ut est feugiat semper non rutrum diam. Vivamus iaculis lectus quam, at pellentesque nisl.', 1, '', '', '', '', '', '', '', 'Cape Town, South Africa', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:10', '2012-02-22 20:00:10', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(43, 0, 29, '298a71ddad51d7471242c0d9e19006ca', 'Binder', 'binder', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque quis eros sed mi placerat scelerisque id eget dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean vitae sapien metus, fringilla euismod quam. Maecenas turpis lectus, luctus id vehicula at, imperdiet eu risus. Curabitur accumsan, felis non eleifend posuere, velit risus auctor dui, at ullamcorper velit mauris in est. Duis in odio tortor, ut aliquet leo. Fusce in velit ligula, et blandit quam. Fusce eleifend mi vitae nunc congue sodales. Morbi pellentesque imperdiet dolor, id convallis tortor imperdiet in. Vestibulum facilisis justo a nisl hendrerit vehicula. In ligula tellus, pharetra eu consectetur id, lacinia eget felis. Donec vitae quam quis lacus varius pretium id eget dolor. Duis vitae felis a enim tempor suscipit eget sed justo. Cras a lacus urna, ac consectetur mauris.', 1, '', '', '', '', '', '', '', 'New Delhi, India', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:00:06', '2012-02-22 20:00:06', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(44, 0, 29, '5aba1df5ae729fb7c4e4f7b6be4854ed', 'Tennis Balls', 'tennis-balls', 'Nam hendrerit, mi vel tempor tempor, lorem metus sodales turpis, et lobortis risus metus sit amet sem. Cras eleifend viverra orci in fringilla. Quisque a lacus vitae lacus dictum elementum non nec ipsum. Sed eu eros velit. Phasellus ut turpis quis erat elementum congue ac in tortor. Donec sollicitudin congue sapien non dapibus. Nulla facilisi. Aenean nisi lorem, mollis in ornare ut, tincidunt porttitor justo. Quisque bibendum fermentum lectus, et sollicitudin nunc aliquet elementum. Cras pulvinar, lectus et tempor dapibus, sem ligula venenatis risus, ut lacinia neque orci porta nibh. Nam velit sem, dictum a porttitor ac, porta vel mi. Phasellus vitae elit massa. Vestibulum commodo consequat aliquet. Nulla facilisis, nunc eu viverra pharetra, nisi erat mattis mi, ac imperdiet mi ante non sem.', 1, '', '', '', '', '', '', '', 'Beijing, China', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:01:36', '2012-02-22 20:01:36', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*'),
(45, 0, 29, 'c4a4edfa160cacc342d75014e8799dd5', 'Tennis Court', 'tennis-court', 'Donec id risus justo. Donec gravida erat ac leo rutrum fermentum. Donec ac condimentum magna. Donec congue lectus ut felis consequat consectetur. Donec rutrum lorem sed felis pretium in sollicitudin diam auctor. Sed id porta sapien. Donec euismod tellus non risus congue porttitor.', 1, '', '', '', '', '', '', '', 'Uppsala, Sweden', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 1, '', 0, 42, '', '2012-02-22 20:01:41', '2012-02-22 20:01:41', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '*');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Install sample file data
$query = <<<SQL
INSERT INTO `#__hwdms_files` (`id`, `element_type`, `element_id`, `file_type`, `basename`, `ext`, `size`, `checked`) VALUES
(28, 1, 43, 1, '4767a2aa3fdc307d0f51663920a6ec25', 'jpg', 67846, '2012-01-25 12:00:00'),
(29, 1, 42, 1, '8f8bbfca08f2d4b09f610803c8f67c94', 'jpg', 217827, '2012-01-25 12:00:00'),
(30, 1, 41, 1, 'ee6c39d69188d8a5916eef3f67e34aab', 'jpg', 135569, '2012-01-25 12:00:00'),
(31, 1, 40, 1, 'd58085956fbdc00721f8b064ab3e7f96', 'jpg', 116953, '2012-01-25 12:00:00'),
(32, 1, 39, 1, '20ee599db4b6f25bf4543b5c2d9735d6', 'jpg', 69505, '2012-01-25 12:00:00'),
(33, 1, 38, 1, '4a78bbd9f3a8402efae12ea01fb9bcf7', 'jpg', 68789, '2012-01-25 12:00:00'),
(34, 1, 37, 1, '44a872a39b4d2699665e3cdacb3ee1b2', 'jpg', 72559, '2012-01-25 12:00:00'),
(35, 1, 36, 1, 'f01d7fe5b34f2c9e9a512b3eee8369b0', 'jpg', 136929, '2012-01-25 12:00:00'),
(36, 1, 35, 1, '9c3e7d35f2bb3ad3cb8a9584ee58dd51', 'jpg', 79528, '2012-01-25 12:00:00'),
(37, 1, 34, 1, '4b0f6a867a6e548b306018c86a4f478b', 'jpg', 101570, '2012-01-25 12:00:00'),
(38, 1, 33, 1, 'ed8e2901456df11b6cb0605621d3fb44', 'jpg', 263275, '2012-01-25 12:00:00'),
(39, 1, 32, 1, '4b69ea72ed9b7352f4121276d895f6f9', 'jpg', 81308, '2012-01-25 12:00:00'),
(40, 1, 31, 1, '47251280943d9240f258045dffa11aa8', 'jpg', 180787, '2012-01-25 12:00:00'),
(41, 1, 30, 1, '0c1455a8b4d8be04ed03b2158b716834', 'jpg', 77547, '2012-01-25 12:00:00'),
(42, 1, 29, 1, 'c00108dcee0217b0ccf26f4e06befe5c', 'jpg', 108813, '2012-01-25 12:00:00'),
(43, 1, 28, 1, '932f3f856e39481ede53485c90d84606', 'jpg', 303244, '2012-01-25 12:00:00'),
(44, 1, 44, 1, 'abe13b5b248fb65f6f43022edacd6c45', 'jpg', 74197, '2012-01-25 12:00:00'),
(45, 1, 45, 1, '4b049e8403ec68e441c81ee47a638f36', 'jpg', 116968, '2012-01-25 12:00:00');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Install sample album map data
$query = <<<SQL
INSERT INTO `#__hwdms_album_map` (`id`, `media_id`, `album_id`, `created_user_id`, `created`) VALUES
(1, 37, 28, 0, '0000-00-00 00:00:00'),
(2, 36, 28, 0, '0000-00-00 00:00:00'),
(3, 35, 28, 0, '0000-00-00 00:00:00'),
(4, 34, 28, 0, '0000-00-00 00:00:00'),
(5, 33, 28, 0, '0000-00-00 00:00:00'),
(6, 32, 28, 0, '0000-00-00 00:00:00'),
(7, 31, 29, 0, '0000-00-00 00:00:00'),
(8, 30, 29, 0, '0000-00-00 00:00:00'),
(9, 29, 29, 0, '0000-00-00 00:00:00'),
(10, 28, 29, 0, '0000-00-00 00:00:00'),
(11, 44, 29, 0, '0000-00-00 00:00:00'),
(12, 45, 29, 0, '0000-00-00 00:00:00'),
(13, 43, 30, 0, '0000-00-00 00:00:00'),
(14, 42, 30, 0, '0000-00-00 00:00:00'),
(15, 41, 30, 0, '0000-00-00 00:00:00'),
(16, 40, 30, 0, '0000-00-00 00:00:00'),
(17, 39, 30, 0, '0000-00-00 00:00:00'),
(18, 38, 30, 0, '0000-00-00 00:00:00');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Install sample group map data
$query = <<<SQL
INSERT INTO `#__hwdms_group_map` (`id`, `media_id`, `group_id`, `created_user_id`, `created`) VALUES
(1, 37, 28, 0, '0000-00-00 00:00:00'),
(2, 36, 28, 0, '0000-00-00 00:00:00'),
(3, 35, 28, 0, '0000-00-00 00:00:00'),
(4, 34, 28, 0, '0000-00-00 00:00:00'),
(5, 33, 28, 0, '0000-00-00 00:00:00'),
(6, 32, 28, 0, '0000-00-00 00:00:00'),
(7, 31, 29, 0, '0000-00-00 00:00:00'),
(8, 30, 29, 0, '0000-00-00 00:00:00'),
(9, 29, 29, 0, '0000-00-00 00:00:00'),
(10, 28, 29, 0, '0000-00-00 00:00:00'),
(11, 44, 29, 0, '0000-00-00 00:00:00'),
(12, 45, 29, 0, '0000-00-00 00:00:00'),
(13, 43, 30, 0, '0000-00-00 00:00:00'),
(14, 42, 30, 0, '0000-00-00 00:00:00'),
(15, 41, 30, 0, '0000-00-00 00:00:00'),
(16, 40, 30, 0, '0000-00-00 00:00:00'),
(17, 39, 30, 0, '0000-00-00 00:00:00'),
(18, 38, 30, 0, '0000-00-00 00:00:00');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }

                // Install sample playlist map data
$query = <<<SQL
INSERT INTO `#__hwdms_playlist_map` (`id`, `playlist_id`, `media_id`, `ordering`, `created_user_id`, `created`) VALUES
(1, 28, 37, 6, 0, '0000-00-00 00:00:00'),
(2, 28, 36, 5, 0, '0000-00-00 00:00:00'),
(3, 28, 35, 4, 0, '0000-00-00 00:00:00'),
(4, 28, 34, 3, 0, '0000-00-00 00:00:00'),
(5, 28, 33, 2, 0, '0000-00-00 00:00:00'),
(6, 28, 32, 1, 0, '0000-00-00 00:00:00'),
(7, 29, 43, 6, 0, '0000-00-00 00:00:00'),
(8, 29, 42, 5, 0, '0000-00-00 00:00:00'),
(9, 29, 41, 4, 0, '0000-00-00 00:00:00'),
(10, 29, 40, 3, 0, '0000-00-00 00:00:00'),
(11, 29, 39, 2, 0, '0000-00-00 00:00:00'),
(12, 29, 38, 1, 0, '0000-00-00 00:00:00'),
(13, 30, 31, 6, 0, '0000-00-00 00:00:00'),
(14, 30, 30, 5, 0, '0000-00-00 00:00:00'),
(15, 30, 29, 4, 0, '0000-00-00 00:00:00'),
(16, 30, 28, 3, 0, '0000-00-00 00:00:00'),
(17, 30, 44, 2, 0, '0000-00-00 00:00:00'),
(18, 30, 45, 1, 0, '0000-00-00 00:00:00');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }             
                    
                JTable::addIncludePath(JPATH_SITE.'/libraries/joomla/database/table');

                // Setup sample nature category
                $nature = JTable::getInstance('Category', 'JTable');
                $nature->setLocation($data['parent_id'], 'last-child');
                
                $data = array();
                $data['id'] = 0;
                $data['parent_id'] = 1;
                $data['level'] = 1;
                $data['extension'] = 'com_hwdmediashare';
                $data['title'] = 'Nature';
                $data['alias'] = 'nature';
                $data['description'] = "<p>\r\nMattis, scelerisque placerat. Ridiculus penatibus, lacus urna nec placerat sit ac eu sed amet, cras phasellus diam aenean turpis? Sed, sagittis dolor nec integer tristique in turpis turpis egestas, egestas augue, lacus sit vel eros? \r\n</p>\r\n<p>\r\nPenatibus duis porta porta? Habitasse nec! Dignissim ridiculus a, arcu egestas a, porttitor placerat augue penatibus, quis phasellus, placerat? Ut ut integer aenean amet, in dapibus ultricies pid enim aliquet ac, hac scelerisque porta, montes dapibus turpis elementum odio, arcu massa porttitor facilisis tincidunt, cum, pellentesque turpis sit?\r\n</p> \r\n<p>Mid et, nisi enim ut nunc ultrices mauris, integer ultricies urna rhoncus? Mus eu sagittis ut! Odio urna! Etiam sit dignissim, ut massa, risus vut placerat turpis, cursus, et, rhoncus turpis ridiculus, integer auctor augue turpis.\r\n</p>";
                $data['published'] = 1;
                $data['access'] = 1;
                $data['created_user_id'] = 0;
                $data['language'] = '*';               
                // Bind the data.
                if (!$nature->bind($data)) {
			$this->setError($nature->getError());
			return false;
		}
                // Store the data.
		if (!$nature->store()) {
			$this->setError($nature->getError());
			return false;
		}    

                // Install sample category map data
$query = <<<SQL
INSERT INTO `#__hwdms_category_map` (`id`, `element_type`, `element_id`, `category_id`, `created_user_id`, `created`) VALUES
(1, 1, 37, $nature->id, 0, '0000-00-00 00:00:00'),
(2, 1, 36, $nature->id, 0, '0000-00-00 00:00:00'),
(3, 1, 35, $nature->id, 0, '0000-00-00 00:00:00'),
(4, 1, 34, $nature->id, 0, '0000-00-00 00:00:00'),
(5, 1, 33, $nature->id, 0, '0000-00-00 00:00:00'),
(6, 1, 32, $nature->id, 0, '0000-00-00 00:00:00');
SQL;

                $db->setQuery($query);
                $db->query();
                echo $db->getErrorMsg(); 
                
                // Setup sample business category               
                $business = JTable::getInstance('Category', 'JTable');
                $business->setLocation($data['parent_id'], 'last-child');  
                
                $data = array();
                $data['id'] = 0;
                $data['parent_id'] = 1;
                $data['level'] = 1;
                $data['extension'] = 'com_hwdmediashare';
                $data['title'] = 'Business';
                $data['alias'] = 'business';
                $data['description'] = "<p>\r\nTurpis in mid montes, placerat a eros aenean nunc adipiscing? Natoque. Eu amet augue? Penatibus tincidunt diam sit aliquet, placerat, diam cras egestas placerat arcu urna platea placerat aenean, tincidunt elementum rhoncus? \r\n</p>\r\n<p>\r\nSed mus, dapibus nisi sed aliquet rhoncus turpis dignissim quis mid. Vut diam ac! Ac, odio mauris? Massa mauris? Elit! Nisi quis mattis porta risus cum aenean! \r\n</p>\r\n<p>\r\nTincidunt nascetur penatibus eros porttitor, nascetur? Parturient enim, elit tincidunt tristique ac nec a cras augue, nisi porttitor parturient montes ut eros, pulvinar, placerat mauris montes ac pulvinar est sociis a natoque, sit tincidunt dolor magna? Duis habitasse scelerisque natoque porta, amet natoque integer sociis porta ultricies natoque enim pulvinar dignissim? Vel. A integer, urna tristique, vel dis vel cursus.\r\n</p>";
                $data['published'] = 1;
                $data['access'] = 1;
                $data['created_user_id'] = 0;
                $data['language'] = '*';               
                // Bind the data.
                if (!$business->bind($data)) {
			$this->setError($business->getError());
			return false;
		}
                // Store the data.
		if (!$business->store()) {
			$this->setError($business->getError());
			return false;
		} 
                
                // Install sample category map data
$query = <<<SQL
INSERT INTO `#__hwdms_category_map` (`id`, `element_type`, `element_id`, `category_id`, `created_user_id`, `created`) VALUES
(7, 1, 38, $business->id, 0, '0000-00-00 00:00:00'),
(8, 1, 39, $business->id, 0, '0000-00-00 00:00:00'),
(9, 1, 40, $business->id, 0, '0000-00-00 00:00:00'),
(10, 1, 41, $business->id, 0, '0000-00-00 00:00:00'),
(11, 1, 42, $business->id, 0, '0000-00-00 00:00:00'),
(12, 1, 43, $business->id, 0, '0000-00-00 00:00:00');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }
                
                // Setup sample sports category               
                $sports = JTable::getInstance('Category', 'JTable');
                $sports->setLocation($data['parent_id'], 'last-child');  
                
                $data = array();
                $data['id'] = 0;
                $data['parent_id'] = 1;
                $data['level'] = 1;
                $data['extension'] = 'com_hwdmediashare';
                $data['title'] = 'Sports';
                $data['alias'] = 'sports';
                $data['description'] = "<p>\r\nTurpis tortor tortor, dictumst cras adipiscing augue nec porttitor risus! Parturient? Tempor? Lectus phasellus enim scelerisque! Quis? Sociis integer, adipiscing enim, tortor odio ultrices nascetur amet odio mid natoque amet turpis a! \r\n</p>\r\n<p>\r\nAc urna nisi pid duis platea lectus, purus turpis quis? Tincidunt nec ac, phasellus nec placerat, adipiscing elementum penatibus enim amet rhoncus proin ac enim diam aliquam, in purus amet mattis egestas facilisis scelerisque eros. Ultrices ac adipiscing pulvinar! Etiam ultrices. Augue pid sit amet. Velit sed, nisi, sit magna, magna! \r\n</p>\r\n<p>\r\nDapibus sit natoque pellentesque aliquam est rhoncus. Rhoncus et? Urna nec? Aliquam. Augue augue odio odio augue magna est augue enim penatibus egestas amet. Magna scelerisque ac mid mauris nascetur tincidunt lectus, arcu in sociis, ultricies.\r\n</p>";
                $data['published'] = 1;
                $data['access'] = 1;
                $data['created_user_id'] = 0;
                $data['language'] = '*';               
                // Bind the data.
                if (!$sports->bind($data)) {
			$this->setError($sports->getError());
			return false;
		}
                // Store the data.
		if (!$sports->store()) {
			$this->setError($sports->getError());
			return false;
		} 
                
                // Install sample category map data
$query = <<<SQL
INSERT INTO `#__hwdms_category_map` (`id`, `element_type`, `element_id`, `category_id`, `created_user_id`, `created`) VALUES
(13, 1, 28, $sports->id, 0, '0000-00-00 00:00:00'),
(14, 1, 29, $sports->id, 0, '0000-00-00 00:00:00'),
(15, 1, 30, $sports->id, 0, '0000-00-00 00:00:00'),
(16, 1, 31, $sports->id, 0, '0000-00-00 00:00:00'),
(17, 1, 44, $sports->id, 0, '0000-00-00 00:00:00'),
(18, 1, 45, $sports->id, 0, '0000-00-00 00:00:00');
SQL;
                $db->setQuery($query);
                try
                {
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                }
                
                JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_SAMPLE_DATA_SUCCESS') );
                JFactory::getApplication()->redirect( 'index.php?option=com_hwdmediashare' );
        }
}
