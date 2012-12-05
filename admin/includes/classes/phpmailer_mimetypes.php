<?php
$aMimeTypes = array(
	"gz"  => "application/gzip",
	"hqx" => "application/mac-binhex40",
	"cpt" => "application/mac-compactpro",
	"oda" => "application/oda",
	"ai"  => "application/postscript",
	"eps" => "application/postscript",
	"ps"  => "application/postscript",
	"ppt" => "application/powerpoint",
	"rtf" => "application/rtf",
	"bcpio" => "application/x-bcpio",
	"vcd" => "application/x-cdlink",
	"Z"   => "application/x-compress",
	"cpio"  => "application/x-cpio",
	"csh" => "application/x-csh",
	"dcr" => "application/x-director",
	"dir" => "application/x-director",
	"dxr" => "application/x-director",
	"dvi" => "application/x-dvi",
	"gtar"  => "application/x-gtar",
	"hdf" => "application/x-hdf",
	"latex" => "application/x-latex",
	"mif" => "application/x-mif",
	"nc"  => "application/x-netcdf",
	"cdf" => "application/x-netcdf",
	"sh"  => "application/x-sh",
	"shar"  => "application/x-shar",
	"swf" => "application/x-shockwave-flash",
	"sit" => "application/x-stuffit",
	"sv4cpio" => "application/x-sv4cpio",
	"sv4crc"  => "application/x-sv4crc",
	"tar" => "application/x-tar",
	"tcl" => "application/x-tcl",
	"tex" => "application/x-tex",
	"man" => "application/x-troff-man",
	"me"  => "application/x-troff-me",
	"ms"  => "application/x-troff-ms",
	"ustar" => "application/x-ustar",
	"src" => "application/x-wais-source",
	"zip" => "application/zip",
	"au"  => "audio/basic",
	"snd" => "audio/basic",
	"mid" => "audio/midi",
	"midi"  => "audio/midi",
	"kar" => "audio/midi",
	"mpga"  => "audio/mpeg",
	"mp2" => "audio/mpeg",
	"mp3" => "audio/mpeg",
	"aif" => "audio/x-aiff",
	"aiff"  => "audio/x-aiff",
	"aifc"  => "audio/x-aiff",

	"ram" => "audio/x-pn-realaudio",
	"rpm" => "audio/x-pn-realaudio-plugin",
	"ra"  => "audio/x-pn-realaudio",
	"rm"  => "audio/x-pn-realaudio",

	"wav" => "audio/x-wav",
	"pdb" => "chemical/x-pdb",
	"xyz" => "chemical/x-pdb",
	"gif" => "image/gif",
	"ief" => "image/ief",
	"bmp" => "image/bmp",
	"jpeg"  => "image/jpeg",
	"jpg" => "image/jpeg",
	"jpe" => "image/jpeg",
	"png" => "image/png",
	"tiff"  => "image/tiff",
	"tif" => "image/tiff",
	"ras" => "image/x-cmu-raster",
	"pnm" => "image/x-portable-anymap",
	"pbm" => "image/x-portable-bitmap",
	"pgm" => "image/x-portable-graymap",
	"ppm" => "image/x-portable-pixmap",
	"rgb" => "image/x-rgb",
	"xbm" => "image/x-xbitmap",
	"xpm" => "image/x-xpixmap",
	"xwd" => "image/x-xwindowdump",
	"css" => "text/css",
	"html"  => "text/html",
	"htm" => "text/html",
	"txt" => "text/plain",
	"rtx" => "text/richtext",
	"tsv" => "text/tab-separated-values",
	"etx" => "text/x-setext",
	"sgm" => "text/x-sgml",
	"sgml"  => "text/x-sgml",

	"mpeg"  => "video/mpeg",
	"mpg" => "video/mpeg",
	"mpe" => "video/mpeg",
	"mpv" => "video/mpeg",
	"vbs" => "video/mpeg",
	"mpegv" => "video/mpeg",
	"mpa" => "video/mpeg",

	"qt"  => "video/quicktime",
	"mov" => "video/quicktime",
	"avi" => "video/x-msvideo",
	"movie" => "video/x-sgi-movie",
	"ice" => "x-conference/x-cooltalk",
	"wrl" => "x-world/x-vrml",
	"vrml"  => "x-world/x-vrml",

	/* MHTML (html x posta elettr.) */
	"mhtml" => "message/rfc822",
	"mht" => "message/rfc822",
	"eml" => "message/rfc822",
	"nws" => "message/rfc822",

	/* ms-excel */
	"xls" => "application/vnd.ms-excel",
	"xlc" => "application/vnd.ms-excel",
	"xlt" => "application/vnd.ms-excel",
	"xlm" => "application/vnd.ms-excel",
	"xld" => "application/vnd.ms-excel",
	"xla" => "application/vnd.ms-excel",
	"xlw" => "application/vnd.ms-excel",
	"xll" => "application/vnd.ms-excel",

	"xlk" => "application/x-msexcel",
	"csv" => "application/x-msexcel",
	"xlb" => "application/x-msexcel",
	"slk" => "application/x-msexcel",
	"dif" => "application/x-msexcel",
	"slk" => "application/x-msexcel",
	"iqy" => "application/x-msexcel-iqy", /* excel query file */

	/* shockwave-flash */
	"swf" => "application/x-shockwave-flash",

	/* ms-access */
	"mdb" => "application/vnd.ms-access",
	"mda" => "application/vnd.ms-access",
	"mde" => "application/vnd.ms-access",

	"mad" => "application/x-msaccess-mad",
	"mar" => "application/x-msaccess-mar",
	"mam" => "application/x-msaccess-mam",
	"maf" => "application/x-msaccess-maf",
	"maq" => "application/x-msaccess-maq",
	"mat" => "application/x-msaccess-mat",
	"mdz" => "application/x-msaccess-mdz",
	"mdn" => "application/x-msaccess-mdn",

	/* ms-ppoint */
	"ppt" => "application/vnd.ms-powerpoint",
	"pot" => "application/vnd.ms-powerpoint",
	"ppa" => "application/vnd.ms-powerpoint",
	"pps" => "application/vnd.ms-powerpoint",
	"pwz" => "application/vnd.ms-powerpoint",

	/* ms-word */
	"doc" => "application/msword",
	"wbk" => "application/x-msword", /* file di backup */

	/* ms-write */
	"wri" => "application/x-mswrite",

	/* altre compressioni*/
	"tgz"   => "application/x-compressed",

	/* compressioni non mime-standard*/
	"arj"   => "application/x-arj-compressed",
	"taz"   => "application/x-taz-compressed",
	"arc"   => "application/x-arc-compressed",
	"lzh"   => "application/x-lzh-compressed",

	/* adobe acrobat */
	"pdf"   => "application/pdf",
	"fdf"   => "application/vnd.fdf",

	/* AT&T DjVu */
	"djvu"    => "image/x-djvu",

	/* eseguibili-binari */
	"exe"   => "application/x-msdownload",
	"bin"   => "application/octet-stream",
	"com"   => "application/octet-stream",
	"bat"   => "application/octet-stream",
	"pl"    => "application/octet-stream"
);
?>