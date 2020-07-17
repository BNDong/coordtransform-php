<?php

require_once dirname(dirname(__FILE__)) . '/coordtransform.php';

/**
 * 与 wandergis/coordtransform 版本对比
 */
echo "<script src='https://cdn.jsdelivr.net/gh/wandergis/coordtransform@master/index.js'></script>";

$lng = 101.661135;
$lat = 34.440429;

/**
 * GCJ02 转 BD09
 */
$gtb = Coordtransform::gcj02ToBd09($lng, $lat);
var_dump($gtb);
$js = <<< EOF
    console.log(coordtransform.gcj02tobd09($lng, $lat));
EOF;
script($js);

/**
 * GCJ02 转 WGS84
 */
$gtw = Coordtransform::gcj02ToWgs84($lng, $lat);
var_dump($gtw);
$js = <<< EOF
    console.log(coordtransform.gcj02towgs84($lng, $lat));
EOF;
script($js);

/**
 * WGS84 转 GCJ02
 */
$wtg = Coordtransform::wgs84ToGcj02($lng, $lat);
var_dump($wtg);
$js = <<< EOF
    console.log(coordtransform.wgs84togcj02($lng, $lat));
EOF;
script($js);

/**
 * WGS84 转 BD09
 */
$wtb = Coordtransform::wgs84ToBd09($lng, $lat);
var_dump($wtb);
$js = <<< EOF
    var gcj02 = coordtransform.wgs84togcj02($lng, $lat);
    console.log(coordtransform.gcj02tobd09(gcj02[0], gcj02[1]));
EOF;
script($js);

/**
 *
 */
$btg = Coordtransform::bd09ToGcj02($lng, $lat);
var_dump($btg);
$js = <<< EOF
    console.log(coordtransform.bd09togcj02($lng, $lat));
EOF;
script($js);

/**
 *
 */
$btw = Coordtransform::bd09ToWgs84($lng, $lat);
var_dump($btw);
$js = <<< EOF
    var gcj02 = coordtransform.bd09togcj02($lng, $lat);
    console.log(coordtransform.gcj02towgs84(gcj02[0], gcj02[1]));
EOF;
script($js);

function script($js) {
    echo "<script type='application/javascript'>$js</script>";
}

/**
以上代码执行输出（PHP7.4）：

array (size=2)
    0 => float 101.66758917038
    1 => float 34.446565679398

array (size=2)
    0 => float 101.6593935994
    1 => float 34.44201870302

array (size=2)
    0 => float 101.6628764006
    1 => float 34.43883929698

array (size=2)
    0 => float 101.66933760126
    1 => float 34.444950045745

array (size=2)
    0 => float 101.65471487632
    1 => float 34.434211121566

array (size=2)
    0 => float 101.65298716899
    1 => float 34.435816401314

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

(2) [101.66758917038334, 34.446565679397786]
(2) [101.65939359940022, 34.442018703020054]
(2) [101.66287640059979, 34.43883929697995]
(2) [101.66933760125637, 34.44495004574466]
(2) [101.65471487632013, 34.434211121566484]
(2) [101.65298716898793, 34.43581640131362]

 */