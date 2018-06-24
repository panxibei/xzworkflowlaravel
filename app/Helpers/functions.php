<?php
/**
 * 这里是公共common模块函数(PHP)调用库(2014/06/21)
 * 
 * 
 */

/**添加时间：201707171117（网上摘录）
 * 生成GUID
 * @param string $namespace 命名空间
 * 示例：
 * {E2DFFFB3-571E-6CFC-4B5C-9FEDAAF2EFD7}
 *
 */
function create_guid($namespace = '') {  
	static $guid = '';
	$uid = uniqid("", true);
	$data = $namespace;
	$data .= $_SERVER['REQUEST_TIME'];
	$data .= $_SERVER['HTTP_USER_AGENT'];
	$data .= $_SERVER['SERVER_ADDR'];
	$data .= $_SERVER['SERVER_PORT'];
	$data .= $_SERVER['REMOTE_ADDR'];
	$data .= $_SERVER['REMOTE_PORT'];
	$hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
	$guid = '{' . 
		substr($hash, 0, 8) .
		'-' .
		substr($hash, 8, 4) .
		'-' .
		substr($hash, 12, 4) .
		'-' .
		substr($hash, 16, 4) .
		'-' .
		substr($hash, 20, 12) .
		'}';
	return $guid;
}