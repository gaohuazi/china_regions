<?php

class Region {
	public $config = [
		'debug'   => true, //输出调试信息
		'logPath' => './error.log', //错误日志
		'outPath' => '', //输出目录
	];

	private $province_url = 'https://misc.360buyimg.com/jdf/1.0.0/ui/area/1.0.0/area.js';
	private $children_url = 'https://fts.jd.com/area/get?fid=';

	private $province_arr = [];
	private $city_arr     = [];
	private $area_arr     = [];
	private $street_arr   = [];

	public function __construct($config = []) {
		$this->config['outPath'] = dirname(__FILE__) . '/../../';
		if ($config) {
			$this->config = array_merge($this->config, $config);
		}
	}

	/**
	 * [run 采集入口方法]
	 * @Author   gaohuazi
	 * @return   [type]                   [description]
	 */
	public function run() {
		$start = microtime(true);
		$this->_clearLog();
		$this->_initData();
		$end = microtime(true);
		$this->_debug('耗时' . intval($end - $start) . '秒');

		$this->_toJson();
		$this->_toSql();
	}

	/**
	 * [_toJson 生成json文件]
	 * @Author   gaohuazi

	 * @return   [type]                   [description]
	 */
	private function _toJson() {
		$path = $this->config['outPath'] . '/json/';
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
		}
		file_put_contents($path . 'province.json', json_encode($this->province_arr, 320));
		file_put_contents($path . 'city.json', json_encode($this->city_arr, 320));
		file_put_contents($path . 'area.json', json_encode($this->area_arr, 320));
		file_put_contents($path . 'street.json', json_encode($this->street_arr, 320));

