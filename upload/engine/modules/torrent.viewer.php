<?php

if ( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

// Ayarlar
$tor_set['use_cache'] = '1';

if ( $is_torrent ) {
	$tpl->set( '[torrent]', "" );
	$tpl->set( '[/torrent]', "" );
	$tpl->set_block( "'\\[not-torrent\\](.*?)\\[/not-torrent\\]'si", "" );
	require_once ENGINE_DIR . '/api/api.class.php';
	$caching = $dle_api->load_from_cache( "news_torrent_" . $row['id'], $timeout = 60*60*30, $type = "array" );
	if ( ! $caching && $tor_set['use_cache'] ) {
		require_once ENGINE_DIR . '/classes/torrent.class.php';
		$torrent = new Torrent ( ROOT_DIR . '/uploads/files/' . $row['onserver'] );

		preg_match_all( "#{torrent:([a-z0-9\-]+)}#is", $tpl->copy_template, $matches );

		$caching = array();
		foreach( $matches[1] as $need ) {
			if ( $need == "name" ) $val = $torrent->name();
			else if ( $need == "announce" ) {
				$val = "<ul class=\"torrent-announce\">";
				foreach ( $torrent->announce() as $announce ) {
					$val .= "<li>" . $announce[0] . "</li>";
				}
				$val .= "</ul>";
			}
			else if ( $need == "comment" ) $val = $torrent->comment();
			else if ( $need == "pieces" ) $val = $torrent->piece_length();
			else if ( $need == "size" ) $val = $torrent->size( 2 );
			else if ( $need == "count" ) $val = count( $torrent->content() );
			else if ( $need == "hash" ) $val = $torrent->hash_info();
			else if ( $need == "stats" ) {
				$val = "";
				foreach( $torrent->scrape() as $announce => $stats ) {
					$val .= "<li>" . $announce;
					$val .= "<ul>";
					foreach( $stats as $_name => $data ) {
						$val .= "<li><span class=\"{$_name}\">{$data}</span></li>";
					}
					$val .= "<div style=\"clear: both\"></div></ul>";
					$val .= "</li>";
				}
				if ( $val != "" ) $val = "<ul class=\"torrent-stats\">{$val}</ul>";
			}
			else if ( $need == "magnet" ) $val = $torrent->magnet();
			else if ( substr( $need, 0, 5 ) == "list-" ) {
				$_tmp = explode( "-", $need );
				$limit = intval( $_tmp[1] );
				$val = "";
				$x = 0;
				foreach( $torrent->content() as $_name => $_size ) {
					// Filtreleme için kullanılabilir.
					//$_name = str_replace( "bul", "değiştir", $_name );
					// Son / ve sonrasını gösterir.
					//$_name = strrchr( $_name, "/" );
					$_size = strtoupper( formatsize( $_size ) );
					$val .= "<li>{$_name}<span>{$_size}</span></li>";
					$x++;
					if ( $x >= $limit ) break;
				}
				if ( $val != "" ) $val = "<ul class=\"torrent-list\">{$val}</ul>";
			}
			else $val = "";
			$caching[ $need ] = $val;
			// $torrent->is_private()
			// $torrent->encoding;
		}
		$dle_api->save_to_cache( "news_torrent_" . $row['id'], $caching );
	}
	foreach ( $caching as $key => $value ) {
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