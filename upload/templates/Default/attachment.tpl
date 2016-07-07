[allow-download]

	[torrent]
		<div class="torrent">
			<div class="torrentname"><div class="torrentnamex">Dosya Adı:</div><div class="torrentname-text">{torrent:name}</div></div>
			<div class="torrentname"><div class="torrentnamex">Dosya Boyutu:</div><div class="torrentname-text">{torrent:size}</div></div>
			[torrent:comment]<div class="torrentname"><div class="torrentnamex">Dosya Yorumu:</div><div class="torrentname-text">{torrent:comment}</div></div>[/torrent:comment]
			[not-torrent:comment]<div class="torrentname"><div class="torrentnamex">Dosya Yorumu:</div><div class="torrentname-text">Yorum Yok</div></div>[/not-torrent:comment]
			<div class="torrentname"><div class="torrentnamex">Dosya Hash:</div><div class="torrentname-text">{torrent:hash}</div></div>
			<div class="torrentname">
				<div class="torrentnamex">Dosya Sayısı:</div>
				<div class="torrentname-stext">{torrent:count}</div>
				<div class="torrentnamex">Parça Sayısı:</div>
				<div class="torrentname-stext">{torrent:pieces}</div>
				<div class="torrentnamex">İndirilme:</div>
				<div class="torrentname-stext">{count}</div>
			</div>
			<div class="torrentname doc22">Torrent ile indereceğiniz dosyalar</div>
			<div class="torrentdoct">
				{torrent:list-10}
			</div>
			[torrent:announce]
			<div class="torrentname doc22">Anons Adresleri</div>
			<div class="torrentdoct">
				{torrent:announce}
			</div>
			[/torrent:announce]
			<div class="torrentname doc22">İstatistikler</div>
			<div class="torrentdoct torrentstats">
				{torrent:stats}
			</div>
			<a class="attachment" href="{link}" ><i class="fa fa-download"></i>Torrent Dosyasını İndir [{size}]</a>
			<a class="attachment" href="{torrent:magnet}" ><i class="fa fa-download"></i>Magnet Link</a>
		</div>
	[/torrent]

	[not-torrent]
		<span class="attachment">
			<a href="{link}" >{name}</a> [count] [{size}] (İndirilme: {count})[/count]
		</span>
	[/not-torrent]

[/allow-download]
[not-allow-download]<span class="attachment">Sunucudan dosya indirme izniniz yok</span>[/not-allow-download]