		$this->_debug("json文件已写入到" . realpath($path) . "目录下");
	}

	/**
	 * [_toSql 生成sql文件]
	 * @Author   gaohuazi

	 * @return   [type]                   [description]
	 */
	private function _toSql() {
		$path = $this->config['outPath'] . '/sql/';
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
		}

		file_put_contents($path . 'init.sql', $this->_getSqlDesc());
		file_put_contents($path . 'province.sql', $this->_getProvinceSql());
		file_put_contents($path . 'city.sql', $this->_getRegionSql('city'));
		file_put_contents($path . 'area.sql', $this->_getRegionSql('area'));
		file_put_contents($path . 'street.sql', $this->_getRegionSql('street'));

		$this->_debug("sql文件已写入到" . realpath($path) . "目录下");
	}

	/**
	 * [_getSqlDesc 获取mysqll表结构sql]
	 * @Author   gaohuazi
	 * @return   [type]                   [description]
	 */
	private function _getSqlDesc() {

		$sql = <<<SQL
    -- 省、市、区、街道，四张表
    DROP TABLE IF EXISTS `province`;
    CREATE TABLE IF NOT EXISTS `province` (
      `id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(30) NOT NULL DEFAULT '' COMMENT '省份名称',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='省份表';

    DROP TABLE IF EXISTS `city`;
    CREATE TABLE IF NOT EXISTS `city` (
      `id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(60) NOT NULL DEFAULT '' COMMENT '城市名称',
      `pid` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省份id',
      PRIMARY KEY (`id`),
      KEY `pid` (`pid`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='城市表';

    DROP TABLE IF EXISTS `area`;
    CREATE TABLE IF NOT EXISTS `area` (
      `id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(60) NOT NULL DEFAULT '' COMMENT '区域名称',
      `pid` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市id',
      PRIMARY KEY (`id`),
      KEY `pid` (`pid`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='区域表';

    DROP TABLE IF EXISTS `street`;
    CREATE TABLE IF NOT EXISTS `street` (
      `id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(60) NOT NULL DEFAULT '' COMMENT '街道名称',
      `pid` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域id',
      PRIMARY KEY (`id`),
      KEY `pid` (`pid`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='街道表';
SQL;
		return $sql;
	}

	/**
	 * [_getProvinceSql 获取省sql]
	 * @Author   gaohuazi
	 * @return   [type]                   [description]
	 */
	private function _getProvinceSql() {
		$sql = 'INSERT INTO `province` (`id`, `name`) VALUES ';
		foreach ($this->province_arr as $v) {
			$sql .= "\n({$v['id']}, '{$v['name']}'),";
		}

		$sql = rtrim($sql, ',') . ";";
		return $sql;
	}

	/**
	 * [_getRegionSql 获取市区街道sql]
	 * @Author   gaohuazi
	 * @DateTime 2022-02-08T12:42:05+0800
	 * @param    string                   $type ['city', 'area', 'street'其中一个]
	 * @return   [type]                         [description]
	 */
	private function _getRegionSql($type = 'city') {
		if (!in_array($type, ['city', 'area', 'street'])) {
			return '';
		}
		$sql      = "INSERT INTO `{$type}` (`id`, `name`,`pid`) VALUES ";
		$arr_name = "{$type}_arr";
		foreach ($this->$arr_name as $pid => $childArr) {
			foreach ($childArr as $v) {
				$sql .= "\n({$v['id']}, '{$v['name']}',$pid),";
			}
		}

		$sql = rtrim($sql, ',') . ";";
		return $sql;
	}

	/**
	 * [_initData 初始化数据]
	 * @Author   gaohuazi

	 * @return   [type]                   [description]
	 */
	private function _initData() {
		set_time_limit(0);

		$html = $this->_request('GET', $this->province_url);
		if (!$html) {
			$this->_debug('获取html异常');
			return;
		}

		// 1.获取省数据
		preg_match('/a\.each\(\"(.*?)\"/mi', $html, $matches);
		$provinceStr = $this->_unicodeDecode($matches[1]);

		$this->province_arr = array_map(function ($item) {
			list($name, $id) = explode('|', $item);
			return ['id' => intval($id), 'name' => $name];
		}, explode(',', $provinceStr));

		// 2.获取市数据
		preg_match('/city:.*?a\.each\((\{.*?\})/mi', $html, $matches);

		$cityStr       = $this->_unicodeDecode($matches[1]);
		$cityStr       = preg_replace('/(\d+)\:/', '"$1":', $cityStr);
		$temp_city_arr = json_decode($cityStr, true);

		foreach ($temp_city_arr as $pid => $childStr) {
			$childArr = [];
			$tempArr  = explode(',', $childStr);

			$areaUrls = [];
			foreach ($tempArr as $item) {
				list($name, $id) = explode('|', $item);
				$childArr[]      = ['id' => intval($id), 'name' => $name];

				$this->_debug('市：' . $name);
				$areaUrls[] = [
					'url' => $this->children_url . $id,
					'pid' => $id,
				];
			}

			// 3.获取区数据
			$areaDataList = $this->_multiRequest($areaUrls);
			foreach ($areaDataList as $cityId => $area_str) {
				if ($area_str) {
					$temp_area_arr = json_decode($area_str, true);
					if ($temp_area_arr) {
						$this->area_arr[$cityId] = $temp_area_arr;

						// 4.获取街道数据
						$streetUrls = [];
						foreach ($temp_area_arr as $streetItem) {
							$this->_debug('区：' . $streetItem['name']);

							$streetUrls[] = [
								'url' => $this->children_url . $streetItem['id'],
								'pid' => $streetItem['id'],
							];
						}

						$streetDataList = $this->_multiRequest($streetUrls);
						foreach ($streetDataList as $areaId => $street_str) {
							if ($street_str) {
								$temp_street_arr = json_decode($street_str, true);
								if ($temp_street_arr) {
									$this->street_arr[$areaId] = $temp_street_arr;
								}
							} else {
								$this->_writeLog("获取数据失败：areaId={$areaId}");
							}
						}
					}
				} else {
					$this->_writeLog("获取数据失败：cityId={$cityId}");
				}
			}

			$this->city_arr[$pid] = $childArr;
		}
	}

	/**
	 * [request 发送restful 请求]
	 * @Author   gaohuazi
	 * @param    string                   $method        [description]
	 * @param    string                   $url           [description]
	 * @param    array                    $data          [description]
	 * @param    array                    $ext_header    [description]
	 * @param    boolean                  $return_header [description]
	 * @return   [type]                                  [description]
	 */
	private function _request($method = 'POST', $url = '', $data = [], $ext_header = [], $return_header = false) {
		// set_time_limit(0);
		$method = strtoupper($method);
		$header = [
			// 'Content-Length:' . strlen(http_build_query($data)),
			// 'Content-Type: application/json;charset=UTF-8',
			// 'X-Requested-With: XMLHttpRequest',
			// 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
		];
		if ($ext_header) {
			$header = array_merge($header, $ext_header);
		}

		$ch = curl_init();

		switch ($method) {
		case 'GET':
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			if (is_array($data)) {
				$url .= '?' . http_build_query($data);
			}

			break;
		case 'POST':
			if (is_array($data)) {
				$data = http_build_query($data);
			}
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			break;
		case 'PUT':
		case 'DELETE':
			if (is_array($data)) {
				$data = http_build_query($data);
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			break;
		}
		curl_setopt($ch, CURLOPT_ENCODING, ''); //请求时自动加上请求头Accept-Encoding，并且返回内容会自动解压，不会乱码
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //超时时间
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_HEADER, $return_header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($ch);
		$errno  = curl_errno($ch);
		$error  = curl_error($ch);

		$i = 3;
		while ($i) {
			if (!$output) {
				$this->_writeLog(sprintf("url:%s,errno:%s,error:%s\noutput:%s", $url, $errno, $error, $output));
				// timeout 超时重试3次
				if ($errno == 28) {
					sleep(5);
				} else {
					break;
				}
			} else {
				break;
			}
			$i--;
		}

		curl_close($ch);

		if ($return_header) {
			return $output;
		}

		return $output;
	}

	/**
	 * [_multiRequest 并行发送GET请求]
	 * @Author   gaohuazi
	 * @param    [array]                   $urls [description]
	 * @return   [array]                         [description]
	 */
	private function _multiRequest($urls) {
		$mh          = curl_multi_init();
		$urlHandlers = [];
		$urlData     = [];
		// 初始化多个请求句柄为一个
		foreach ($urls as $v) {
			$ch  = curl_init();
			$url = $v['url'];
			if (isset($v['data'])) {
				$url .= strpos($url, '?') ? '&' : '?';
				$data = $v['data'];
				$url .= is_array($data) ? http_build_query($data) : $data;
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_SSLVERSION, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30); //超时时间

			if (isset($v['pid'])) {
				$urlHandlers[$v['pid']] = $ch;
			} else {
				$urlHandlers[] = $ch;
			}

			curl_multi_add_handle($mh, $ch);
		}
		$active = null;
		// 检测操作的初始状态是否OK，CURLM_CALL_MULTI_PERFORM为常量值-1
		do {
			// 返回的$active是活跃连接的数量，$mrc是返回值，正常为0，异常为-1
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		// 如果还有活动的请求，同时操作状态OK，CURLM_OK为常量值0
		while ($active && $mrc == CURLM_OK) {
			// 持续查询状态并不利于处理任务，每50ms检查一次，此时释放CPU，降低机器负载
			usleep(50000);
			// 如果批处理句柄OK，重复检查操作状态直至OK。select返回值异常时为-1，正常为1（因为只有1个批处理句柄）
			if (curl_multi_select($mh) != -1) {
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		foreach ($urlHandlers as $index => $ch) {
			$urlData[$index] = curl_multi_getcontent($ch);
			curl_multi_remove_handle($mh, $ch);
		}
		curl_multi_close($mh);
		return $urlData;
	}

	/**
	 * [_unicodeDecode unicode解码]
	 * @Author   gaohuazi
	 * @param    [string]                   $unicode_str [description]
	 * @return   [string]                                [description]
	 */
	private function _unicodeDecode($unicode_str) {
		$unicode_str = str_replace('"', '\"', $unicode_str);
		$json        = '{"str":"' . $unicode_str . '"}';
		$arr         = json_decode($json, true);
		if (empty($arr)) {
			return '';
		}

		return $arr['str'];
	}

	/**
	 * [debug 输出调试信息]
	 * @Author   gaohuazi
	 * @param    [type]                   $msg [description]
	 * @return   [type]                        [description]
	 */
	private function _debug($msg) {
		if ($this->config['debug']) {
			echo "{$msg}...\n";
		}
	}

	/**
	 * [_writeLog 记录信息到日志文件]
	 * @Author   gaohuazi
	 * @param    [type]                   $text [description]
	 * @return   [type]                         [description]
	 */
	private function _writeLog($text) {
		$logPath = dirname($this->config['logPath']);
		if (!file_exists($logPath)) {
			mkdir($logPath, 0755, true);
		}
		file_put_contents($this->config['logPath'], "{$text}\n", FILE_APPEND);
	}

	/**
	 * [_clearLog 清空日志文件]
	 * @Author   gaohuazi
	 * @return   [type]                   [description]
	 */
	private function _clearLog() {
		$this->_debug('清理日志');
		file_put_contents($this->config['logPath'], "");
	}
}