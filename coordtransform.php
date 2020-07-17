<?php

/**
 * Class Rectangle
 */
class Rectangle {

    private $_west;
    private $_north;
    private $_east;
    private $_south;

    /**
     * China region - raw data
     * @var null
     */
    private static $_region = null;

    /**
     * China excluded region - raw data
     * @var null
     */
    private static $_exclude = null;

    /**
     * Rectangle constructor.
     * @param float $lng1
     * @param float $lat1
     * @param float $lng2
     * @param float $lat2
     */
    public function __construct($lng1, $lat1, $lng2, $lat2)
    {
        $this->_west  = min($lng1, $lng2);
        $this->_north = max($lat1, $lat2);
        $this->_east  = max($lng1, $lng2);
        $this->_south = min($lat1, $lat2);
    }

    /**
     * @param float $lon
     * @param float $lat
     * @return bool
     */
    public function contain($lon, $lat)
    {
        return $this->_west <= $lon && $this->_east >= $lon && $this->_north >= $lat && $this->_south <= $lat;
    }

    /**
     * initData
     */
    private static function initData()
    {
        if (!self::$_region) {
            self::$_region = [];
            self::$_region[] = new self(79.446200, 49.220400, 96.330000, 42.889900);
            self::$_region[] = new self(109.687200, 54.141500, 135.000200, 39.374200);
            self::$_region[] = new self(73.124600, 42.889900, 124.143255, 29.529700);
            self::$_region[] = new self(82.968400, 29.529700, 97.035200, 26.718600);
            self::$_region[] = new self(97.025300, 29.529700, 124.367395, 20.414096);
            self::$_region[] = new self(107.975793, 20.414096, 111.744104, 17.871542);
        }

        if (!self::$_exclude) {
            self::$_exclude = [];
            self::$_exclude[] = new self(119.921265, 25.398623, 122.497559, 21.785006);
            self::$_exclude[] = new self(101.865200, 22.284000, 106.665000, 20.098800);
            self::$_exclude[] = new self(106.452500, 21.542200, 108.051000, 20.487800);
            self::$_exclude[] = new self(109.032300, 55.817500, 119.127000, 50.325700);
            self::$_exclude[] = new self(127.456800, 55.817500, 137.022700, 49.557400);
            self::$_exclude[] = new self(131.266200, 44.892200, 137.022700, 42.569200);
            self::$_exclude[] = new self(73.124600, 35.398637, 77.948114, 29.529700);
        }
    }

