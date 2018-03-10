<?php
/*
=============================================
 Name      : MWS Torrent Viewer v1.1
 Author    : Mehmet Hanoğlu ( MaRZoCHi )
 Site      : https://dle.net.tr/
 License   : MIT License
 Date      : 10.03.2018
=============================================
*/

if ( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

require_once ENGINE_DIR . '/data/torrentviewer.conf.php';

if ( $is_torrent ) {
	$tpl->set( '[torrent]', "" );
	$tpl->set( '[/torrent]', "" );
	$tpl->set_block( "'\\[not-torrent\\](.*?)\\[/not-torrent\\]'si", "" );

	require_once ENGINE_DIR . '/api/api.class.php';

	$caching = $dle_api->load_from_cache( "news_torrent_" . $row['id'], $timeout = 60 * $tor_set['cache_interval_minute'], $type = "array" );

	if ( ! $caching && $tor_set['use_cache'] ) {

		require_once ENGINE_DIR . '/classes/torrent.class.php';
		$torrent = new Torrent ( ROOT_DIR . '/uploads/files/' . $row['onserver'] );

		$caching = array();
		$caching['name'] = $torrent->name();

		$val = "<ul class=\"torrent-announce\">";
		foreach ( $torrent->announce() as $announce ) {
			$val .= "<li>" . $announce[0] . "</li>";
		}
		$val .= "</ul>";
		$caching['announce'] = $val;

		$caching['comment'] = $torrent->comment();
		$caching['pieces'] = $torrent->piece_length();
		$caching['size'] = $torrent->size( 2 );
		$caching['count'] = count( $torrent->content() );
		$caching['hash'] = $torrent->hash_info();

		$val = "";
		$_stats = array(
			'complete'   => 0,
			'downloaded' => 0,
			'incomplete' => 0,
		);
		foreach( $torrent->scrape() as $announce => $stats ) {
			$val .= "<li>" . $announce;
			$val .= "<ul>";
			foreach( $stats as $_name => $data ) {
				if ( ! is_numeric( $data ) ) continue;
				$_stats[ $_name ] += $data;
				$val .= "<li><span class=\"{$_name}\">{$data}</span></li>";
			}
			$val .= "<div style=\"clear: both\"></div></ul>";
			$val .= "</li>";
		}
		$caching['stats'] = "<ul class=\"torrent-stats\">{$val}</ul>";

		$caching['complete'] = $_stats['complete'];
		$caching['downloaded'] = $_stats['downloaded'];
		$caching['incomplete'] = $_stats['incomplete'];

		$caching['magnet'] = $torrent->magnet();
		$caching['comment'] = $torrent->comment();
		$caching['comment'] = $torrent->comment();
		$caching['comment'] = $torrent->comment();

		$caching['content'] = array();
		foreach( $torrent->content() as $_name => $_size ) {
			// Filtreleme için kullanılabilir.
			//$_name = str_replace( "bul", "değiştir", $_name );
			// Son / ve sonrasını gösterir.
			//$_name = strrchr( $_name, "/" );
			$caching['content'][] = "<li>" . $_name . "<span>" . strtoupper( formatsize( $_size ) ) . "</span></li>";
		}

		preg_match_all( "#{torrent:list-([0-9]+)}#is", $tpl->copy_template, $matches );
		for( $x = 0; $x < count( $matches[0] ); $x++ ) {
			$limit = intval( trim( $matches[1][$x] ) );
			$caching[ "list-" . $limit ] = "<ul class=\"torrent-list\">" . implode( "", array_slice( $caching['content'], 0, $limit ) ) . "</ul>";
		}

		$dle_api->save_to_cache( "news_torrent_" . $row['id'], $caching );

		if ( $tor_set['write_to_xfields'] ) {
			$post = $db->super_query("SELECT xfields FROM " . PREFIX . "_post WHERE id = " . $row['news_id'] );
			$post_xfield = array();
			foreach( xfieldsdataload( $post['xfields'] ) as $xf_name => $xf_val ) {
				$post_xfield[] = $xf_name . "|" . $xf_val;
			}
			$post_xfield[] = "complete|" . $caching['complete'];
			$post_xfield[] = "downloaded|" . $caching['downloaded'];
			$post_xfield[] = "incomplete|" . $caching['incomplete'];
			$db->query("UPDATE " . PREFIX . "_post SET xfields = '" . implode( "||", $post_xfield ) . "' WHERE id = " . $row['news_id'] );
		}
	}

	foreach ( $caching as $key => $value ) {
		if ( $key == "content" ) continue;
		if ( $value == "" ) {
			$tpl->set( '[not-torrent:' . $key . ']', "" );
			$tpl->set( '[/not-torrent:' . $key . ']', "" );
			$tpl->set_block( "'\\[torrent:" . $key . "\\](.*?)\\[/torrent:" . $key . "\\]'si", "" );
		} else {
			$tpl->set( '[torrent:' . $key . ']', "" );
			$tpl->set( '[/torrent:' . $key . ']', "" );
			$tpl->set_block( "'\\[not-torrent:" . $key . "\\](.*?)\\[/not-torrent:" . $key . "\\]'si", "" );
		}
		$tpl->set( '{torrent:' . $key . '}', $value );
	}
} else {
	$tpl->set( '[not-torrent]', "" );
	$tpl->set( '[/not-torrent]', "" );
	$tpl->set_block( "'\\[torrent\\](.*?)\\[/torrent\\]'si", "" );
}

?>
