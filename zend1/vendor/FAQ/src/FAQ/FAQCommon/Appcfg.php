<?php
namespace FAQ\FAQCommon;
/**
 *
 * @author izzi
 *
 */
class Appcfg {

    //TODO: you must set debug is false when run product mode.
    // false,0 - not out message
    // 1 - console controller info, console Exception
    // 2 - var_dump controller info, console Exception
    // 3 - var_dump simple message
    // 4 - var_dump complex message
    public static $debug = 1;
    public static $qapolo_domain  = "123hoidap.vn";
    public static $domain = "http://123hoidap.vn";

    public static  $question_paging_size = 16;



    public static $img_avatar_size = 1000000;

    public static $img_media_size = 30000000;

}
?>