<?php
    class Gitwiki extends Modules {
        public function __init() {}

        static function __install() {}

        static function __uninstall($confirm) {}

        public function extend_nav($navs) {
            if (Visitor::current()->group->can("toggle_extensions"))
                $navs["gitwiki"] = array("title" => __("Documentation", "gitwiki"));

            return $navs;
        }
        static function admin_context($context) {
            if($_GET['action']=='gitwiki'){
                if(isset($_GET['url'])){
                    $c = curl_init('https://github.com/chyrp/chyrp/wiki/'.$_GET['url']);
                    curl_setopt($c,  CURLOPT_RETURNTRANSFER, TRUE);
                    $html = curl_exec($c);
                    $httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
                    curl_close($c);
                    if($httpCode == 302||$httpCode == 404) {
                        //they want us to create a new page or its not found
                        $context['html']='Page not found. <a href="?action=gitwiki">Back to documentation home page.</a>';
                        return $context;
                    }
                    $context['html']=self::process($html);
                }else{
                    $c = curl_init('https://github.com/chyrp/chyrp/wiki');
                    curl_setopt($c,  CURLOPT_RETURNTRANSFER, TRUE);
                    $html = curl_exec($c);
                    $httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
                    curl_close($c);
                    $context['html']=self::process($html);
                }
            }
            return $context;
        }
        function process($html){
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $html= self::get_inner_html($doc->getElementById("wiki-wrapper"));
            $html=preg_replace('/href="\/chyrp\/chyrp\/wiki\/(.*)"/', "href='?action=gitwiki&url=$1'", $html);
            return $html;
        }

        function get_inner_html( $node ) {//from http://www.php.net/manual/en/class.domelement.php#101243
            $innerHTML= '';
            $children = $node->childNodes;
            foreach ($children as $child) {
                $innerHTML .= $child->ownerDocument->saveXML( $child );
            }       
            return $innerHTML;
        } 
    }
