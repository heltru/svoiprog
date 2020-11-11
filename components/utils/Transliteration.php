<?php
namespace app\components\utils;

class Transliteration
{


    private $table = array(
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ё' => 'Yo', 'ё' => 'yo',
        'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Й' => 'J', 'й' => 'j',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => "N", 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'H', 'х' => 'h',
        'Ц' => 'C', 'ц' => 'c',
        'Ч' => 'Ch', 'ч' => 'ch',
        'Ш' => 'Sh', 'ш' => 'sh',
        'Щ' => 'Shch', 'щ' => 'shch',
        'Ъ' => 'ʺ', 'ъ' => 'ʺ',
        'Ы' => 'Y`', 'ы' => 'y`',
        'Ь' => '', 'ь' => '',
        'Э' => 'Eh', 'э' => 'eh',
        'Ю' => 'Yu', 'ю' => 'yu',
        'Я' => 'Ya', 'я' => 'ya',
        '№' => '#', 'Ӏ' => '‡',
        '’' => '`', 'ˮ' => '¨',
    );


	private function transl($str){
	    return 	$str =  str_replace(array_keys($this->table),
            array_values($this->table), $str);
    }


	public function transliterate($content)
	{

        $arr = preg_split('//u',$content,-1,PREG_SPLIT_NO_EMPTY);

		$res = '';
        for ( $i= 0 ; $i <= count($arr)-1; $i++){
            $let[  ] =  $this->transl($arr[$i]);
        }



        for ( $i= 0 ; $i <= count($let)-1; $i++){
            $w = $let[$i];
            if ( $i != 0 && $let[$i] == 'h' ||  $let[$i] == 'H'
                && isset($let[$i-1]) )
            {
                 if (strpos( $let[$i-1], 'c' ) !== false){
                     $w = 'kh';
                 } elseif (strpos( $let[$i-1], 's' ) !== false){
                     $w = 'kh';
                 }elseif (strpos( $let[$i-1], 'e' ) !== false){
                     $w = 'kh';
                 }elseif (strpos( $let[$i-1], 'h' ) !== false){
                     $w = 'kh';
                 }
            }
            $res .= $w;

        }
        return $res;

	}


}
