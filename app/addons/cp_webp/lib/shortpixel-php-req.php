<?php

require_once("ShortPixel/Settings.php");
require_once("ShortPixel/Lock.php");
require_once("ShortPixel/SPLog.php");
require_once("ShortPixel/SPCache.php");

require_once("ShortPixel/Persister.php");
require_once("ShortPixel/persist/TextPersister.php");
require_once("ShortPixel/persist/ExifPersister.php");
require_once("ShortPixel/persist/PNGMetadataExtractor.php");
require_once("ShortPixel/persist/PNGReader.php");

require_once("ShortPixel/notify/ProgressNotifier.php");
require_once("ShortPixel/notify/ProgressNotifierMemcache.php");
require_once("ShortPixel/notify/ProgressNotifierFileQ.php");

require_once("ShortPixel/Commander.php");
require_once("ShortPixel/Client.php");
require_once("ShortPixel/Exception.php");
require_once("ShortPixel/Source.php");
require_once("ShortPixel/Result.php");
require_once("ShortPixel.php");

