<?php
namespace app\modules\test\app;
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 16.02.18
 * Time: 16:05
 */
class SiteError
{
    private $sites = [
        [
            'host'=>'https://gradushaus.ru',
            'sitemap'=>'https://gradushaus.ru/sitemap.xml',
            'loadLinks'=>[
                'all'=>[],
                'err'=>[]
            ],
            'nocheckurl'=>[],
            'static'=>[
                'count200'=>0,
                'countErr'=>0,
            ]
        ],
        [
            'host'=>'https://kirov.gradushaus.ru',
            'sitemap'=>'https://kirov.gradushaus.ru/sitemap.xml',
            'loadLinks'=>[
                'all'=>[],
                'err'=>[]
            ],
            'nocheckurl'=>[],
            'static'=>[
                'count200'=>0,
                'countErr'=>0,
            ]
        ],
    ];

    public function actionSitemapNot200(){

         set_time_limit(300);
        $this->getLoadSiteMap();

    }

    public function resultTextFormat(){
        $str = '';
        foreach ( $this->sites as $site){
            $str .= 'ℹ ' . $site['host'] . PHP_EOL;
            //$str .= '*⚠' . $site['static']['countErr'] . '*' . PHP_EOL ;
            if (count($site['loadLinks']['err'])){
                foreach ($site['loadLinks']['err'] as $err){
                    $str .= '⚠ * ' . $err['code'] .  '*'. PHP_EOL;
                      $str .=  '['.$err['url']  .']' . '('.$err['url'].')'. PHP_EOL;
                }
            }
            $str .= '✅ *' . ($site['static']['countErr'] + $site['static']['count200'] ). '*'. PHP_EOL ;

            if (count($site['nocheckurl'])){
                foreach ($site['nocheckurl'] as $nocheck){
                    $str .= '❓'. PHP_EOL;
                    $str .=  '['.$nocheck .']' . '('.$nocheck.')'. PHP_EOL;

                }
            }
            $str .= PHP_EOL . PHP_EOL;


        }
        return $str;
    }

    private function getLoadSiteMap(){
        foreach ( $this->sites as $num => $item){

            $siteMap = $this->url_load( $item['sitemap'] ); // load sitemap.xml
            if ( ! $siteMap['respond'] ) {
                continue;
            }

            $xml = new \SimpleXMLElement($siteMap['respond']);

            foreach ($xml->url as $url_list) {
                $url = (string)$url_list->loc;
                if ( (boolean) ($url && $this->validUrl($url)) ) {

                    $raw = $this->url_load($url);

                    $rec = [
                        'url'=>$url,
                        'code'=>$raw['http_code']
                    ];

                    if ($raw['http_code'] == "200" ||
                        $raw['http_code'] == "301" ||
                        $raw['http_code'] == "302"
                    ){ // 200
                        $this->sites[$num]['static']['count200'] += 1;

                    } else { // err
                        $this->sites[$num]['static']['countErr'] += 1;
                        $this->sites[$num]['loadLinks']['err'][] = $rec;
                    }

                    $this->sites[$num]['loadLinks']['all'][] = $rec;


                    $this->sites[$num]['loadLinks'][] = $rec;
                } else {
                    $this->sites[$num]['nocheckurl'][] = $url;
                }
            }
        }

    }

    private function validUrl($url){
        return (  ! filter_var($url, FILTER_VALIDATE_URL) === FALSE) ;
    }

    private function url_load($url=''){
        $timeout = 10;
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
        $http_respond = curl_exec($ch);
        //$http_respond = trim( strip_tags( $http_respond ) );
        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        /*if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
            return true;
        } else {
            // return $http_code;, possible too
            return false;
        }*/
        curl_close( $ch );

        return [
            'respond'=>$http_respond,
            'http_code'=>$http_code
        ];
    }


}