<?php

/**
 * @link https://github.com/hightman/scws
 * @link http://www.xunsearch.com/scws/
 */

if (!extension_loaded('scws')) {
    return;
}

/**
 * 词典文件为 XDB
 */
defined('SCWS_XDICT_XDB') || define('SCWS_XDICT_XDB', 1);

/**
 * 将词典全部加载到内存里
 */
defined('SCWS_XDICT_MEM') || define('SCWS_XDICT_MEM', 2);

/**
 * 词典文件为 TXT（纯文本）
 */
defined('SCWS_XDICT_TXT') || define('SCWS_XDICT_TXT', 3);

/**
 * 不进行复合分词
 */
defined('SCWS_MULTI_NONE') || define('SCWS_MULTI_NONE', 0x00000);

/**
 * 短词复合
 */
defined('SCWS_MULTI_SHORT') || define('SCWS_MULTI_SHORT', 0x01000);

/**
 * 散字二元复合
 */
defined('SCWS_MULTI_DUALITY') || define('SCWS_MULTI_DUALITY', 0x02000);

/**
 * 重要单字
 */
defined('SCWS_MULTI_ZMAIN') || define('SCWS_MULTI_ZMAIN', 0x04000);

/**
 * 全部单字
 */
defined('SCWS_MULTI_ZALL') || define('SCWS_MULTI_ZALL', 0x08000);

if (!class_exists(SimpleCWS::class)) {
    /**
     * Scws中文分词
     * @desc 这是一个类似 Directory 的内置式伪类操作，类方法建立请使用 scws_new() 函数，而不能直接用 new SimpleCWS。 否则不会包含有 handle 指针，将无法正确操作
     * @author mosquito <zwj1206_hi@163.com>
     */
    class SimpleCWS
    {
        /**
         * @var resource
         * @author mosquito <zwj1206_hi@163.com>
         */
        private $handle;

        /**
         * 关闭一个已打开的 scws 分词操作句柄
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function close()
        {}

        /**
         * 设定分词词典、规则集、欲分文本字符串的字符集
         * @param string $charset 要新设定的字符集，目前只支持 utf8 和 gbk
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function set_charset(string $charset)
        {}

        /**
         * 添加分词所用的词典，新加入的优先查找
         * @param string $dict_path 词典的路径，可以是相对路径或完全路径
         * @param int $mode 可选，表示加载的方式（SCWS_XDICT_TXT、SCWS_XDICT_XDB、SCWS_XDICT_MEM）
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function add_dict(string $dict_path, ?int $mode = SCWS_XDICT_XDB)
        {}

        /**
         * 设定分词所用的词典并清除已存在的词典列表
         * @param string $dict_path 词典的路径，可以是相对路径或完全路径
         * @param int $mode 可选，表示加载的方式（SCWS_XDICT_TXT、SCWS_XDICT_XDB、SCWS_XDICT_MEM）
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function set_dict(string $dict_path, ?int $mode = SCWS_XDICT_XDB)
        {}

        /**
         * 设定分词所用的新词识别规则集（用于人名、地名、数字时间年代等识别）
         * @param string $rule_path 规则集的路径，可以是相对路径或完全路径
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function set_rule(string $rule_path)
        {}

        /**
         * 设定分词返回结果时是否去除一些特殊的标点符号之类
         * @param bool $yes 设定值，如果为 true 则结果中不返回标点符号，如果为 false 则会返回，缺省为 false
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function set_ignore(bool $yes)
        {}

        /**
         * 设定分词返回结果时是否复式分割，如“中国人”返回“中国＋人＋中国人”三个词
         * @param int $mode 复合分词法的级别，缺省不复合分词（SCWS_MULTI_SHORT、SCWS_MULTI_DUALITY、SCWS_MULTI_ZMAIN、SCWS_MULTI_ZALL）
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function set_multi(int $mode)
        {}

        /**
         * 设定是否将闲散文字自动以二字分词法聚合
         * @param bool $yes 设定值，如果为 true 则结果中多个单字会自动按二分法聚分，如果为 false 则不处理，缺省为 false
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function set_duality(bool $yes)
        {}

        /**
         * 发送设定分词所要切割的文本
         * @param string $text 要切分的文本的内容
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function send_text(string $text)
        {}

        /**
         * 根据 send_text 设定的文本内容，返回一系列切好的词汇（本函数应该循环调用，直到返回 false 为止）
         * @return array|false
         * word string 词本身
         * idf float 逆文本词频
         * off int 该词在原文本路的位置
         * attr string 词性
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function get_result()
        {}

        /**
         * 根据 send_text 设定的文本内容，返回系统计算出来的最关键词汇列表
         * @param int $limit 可选参数，返回的词的最大数量，缺省是 10
         * @param string $xattr 可选参数，是一系列词性组成的字符串，各词性之间以半角的逗号隔开， 这表示返回的词性必须在列表中，如果以~开头，则表示取反，词性必须不在列表中，缺省为NULL，返回全部词性，不过滤
         * @return array|false
         * word string 词本身
         * times int 词在文本中出现的次数
         * weight float 该词计算后的权重
         * attr string 词性
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function get_tops(?int $limit, ?string $xattr)
        {}

        /**
         * 根据 send_text 设定的文本内容，返回系统中词性符合要求的关键词汇
         * @param string $xattr 是一系列词性组成的字符串，各词性之间以半角的逗号隔开， 这表示返回的词性必须在列表中，如果以~开头，则表示取反，词性必须不在列表中，若为空则返回全部词
         * @return array|false
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function has_word(string $xattr)
        {}

        /**
         * 根据 send_text 设定的文本内容，返回系统中是否包括符合词性要求的关键词
         * @param string $xattr 是一系列词性组成的字符串，各词性之间以半角的逗号隔开， 这表示返回的词性必须在列表中，如果以~开头，则表示取反，词性必须不在列表中，若为空则返回全部词
         * @return bool
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function get_words(string $xattr)
        {}

        /**
         * 返回 scws 版本号名称信息
         * @return string
         * @author mosquito <zwj1206_hi@163.com>
         */
        public function version()
        {}
    }
}