    /**
     * 判断是否是国内
     * @param float $lon
     * @param float $lat
     * @return bool true: 国内 | false: 国外
     */
    public static function isInChina($lon, $lat)
    {
        self::initData();
        foreach (self::$_region as $region) {
            if ($region->contain($lon, $lat)) {
                foreach (self::$_exclude as $exclude) {
                    if ($exclude->contain($lon, $lat)) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }
}

/**
 * Class Coordtransform
 */
class Coordtransform {

    private static $_x_PI = 3.14159265358979324 * 3000.0 / 180.0;
    private static $_PI   = 3.1415926535897932384626;
    private static $_a    = 6378245.0;
    private static $_ee   = 0.00669342162296594323;

    /**
     * 百度坐标系 (BD-09) 与 火星坐标系 (GCJ-02)的转换
     * 即 百度 转 谷歌、高德
     * @param float $lng
     * @param float $lat
     * @return  array
     */
    public static function bd09ToGcj02($lng, $lat)
    {
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::$_x_PI);
        $theta = atan2($y, $x) - 0.000003 * cos($x * self::$_x_PI);
        $gg_lng = $z * cos($theta);
        $gg_lat = $z * sin($theta);
        return [$gg_lng, $gg_lat];
    }

    /**
     * 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换
     * 即谷歌、高德 转 百度
     * @param float $lng
     * @param float $lat
     * @return array
     */
    public static function gcj02ToBd09($lng, $lat) {
        $z = sqrt($lng * $lng + $lat * $lat) + 0.00002 * sin($lat * self::$_x_PI);
        $theta = atan2($lat, $lng) + 0.000003 * cos($lng * self::$_x_PI);
        $bd_lng = $z * cos($theta) + 0.0065;
        $bd_lat = $z * sin($theta) + 0.006;
        return [$bd_lng, $bd_lat];
    }

    /**
     * WGS84 转换为 GCj02
     * @param float $lng
     * @param float $lat
     * @return array
     */
    public static function wgs84ToGcj02($lng, $lat)
    {
        if (!Rectangle::isInChina($lng, $lat)) {
            return [$lng, $lat];
        }
        return self::transform($lng, $lat);
    }

    /**
     * GCJ02 转换为 WGS84
     * @param float $lng
     * @param float $lat
     * @return array
     */
    public static function gcj02ToWgs84($lng, $lat)
    {
        if (!Rectangle::isInChina($lng, $lat)) {
            return [$lng, $lat];
        }
        $out = self::transform($lng, $lat);
        return [$lng * 2 - $out[0], $lat * 2 - $out[1]];
    }

    /**
     * BD09 转换为 WGS84
     * @param float $lng
     * @param float $lat
     * @return array
     */
    public static function bd09ToWgs84($lng, $lat)
    {
        if (!Rectangle::isInChina($lng, $lat)) {
            return [$lng, $lat];
        }
        $gcj02 = self::bd09ToGcj02($lng, $lat);
        return self::gcj02towgs84($gcj02[0], $gcj02[1]);
    }

    /**
     * WGS84 转换为 BD09
     * @param float $lng
     * @param float $lat
     * @return array
     */
    public static function wgs84ToBd09($lng, $lat)
    {
        if (!Rectangle::isInChina($lng, $lat)) {
            return [$lng, $lat];
        }
        $gcj02 = self::wgs84ToGcj02($lng, $lat);
        return self::gcj02ToBd09($gcj02[0], $gcj02[1]);
    }

    /**
     * @param float $lng
     * @param float $lat
     * @return float[]|int[]
     */
    private static function transform($lng, $lat)
    {
        $dlat   = self::transformlat($lng - 105.0, $lat - 35.0);
        $dlng   = self::transformlng($lng - 105.0, $lat - 35.0);
        $radlat = $lat / 180.0 * self::$_PI;
        $magic  = sin($radlat);
        $magic  = 1 - self::$_ee * $magic * $magic;
        $sqrtmagic = sqrt($magic);
        $dlat  = ($dlat * 180.0) / ((self::$_a * (1 - self::$_ee)) / ($magic * $sqrtmagic) * self::$_PI);
        $dlng  = ($dlng * 180.0) / (self::$_a / $sqrtmagic * cos($radlat) * self::$_PI);
        $mglat = $lat + $dlat;
        $mglng = $lng + $dlng;
        return [$mglng, $mglat];
    }

    /**
     * @param float $lng
     * @param float $lat
     * @return float|int
     */
    private static function transformlat($lng, $lat)
    {
        $ret = -100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * self::$_PI) + 20.0 * sin(2.0 * $lng * self::$_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lat * self::$_PI) + 40.0 * sin($lat / 3.0 * self::$_PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($lat / 12.0 * self::$_PI) + 320 * sin($lat * self::$_PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }

    /**
     * @param float $lng
     * @param float $lat
     * @return float|int
     */
    private static function transformlng($lng, $lat)
    {
        $ret = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * self::$_PI) + 20.0 * sin(2.0 * $lng * self::$_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lng * self::$_PI) + 40.0 * sin($lng / 3.0 * self::$_PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($lng / 12.0 * self::$_PI) + 300.0 * sin($lng / 30.0 * self::$_PI)) * 2.0 / 3.0;
        return $ret;
    }
}