if (!function_exists('scws_new')) {
    /**
     * 创建并返回一个 SimpleCWS 类操作对象
     * @return SimpleCWS
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_new()
    {}
}

if (!function_exists('scws_open')) {
    /**
     * 创建并返回一个分词操作句柄
     * @return resource
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_open()
    {}
}

if (!function_exists('scws_close')) {
    /**
     * 关闭一个已打开的 scws 分词操作句柄
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_close(resource $scws_handle)
    {}
}

if (!function_exists('scws_set_charset')) {
    /**
     * 设定分词词典、规则集、欲分文本字符串的字符集
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param string $charset 要新设定的字符集，目前只支持 utf8 和 gbk
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_set_charset(resource $scws_handle, string $charset)
    {}
}

if (!function_exists('scws_add_dict')) {
    /**
     * 添加分词所用的词典，新加入的优先查找
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param string $dict_path 词典的路径，可以是相对路径或完全路径
     * @param int $mode 可选，表示加载的方式（SCWS_XDICT_TXT、SCWS_XDICT_XDB、SCWS_XDICT_MEM）
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_add_dict(resource $scws_handle, string $dict_path, ?int $mode = SCWS_XDICT_XDB)
    {}
}

if (!function_exists('scws_set_dict')) {
    /**
     * 设定分词所用的词典并清除已存在的词典列表
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param string $dict_path 词典的路径，可以是相对路径或完全路径
     * @param int $mode 可选，表示加载的方式（SCWS_XDICT_TXT、SCWS_XDICT_XDB、SCWS_XDICT_MEM）
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_set_dict(resource $scws_handle, string $dict_path, ?int $mode = SCWS_XDICT_XDB)
    {}
}

if (!function_exists('scws_set_rule')) {
    /**
     * 设定分词所用的新词识别规则集（用于人名、地名、数字时间年代等识别）
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param string $rule_path 规则集的路径，可以是相对路径或完全路径
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_set_rule(resource $scws_handle, string $rule_path)
    {}
}

if (!function_exists('scws_set_ignore')) {
    /**
     * 设定分词返回结果时是否去除一些特殊的标点符号之类
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param bool $yes 设定值，如果为 true 则结果中不返回标点符号，如果为 false 则会返回，缺省为 false
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_set_ignore(resource $scws_handle, bool $yes)
    {}
}

if (!function_exists('scws_set_multi')) {
    /**
     * 设定分词返回结果时是否复式分割，如“中国人”返回“中国＋人＋中国人”三个词
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param int $mode 复合分词法的级别，缺省不复合分词（SCWS_MULTI_SHORT、SCWS_MULTI_DUALITY、SCWS_MULTI_ZMAIN、SCWS_MULTI_ZALL）
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_set_multi(resource $scws_handle, int $mode)
    {}
}

if (!function_exists('scws_set_duality')) {
    /**
     * 设定是否将闲散文字自动以二字分词法聚合
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param bool $yes 设定值，如果为 true 则结果中多个单字会自动按二分法聚分，如果为 false 则不处理，缺省为 false
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_set_duality(resource $scws_handle, bool $yes)
    {}
}

if (!function_exists('scws_send_text')) {
    /**
     * 发送设定分词所要切割的文本
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param string $text 要切分的文本的内容
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_send_text(resource $scws_handle, string $text)
    {}
}

if (!function_exists('scws_get_result')) {
    /**
     * 根据 send_text 设定的文本内容，返回一系列切好的词汇（本函数应该循环调用，直到返回 false 为止）
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @return array|false
     * word string 词本身
     * idf float 逆文本词频
     * off int 该词在原文本路的位置
     * attr string 词性
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_get_result(resource $scws_handle)
    {}
}

if (!function_exists('scws_get_tops')) {
    /**
     * 根据 send_text 设定的文本内容，返回系统计算出来的最关键词汇列表
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param int $limit 可选参数，返回的词的最大数量，缺省是 10
     * @param string $xattr 可选参数，是一系列词性组成的字符串，各词性之间以半角的逗号隔开， 这表示返回的词性必须在列表中，如果以~开头，则表示取反，词性必须不在列表中，缺省为NULL，返回全部词性，不过滤
     * @return array|false
     * word string 词本身
     * times int 词在文本中出现的次数
     * weight float 该词计算后的权重
     * attr string 词性
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_get_tops(resource $scws_handle, ?int $limit, ?string $xattr)
    {}
}

if (!function_exists('scws_has_word')) {
    /**
     * 根据 send_text 设定的文本内容，返回系统中词性符合要求的关键词汇
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param string $xattr 是一系列词性组成的字符串，各词性之间以半角的逗号隔开， 这表示返回的词性必须在列表中，如果以~开头，则表示取反，词性必须不在列表中，若为空则返回全部词
     * @return array|false
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_has_word(resource $scws_handle, string $xattr)
    {}
}

if (!function_exists('scws_get_words')) {
    /**
     * 根据 send_text 设定的文本内容，返回系统中是否包括符合词性要求的关键词
     * @param resource $scws_handle 由 scws_open 打开的返回值
     * @param string $xattr 是一系列词性组成的字符串，各词性之间以半角的逗号隔开， 这表示返回的词性必须在列表中，如果以~开头，则表示取反，词性必须不在列表中，若为空则返回全部词
     * @return bool
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_get_words(resource $scws_handle, string $xattr)
    {}
}

if (!function_exists('scws_version')) {
    /**
     * 返回 scws 版本号名称信息
     * @return string
     * @author mosquito <zwj1206_hi@163.com>
     */
    function scws_version()
    {}
}
